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


namespace numero2\StoreLocator;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\FormRadioButton;
use Contao\FormSubmit;
use Contao\FormTextField;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use Patchwork\Utf8;


class ModuleStoreLocatorSearch extends Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_search';


    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate(): string {

        if( TL_MODE == 'BE' ) {

            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['storelocator_search'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile(): void {

        global $objPage;

        $this->Template = new FrontendTemplate($this->storelocator_search_tpl?:$this->strTemplate);

        $this->Template->formId = 'storelocator_search_'.$this->id;
        $this->Template->action = Environment::get('request');

        if( !isset($_GET['search']) && Config::get('useAutoItem') && isset($_GET['auto_item']) ) {
            Input::setGet('search', Input::get('auto_item'));
        }

        $sSearchVal = $this->Input->get('search') ? $this->Input->get('search') : NULL;

        $aSearchValues = StoreLocator::parseSearchValue($sSearchVal);

        // generate form elements
        $widgetSearch = NULL;
        $widgetSearch = new FormTextField(FormTextField::getAttributesFromDca(
                [
                    'name'          => 'location'
                ,   'label'         => &$GLOBALS['TL_LANG']['tl_storelocator']['field']['postal']
                ,   'inputType'     => 'text'
                ,    'eval'         => ['mandatory'=>true]
                ]
            ,   'location'
            ,   $aSearchValues['term']
            )
        );

        $widgetCategories = NULL;
        $aAvailableCategories = StringUtil::deserialize($this->storelocator_search_categories);

        if( count($aAvailableCategories) > 1 ) {

            $aCategories = [
                'all' => $GLOBALS['TL_LANG']['tl_storelocator']['field']['all_categories']
            ];

            $oCategories = NULL;
            $oCategories = CategoriesModel::findMultipleByIds($aAvailableCategories);

            while( $oCategories->next() ) {
                $aCategories[ $oCategories->alias ] = $oCategories->title;
            }

            $widgetCategories = new FormRadioButton(FormRadioButton::getAttributesFromDca(
                    [
                        'name'          => 'category'
                    ,   'inputType'     => 'radio'
                    ,    'eval'         => ['mandatory'=>false]
                    ,   'options'       => $aCategories
                    ]
                ,   'category'
                ,   ($aSearchValues['category']?$aSearchValues['category']:'all')
                )
            );
        }

        $widgetSubmit = NULL;
        $widgetSubmit = new FormSubmit();
        $widgetSubmit->id = 'search';
        $widgetSubmit->label = $GLOBALS['TL_LANG']['tl_storelocator']['field']['search'];

        // redirect to listing page
        if( Input::post('FORM_SUBMIT') == $this->Template->formId ) {

            $widgetSearch->validate();
            $term = $widgetSearch->value;

            if( !empty($term) ) {

                $aSearchValues['term'] = $term;

                if( $widgetCategories ) {

                    $widgetCategories->validate();

                    if( $widgetCategories->value && $widgetCategories->value != 'all' ) {
                        $aSearchValues['category'] = $widgetCategories->value;
                    }
                }

                $aSearchValues['longitude'] = Input::post('longitude');
                $aSearchValues['latitude'] = Input::post('latitude');

                $strData = StoreLocator::generateSearchvalue($aSearchValues);

                $objListPage = $this->jumpTo ? PageModel::findWithDetails($this->jumpTo) : $objPage;
                $href = $objListPage->getFrontendUrl((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/'.$strData : '/search/'.$strData);

                $this->redirect($href);
            }
        }

        // add autocomplete script
        if( $this->storelocator_enable_autocomplete ) {

            $oTemplateAutocomplete = new FrontendTemplate('script_storelocator_autocomplete');
            $oTemplateAutocomplete->mapsKey = Config::get('google_maps_browser_key');
            $oTemplateAutocomplete->country = $this->storelocator_autocomplete_country;
            $oTemplateAutocomplete->fieldId = 'ctrl_'.$widgetSearch->id;

            $this->Template->autoComplete = $oTemplateAutocomplete->parse();
            $objPage->loadedMapsApi = true;
        }

        $this->Template->searchField = $widgetSearch;
        $this->Template->categories = $widgetCategories;
        $this->Template->submitButton = $widgetSubmit;
    }
}
