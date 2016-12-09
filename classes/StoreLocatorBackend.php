<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   Software Licenses
 * @author    Benny Born <benny.born@numero2.de>
 * @license   StoreLocator
 * @copyright 2015 numero2 - Agentur für Internetdienstleistungen
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class StoreLocatorBackend extends \System {


    /**
     * Show a message in backend if google keys are missing
     *
     * @param DataContainer $dc
     *
     * @return none
     */
    public function showGoogleKeysMissingMessage( DataContainer $dc ) {

        if( TL_MODE != 'BE' )
            return;

        if( \Input::get('table') == "tl_module" && \Input::get('act') == "edit" ) {

            $objModule = \Database::getInstance()->prepare("
                SELECT * FROM tl_module WHERE id = ?
                ")->execute( $dc->id );

            if( !array_key_exists($objModule->type, $GLOBALS['FE_MOD']['storelocator']) ){
                return;
            }
        }

        self::loadLanguageFile('tl_settings');

        if( empty(\Config::get('google_maps_server_key')) ) {
            \Message::addInfo(
                sprintf($GLOBALS['TL_LANG']['tl_settings']['err']['missing_key'],
                    $GLOBALS['TL_LANG']['tl_settings']['google_maps_server_key'][0]
                )
            );
        }

        if( empty(\Config::get('google_maps_browser_key')) ) {
            \Message::addInfo(
                sprintf($GLOBALS['TL_LANG']['tl_settings']['err']['missing_key'],
                    $GLOBALS['TL_LANG']['tl_settings']['google_maps_browser_key'][0]
                )
            );
        }
    }
}
