<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\EventListener\StoreLocator;

use Contao\CoreBundle\Slug\Slug;
use Doctrine\DBAL\Connection;
use numero2\StoreLocator\StoreLocator;
use numero2\StoreLocator\StoresModel;
use numero2\StoreLocatorBundle\Event\StoreImportEvent;
use Symfony\Contracts\EventDispatcher\Event;


class StoreImportListener extends Event {


    /**
     * @var Contao\CoreBundle\Slug\Slug
     */
    private Connection $connection;

    /**
     * @var Doctrine\DBAL\Connection
     */
    private Slug $slug;


    public function __construct( Connection $connection, Slug $slug ) {

        $this->connection = $connection;
        $this->slug = $slug;
    }


    /**
     * Parses the given store before saved or updated
     *
     * @param numero2\StoreLocatorBundle\Event\StoreImportEvent $event
     */
    public function __invoke( StoreImportEvent $event ): void  {

        $model = $event->getModel();
        $columns = $event->getColumns();
        $data = $event->getData();

        if( $event->getSkipImport() ) {
            return;
        }

        // generate alias
        if( empty($model->alias) && !empty($model->name) ) {

            $aliasExists = function( string $alias ) use ( $model ): bool {

                $t = StoresModel::getTable();

                $result = $this->connection->executeQuery(
                    "SELECT id FROM $t WHERE alias=:alias AND id!=:id"
                ,   ['alias'=>$alias, 'id'=>$model->id ?? 0]
                )->fetchAllAssociative();

                if( !empty($result) ) {
                    return true;
                }

                return false;
            };

            $alias = $this->slug->generate($model->name, [], $aliasExists);

            $model->alias = $alias;
        }

        // add "https" in front of website url
        if( !empty($model->url) && strpos($model->url, 'https') === false ) {
            $model->url = 'https://' . $model->url;
        }

        // get coordinates
        if( empty($model->latitude) && empty($model->longitude)
            && !empty($model->street) && !empty($model->postal) && !empty($model->city) && !empty($model->country)
        ) {

            $oSL = new StoreLocator();

            $aCoords = $oSL->getCoordinates($model->street, $model->postal, $model->city, $model->country);

            if( !empty($aCoords['latitude']) ) {
                $model->latitude = $aCoords['latitude'];
            }
            if( !empty($aCoords['longitude']) ) {
                $model->longitude = $aCoords['longitude'];
            }
        }
    }
}
