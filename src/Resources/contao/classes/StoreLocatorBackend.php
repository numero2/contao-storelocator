<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\System;


class StoreLocatorBackend {


    /**
     * Show a message in backend if provider keys are missing
     *
     * @param DataContainer $dc
     */
    public function showNoProviderAvailable( DataContainer $dc ): void {

        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
        $requestStack = System::getContainer()->get('request_stack');

        if( $scopeMatcher->isBackendRequest($requestStack->getCurrentRequest()) ) {
            return;
        }

        $isEditingModule = false;

        if( Input::get('table') == "tl_module" && Input::get('act') == "edit" ) {

            $objModule = Database::getInstance()->prepare("
                SELECT * FROM tl_module WHERE id = ?
                ")->execute($dc->id);

            if( $objModule && !array_key_exists($objModule->type, $GLOBALS['FE_MOD']['storelocator']) ) {
                return;
            }

            $isEditingModule = true;
        }

        System::loadLanguageFile('tl_settings');

        $oGeo = System::getContainer()->get('numero2_storelocator.geocoder');
        $hasActiveProvider = false;
        foreach( $oGeo->getAvailableProviders() as $name ) {

            if( $oGeo->getProvider($name) ) {
                $hasActiveProvider = true;
                break;
            }
        }

        if( !$hasActiveProvider ) {
            Message::addInfo($GLOBALS['TL_LANG']['tl_settings']['err']['missing_server_key']);
        }

        if( $isEditingModule && !count($oGeo->getJavascriptProviders()) ) {
            Message::addInfo($GLOBALS['TL_LANG']['tl_settings']['err']['missing_browser_key']);
        }
    }


    /**
     * Fills coordinates if not already set and saving
     *
     * @param DataContainer $dc
     */
    public function fillCoordinates( DataContainer $dc ): void {

        $aResults = [];

        if( Input::get('key') == "fillCoordinates" ) {

            ini_set('max_execution_time', 0);

            $results = Database::getInstance()->prepare("
                SELECT *
                FROM tl_storelocator_stores
                WHERE pid=? AND (longitude='' OR latitude='')
            ")->execute($dc->id);

            $aResults = $results->fetchAllAssoc();

        }

        // creates array with data from activeRecord
        if( $dc->activeRecord ) {

            if( empty($dc->activeRecord->longitude) || empty($dc->activeRecord->latitude) ) {

                $aResults = [
                    [
                        "id" => $dc->id
                    ,   "street" => $dc->activeRecord->street
                    ,   "postal" => $dc->activeRecord->postal
                    ,   "city" => $dc->activeRecord->city
                    ,   "country" => $dc->activeRecord->country
                    ]
                ];
            }
        }

        if( !empty($aResults) ) {

            foreach( $aResults as $key => $value ) {

                $oSL = null;
                $oSL = new StoreLocator();

                // find coordinates using google maps api
                $coords = $oSL->getCoordinates(
                    $value['street']
                ,   $value['postal']
                ,   $value['city']
                ,   $value['country']
                );

                if( !empty($coords) ) {
                    Database::getInstance()->prepare("UPDATE tl_storelocator_stores %s WHERE id=?")->set($coords)->execute($value['id']);
                }
            }
        }

        if( Input::get('key') == "fillCoordinates" ) {
            Controller::redirect(System::getReferer());
        }
    }


    /**
     * Returns a list of weekdays
     *
     * @return array
     */
    public static function getMapInteractions(): array {

        return [
            'nothing'               => $GLOBALS['TL_LANG']['tl_storelocator']['interactions']['nothing']
        ,   'showMarkerInfo'        => $GLOBALS['TL_LANG']['tl_storelocator']['interactions']['showMarkerInfo']
        ,   'scrollToListElement'   => $GLOBALS['TL_LANG']['tl_storelocator']['interactions']['scrollToListElement']
        ];
    }


    /**
     * Returns a list of weekdays
     *
     * @return array
     */
    public static function getListInteractions(): array {

        return [
            'nothing'                       => $GLOBALS['TL_LANG']['tl_storelocator']['interactions']['nothing']
        ,   'scrollToMapAndCenterMarker'    => $GLOBALS['TL_LANG']['tl_storelocator']['interactions']['scrollToMapAndCenterMarker']
        ];
    }
}
