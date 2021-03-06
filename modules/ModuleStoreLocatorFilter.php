<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2019 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2020 numero2 - Agentur für digitales Marketing GbR
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class ModuleStoreLocatorFilter extends \Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_filter';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate() {

        if( TL_MODE == 'BE' ) {

            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### STORELOCATOR FILTER ###';
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
    protected function compile() {

        global $objPage;

        $this->Template = new \FrontendTemplate($this->storelocator_filter_tpl?:$this->strTemplate);
        $this->Template->formId = 'storelocator_filter_'.$this->id;
        $this->Template->action = \Environment::get('request');

        if( !isset($_GET['search']) && \Config::get('useAutoItem') && isset($_GET['auto_item']) ) {
            \Input::setGet('search', \Input::get('auto_item'));
        }

        $sSearchVal = $this->Input->get('search') ? $this->Input->get('search') : NULL;

        $aSearchValues = StoreLocator::parseSearchValue($sSearchVal);


        // generate form elements
        $widgetFilter = NULL;
        $widgetFilter = new \FormTextField(\FormTextField::getAttributesFromDca(
                array(
                    'name'      => 'filter'
                ,   'label'     => &$GLOBALS['TL_LANG']['tl_storelocator']['filter']['filter_label']
                ,   'inputType' => 'text'
                ,    'eval'        => array( 'mandatory'=>true )
                )
            ,   'filter'
            ,   $aSearchValues['filter']
            )
        );

        $widgetSubmit = NULL;
        $widgetSubmit = new \FormSubmit();
        $widgetSubmit->id = 'filtering';
        $widgetSubmit->label = $GLOBALS['TL_LANG']['tl_storelocator']['filter']['filter'];

        $this->Template->labelReset = $GLOBALS['TL_LANG']['tl_storelocator']['filter']['filter_reset'];
        $this->Template->hrefReset = $objPage->getFrontendUrl( '/clear/filter' );

        if( isset($_GET['clear']) && \Input::get('clear') == 'filter' ) {

            $aSearchValues['filter'] = null;
            $aSearchValues['order'] = null;
            $aSearchValues['sort'] = null;
            $strData = StoreLocator::generateSearchvalue($aSearchValues);

            if( count($trData) == 0 ){

                $href = $objPage->getFrontendUrl();
            } else {

                $href = $objPage->getFrontendUrl((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/%s' : '/search/%s');
                $href = sprintf($href, $strData);
            }

            $this->redirect( $href );
        }

        // redirect to listing page
        if( \Input::post('FORM_SUBMIT') == $this->Template->formId ) {

            $widgetFilter->validate();
            $filter = $widgetFilter->value;

            if( !empty($filter) ) {

                $aSearchValues['filter'] = $filter;

                $strData = StoreLocator::generateSearchvalue($aSearchValues);
                $objListPage = $this->jumpTo ? \PageModel::findWithDetails($this->jumpTo) : $objPage;
                $href = $objPage->getFrontendUrl((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/%s' : '/search/%s');
                $href = sprintf($href, $strData);

                $this->redirect( $href );
            }

        } else if( \Input::get('order') && \Input::get('sort') ){

            $aSearchValues['order'] = \Input::get('order');
            $aSearchValues['sort'] = \Input::get('sort');

            $strData = StoreLocator::generateSearchvalue($aSearchValues);

            $href = $objPage->getFrontendUrl((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/%s' : '/search/%s');
            $href = sprintf($href, $strData);

            $this->redirect( $href );
        }

        $sortFilter = array();
        foreach( deserialize($this->storelocator_sortable) as $key => $value) {

            $active = !empty($aSearchValues['order'])&&$aSearchValues['order']==$value;
            $newSort = ($active&&!empty($aSearchValues['sort'])&&$aSearchValues['sort']=='asc')?'desc':'asc';
            $strData = StoreLocator::generateSearchvalue($aSearchValues);
            if( $strData ){

                $href = $objPage->getFrontendUrl(((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ? '/%s' : '/search/%s' ).'/order/%s/sort/%s');
                $href = sprintf($href, $strData, $value, $newSort);
            } else {

                $href = $objPage->getFrontendUrl('/order/%s/sort/%s');
                $href = sprintf($href, $value, $newSort);
            }

            $sortFilter[] = array(
                'label' => $GLOBALS['TL_LANG']['tl_storelocator']['filter'][$value]
            ,    'href' => $href
            ,    'title' => $GLOBALS['TL_LANG']['tl_storelocator']['filter'][$value]." ".$GLOBALS['TL_LANG']['tl_storelocator']['filter']['order'][$newSort]
            ,    'class' => ($active?"active ".$newSort:"")
            );
        }

        $this->Template->labelFilter = $GLOBALS['TL_LANG']['tl_storelocator']['filter']['filter_label'];
        $this->Template->labelSorting = $GLOBALS['TL_LANG']['tl_storelocator']['filter']['order_label'];

        $this->Template->filterField = $widgetFilter;
        $this->Template->sortFields = $sortFilter;
        $this->Template->submitButton = $widgetSubmit;
        $this->Template->resetButton = $widgetSubmitReset;
    }
}