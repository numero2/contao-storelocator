<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class ModuleStoreLocator extends \Frontend
{

    /**
     * Adds a specific css class to body tag if a search had been done
     * @return none
     */
    public function addResultsBodyClass( \PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular ) {

        $searchTerm = NULL;
        $searchTerm = $this->Input->post('storelocator_search_name');

        if( empty($searchTerm) )
            return false;


        $objPage->cssClass = $objPage->cssClass . ' storelocatorresults';
    }
}