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

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\FormSubmit;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;


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

        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
        $requestStack = System::getContainer()->get('request_stack');

        if( $scopeMatcher->isBackendRequest($requestStack->getCurrentRequest()) ) {

            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.$GLOBALS['TL_LANG']['FMD']['storelocator_search'][0].' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = System::getContainer()->get('router')->generate(
                'contao_backend',
                ['do' => 'themes', 'table' => 'tl_module', 'act' => 'edit', 'id' => $this->id],
            );

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile(): void {

        global $objPage;

        $this->Template->formId = 'storelocator_search_'.$this->id;
        $this->Template->action = Environment::get('request');
        $this->Template->requestToken = (defined('VERSION') ? '{{request_token}}' : System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue());

        if( !isset($_GET['search']) && Config::get('useAutoItem') && isset($_GET['auto_item']) ) {
            Input::setGet('search', Input::get('auto_item'));
        }

        $sSearchVal = Input::get('search') ? Input::get('search') : null;

        $aSearchValues = StoreLocator::parseSearchValue($sSearchVal);

        // generate form elements
        $fieldClass = class_exists('\Contao\FormText')?'\Contao\FormText':'\Contao\FormTextField';

        $widgetSearch = null;
        $widgetSearch = new $fieldClass($fieldClass::getAttributesFromDca(
                [
                    'name'          => 'location'
                ,   'label'         => &$GLOBALS['TL_LANG']['tl_storelocator']['field']['postal']
                ,   'inputType'     => 'text'
                ,   'eval'          => ['mandatory'=>true]
                ]
            ,   'location'
            ,   $aSearchValues['term']??''
            )
        );

        $widgetCategories = null;
        $aAvailableCategories = StringUtil::deserialize($this->storelocator_search_categories);

        if( count($aAvailableCategories) > 1 ) {

            $aCategories = [
                'all' => $GLOBALS['TL_LANG']['tl_storelocator']['field']['all_categories']
            ];

            $oCategories = null;
            $oCategories = CategoriesModel::findMultipleByIds($aAvailableCategories);

            while( $oCategories->next() ) {
                $aCategories[ $oCategories->alias ] = $oCategories->title;
            }

            $strRadioClass = $GLOBALS['TL_FFL']['radio'];

            $widgetCategories = new $strRadioClass($strRadioClass::getAttributesFromDca(
                    [
                        'name'          => 'category'
                    ,   'inputType'     => 'radio'
                    ,   'options'       => $aCategories
                    ,   'eval'          => ['mandatory'=>false]
                    ]
                ,   'category'
                ,   $aSearchValues['category'] ?? 'all'
                )
            );
        }

        $widgetSubmit = null;
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

                    if( $widgetCategories->value ) {
                        if( $widgetCategories->value === 'all' ) {
                            $aSearchValues['category'] = '';
                        } else {
                            $aSearchValues['category'] = $widgetCategories->value;
                        }
                    }
                }

                $aSearchValues['longitude'] = Input::post('longitude');
                $aSearchValues['latitude'] = Input::post('latitude');

                $strData = StoreLocator::generateSearchvalue($aSearchValues);
                $strData = str_replace('/', ' ', $strData);

                $objListPage = $this->jumpTo ? PageModel::findWithDetails($this->jumpTo) : $objPage;
                $href = $objListPage->getFrontendUrl((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/'.$strData : '/search/'.$strData);

                $this->redirect($href);
            }
        }

        if( $this->storelocator_provider === 'google-maps' ) {

            // add autocomplete script
            if( $this->storelocator_enable_autocomplete ) {

                $oTemplateAutocomplete = new FrontendTemplate('script_storelocator_autocomplete');
                $oTemplateAutocomplete->mapsKey = Config::get('google_maps_browser_key');
                $oTemplateAutocomplete->country = $this->storelocator_autocomplete_country;
                $oTemplateAutocomplete->fieldId = 'ctrl_'.$widgetSearch->id;

                $this->Template->autoComplete = $oTemplateAutocomplete->parse();
            }

        } else {

            // TODO: HOOK for adding custom javascript provider
        }

        $this->Template->searchField = $widgetSearch;
        $this->Template->searchValues = $aSearchValues;
        $this->Template->categories = $widgetCategories;
        $this->Template->submitButton = $widgetSubmit;
    }
}
