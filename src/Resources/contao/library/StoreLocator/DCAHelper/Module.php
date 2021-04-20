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
    public function getCategories(): array {

        $aCategories = [];

        $oCategories = NULL;
        $oCategories = CategoriesModel::getCategories();

        if( $oCategories) {
            while( $oCategories->next() ) {
                $aCategories[ $oCategories->id ] = $oCategories->title;
            }
        }

        return $aCategories;
    }


    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getMapType(): array {

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
    public function getMapFormat(): array {

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
    public function getMapScale(): array {

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
     * @param Contao\DataContainer $dc
     *
     * @return array
     */
    public function getTemplates( DataContainer $dc ): array {
        return Controller::getTemplateGroup('mod_storelocator');
    }


    /**
     * Returns a list of all templates
     *
     * @param  Contao\DataContainer $dc
     *
     * @return array
     */
    public function getStoreFields( DataContainer $dc ): array {

        self::loadLanguageFile('tl_storelocator_stores');

        $aOptions = [];

        foreach( $GLOBALS['TL_DCA']['tl_module']['fields'][$dc->field]['options'] as $key => $field ) {
            $aOptions[$field] = $GLOBALS['TL_LANG']['tl_storelocator_stores'][$field][0];
        }

        return $aOptions;
    }


    /**
     * Generates a list of all Stores with Categorie id
     *
     * @return array
     */
    public function getFilterModules(): array {

        $oModules = ModuleModel::findBy('type', 'storelocator_filter');

        $aModule = [];

        if( $oModules ) {
            foreach( $oModules as $key => $oModule ) {
                $aModule[$oModule->id] = $oModule->name.' (ID: '.$oModule->id.')';
            }
        }

        return $aModule;
    }


    /**
     * Generates a list of all available javascript providers
     *
     * @return array
     */
    public function getJavascriptProviders(): array {

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
    public function editModule( DataContainer $dc ): string {

        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_module']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_module']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_module']['editalias'][0]) . '</a>';
    }


    /**
     * Returns a list of fields which a listing module can be sorted by
     *
     * @return array
     */
    public function getSortableFields(): array {

        self::loadLanguageFile('tl_storelocator_stores');
        self::loadDataContainer('tl_storelocator_stores');

        $aFields = [];

        if( !empty($GLOBALS['TL_DCA']['tl_storelocator_stores']['fields']) ) {

            foreach( $GLOBALS['TL_DCA']['tl_storelocator_stores']['fields'] as $name => $field ) {

                if( !empty($field['feSortable']) && $field['feSortable'] ) {
                    $aFields[$name] = $field['label'][0];
                }
            }
        }

        return $aFields;
    }
}
