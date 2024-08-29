<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator\DCAHelper;

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\System;
use numero2\StoreLocator\CategoriesModel;


class Module {


    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getCategories(): array {

        $aCategories = [];

        $oCategories = null;
        $oCategories = CategoriesModel::getCategories();

        if( $oCategories) {
            while( $oCategories->next() ) {
                $aCategories[ $oCategories->id ] = $oCategories->title;
            }
        }

        return $aCategories;
    }


    /**
     * Returns a list of all map tyoes
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
     * Returns a list of all map formats
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
     * Returns a list of all map scaling options
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
     * Returns a list of all store fields
     *
     * @param Contao\DataContainer $dc
     *
     * @return array
     */
    public function getStoreFields( DataContainer $dc ): array {

        System::loadLanguageFile('tl_storelocator_stores');

        $aOptions = [];

        foreach( $GLOBALS['TL_DCA']['tl_module']['fields'][$dc->field]['options'] as $key => $field ) {
            $aOptions[$field] = $GLOBALS['TL_LANG']['tl_storelocator_stores'][$field][0];
        }

        return $aOptions;
    }


    /**
     * Generates a list of all available filter modules
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
     * Generates a list of all available JavaScript providers
     *
     * @return array
     */
    public function getJavascriptProviders(): array {

        $oGeo = null;
        $oGeo = System::getContainer()->get('numero2_storelocator.geocoder');

        return $oGeo->getJavascriptProviders();
    }


    /**
    * Return the edit module wizard
    *
    * @param Contao\DataContainer $dc
    *
    * @return string
    */
    public function editModule( DataContainer $dc ): string {

        $requestToken = (defined('VERSION') ? '{{request_token}}' : System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue());

        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . $requestToken . '" title="' . sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_module']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_module']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_module']['editalias'][0]) . '</a>';
    }


    /**
     * Returns a list of fields which a listing module can be sorted by
     *
     * @return array
     */
    public function getSortableFields(): array {

        System::loadLanguageFile('tl_storelocator_stores');
        Controller::loadDataContainer('tl_storelocator_stores');

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


    /**
     * Hides fields depending on the available providers
     *
     * @param Contao\DataContainer $dc
     */
    public function hideProviderDependentField( DataContainer $dc ): void {

        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
        $requestStack = System::getContainer()->get('request_stack');

        if( !$scopeMatcher->isBackendRequest($requestStack->getCurrentRequest()) ) {
            return;
        }

        $provider = '';

        if( Input::get('table') == "tl_module" && Input::get('act') == "edit" ) {

            $objModule = Database::getInstance()->prepare("
                SELECT * FROM tl_module WHERE id = ?
            ")->execute($dc->id);

            if( $objModule ) {
                if( !array_key_exists($objModule->type, $GLOBALS['FE_MOD']['storelocator']) ) {
                    return;
                }
                $provider = $objModule->storelocator_provider;
            }
        }

        if( $provider && array_key_exists('storelocator_provider-'.$provider, $GLOBALS['TL_DCA']['tl_module']['subpalettes']) ) {
            $GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_show_map'] = $GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_provider-'.$provider];
        }
    }
}
