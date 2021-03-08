<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2021 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2021 numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator\DCAHelper;

use Contao\Backend;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Image;
use Contao\ModuleModel;
use numero2\StoreLocator\CategoriesModel;


class Module extends Backend {


    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getCategories() {

        $aCategories = [];

        $oCategories = NULL;
        $oCategories = CategoriesModel::getCategories();

        while( $oCategories->next() ) {
            $aCategories[ $oCategories->id ] = $oCategories->title;
        }

        return $aCategories;
    }

    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getMapType() {

        $aType = [
            'roadmap' => $GLOBALS['TL_LANG']['tl_module']['storelocator_maptypes']['roadmap']
        ,   'satellite' => $GLOBALS['TL_LANG']['tl_module']['storelocator_maptypes']['satellite']
        ,   'terrain' => $GLOBALS['TL_LANG']['tl_module']['storelocator_maptypes']['terrain']
        ,   'hybrid' => $GLOBALS['TL_LANG']['tl_module']['storelocator_maptypes']['hybrid']
        ];

        return $aType;
    }

    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getMapFormat() {

        $aFormat = [
            'png' => $GLOBALS['TL_LANG']['tl_module']['storelocator_formats']['png']
        ,   'png32' => $GLOBALS['TL_LANG']['tl_module']['storelocator_formats']['png32']
        ,   'gif' => $GLOBALS['TL_LANG']['tl_module']['storelocator_formats']['gif']
        ,   'jpg' => $GLOBALS['TL_LANG']['tl_module']['storelocator_formats']['jpg']
        ,   'jpg-baseline' => $GLOBALS['TL_LANG']['tl_module']['storelocator_formats']['jpg-baseline']
        ];

        return $aFormat;
    }

    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getMapScale() {

        $aScale = [
            $GLOBALS['TL_LANG']['tl_module']['storelocator_scales']['free'] => [
                '1' => $GLOBALS['TL_LANG']['tl_module']['storelocator_scales']['1']
            ,   '2' => $GLOBALS['TL_LANG']['tl_module']['storelocator_scales']['2']
            ]
        ,   $GLOBALS['TL_LANG']['tl_module']['storelocator_scales']['premium_plan'] => [
                '4' => $GLOBALS['TL_LANG']['tl_module']['storelocator_scales']['4']
            ]
        ];

        return $aScale;
    }



    /**
     * Returns a list of all templates
     *
     * @param  Contao\DataContainer $dc
     *
     * @return array
     */
    public function getTemplates( DataContainer $dc ) {
        return Controller::getTemplateGroup('mod_storelocator');
    }


    /**
     * Returns a list of all templates
     *
     * @param  Contao\DataContainer $dc
     *
     * @return array
     */
    public function getStoreFields() {

        self::loadLanguageFile('tl_storelocator_stores');

        $arr = [
            'name' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['name'][0]
        ,   'email' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['email'][0]
        ,   'url' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['url'][0]
        ,   'phone' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['phone'][0]
        ,   'fax' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['fax'][0]
        ,   'description' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['description'][0]
        ,   'postal' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['postal'][0]
        ,   'city' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['city'][0]
        ];

        return $arr;
    }


    /**
     * Generates a list of all Stores with Categorie 1
     *
     * @return array
     */
    public function getFilterModules() {

        $oModule = ModuleModel::findBy('type', 'storelocator_filter');
        $aModule = [];

        if( $oModule ) {
            foreach( $oModule as $key => $value ) {
                $aModule[$value->id] = $value->name.' (ID: '.$value->id.')';
            }
        }

        return $aModule;
    }


    /**
     * Generates a list of all Stores with Categorie 1
     *
     * @return array
     */
    public function getJavascriptProviders() {

        $aProviders = [];

        foreach( $GLOBALS['N2SL']['javascript_providers'] as $name => $settings ) {

            $isAvailable = $settings['init_callback']();

            if( $isAvailable ) {
                $aProviders[] = $name;
            }
        }

        return $aProviders;
    }


    /**
    * Return the edit module wizard
    *
    * @param Contao\DataContainer $dc
    *
    * @return string
    */
    public function editModule( DataContainer $dc ) {

        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_module']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_module']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_module']['editalias'][0]) . '</a>';
    }
}
