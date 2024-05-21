<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\Event;

use numero2\StoreLocator\StoresModel;
use Symfony\Contracts\EventDispatcher\Event;


class StoreImportEvent extends Event {


    /**
     * @var numero2\StoreLocator\StoresModel
     */
    private StoresModel $model;

    /**
     * @var array
     */
    private array $columns;

    /**
     * @var array
     */
    private array $data;

    /**
     * @var bool
     */
    private bool $skipImport;


    public function __construct( StoresModel $model, array $columns, array $data ) {

        $this->model = $model;
        $this->columns = $columns;
        $this->data = $data;
        $this->skipImport = false;
    }


    /**
     * @return numero2\StoreLocator\StoresModel
     */
    public function getModel(): StoresModel {

        return $this->model;
    }


    /**
     * @param numero2\StoreLocator\StoresModel $model
     *
     * @return numero2\StoreLocatorBundle\Event\StoreImportEvent
     */
    public function setModel( StoresModel $model ): self {

        $this->model = $model;

        return $this;
    }


    /**
     * @return array
     */
    public function getColumns(): array {

        return $this->columns;
    }


    /**
     * @return array
     */
    public function getData(): array {

        return $this->data;
    }


    /**
     * @return bool
     */
    public function getSkipImport(): bool {

        return $this->skipImport;
    }


    /**
     * @param bool $skip
     *
     * @return numero2\StoreLocatorBundle\Event\StoreImportEvent
     */
    public function setSkipImport( bool $skipImport ): self {

        $this->skipImport = $skipImport;

        return $this;
    }
}
