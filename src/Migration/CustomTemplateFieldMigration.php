<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\ModuleModel;
use Doctrine\DBAL\Connection;


class CustomTemplateFieldMigration extends AbstractMigration {


    /**
     * @var Doctrine\DBAL\Connection
     */
    private $connection;

    private $types = ['storelocator_details', 'storelocator_filter', 'storelocator_list', 'storelocator_search', 'storelocator_static_map'];


    public function __construct( Connection $connection ) {

        $this->connection = $connection;
    }


    public function shouldRun(): bool {

        $schemaManager = $this->connection->createSchemaManager();

        $t = ModuleModel::getTable();
        $tplField = 'customTpl';

        if( !$schemaManager->tablesExist([$t]) ) {
            return false;
        }

        // check if field already exists
        $columns = $schemaManager->listTableColumns($t);

        $hasFieldWithValue = false;

        foreach( $this->types as $type ) {

            $field = $type . '_tpl';

            if( !array_key_exists($field, $columns) ) {
                continue;
            }

            $count = $this->connection->executeQuery(
                "SELECT count(1) FROM $t WHERE type=:type AND $tplField!=$field"
            ,   ['type'=>$type]
            )->fetchOne();

            if( intval($count) > 0 ) {
                return true;
            }
        }

        return false;
    }


    public function run(): MigrationResult {

        $t = ModuleModel::getTable();
        $tplField = 'customTpl';

        foreach( $this->types as $type ) {

            $field = $type . '_tpl';
            $templateDefault = 'mod_' . $type;

            // copy value from x_tpl field to customTpl
            $this->connection->executeStatement(
                "UPDATE $t SET $tplField=$field WHERE type=:type AND $tplField!=$field"
            ,   ['type'=>$type]
            );

            // set default value to empty string
            $this->connection->executeStatement(
                "UPDATE $t SET $tplField=:empty, $field=:empty WHERE type=:type AND $tplField=:template"
            ,   ['empty'=>'', 'type'=>$type, 'template'=>$templateDefault]
            );
        }

        return $this->createResult(true);
    }
}
