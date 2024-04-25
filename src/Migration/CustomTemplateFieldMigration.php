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

        $schemaManager = $this->connection->getSchemaManager();

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

            $result = $this->connection
                ->prepare("SELECT id FROM $t WHERE type=? AND $tplField!=$field")
                ->executeQuery([$type]);

            if( $result && $result->rowCount() ) {
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

            // set default value to empty string
            $this->connection
                ->prepare("UPDATE $t SET $tplField=$field WHERE type=? AND $tplField!=$field")
                ->executeStatement([$type]);

            // set default value to empty string
            $this->connection
                ->prepare("UPDATE $t SET $tplField=?, $field=? WHERE type=? AND $tplField=?")
                ->executeStatement(['', '', $type, $templateDefault]);
        }

        return $this->createResult(true);
    }
}
