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
 * @copyright 2019 numero2 - Agentur für digitales Marketing
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class ModuleStoreLocatorDetails extends \Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_details';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate() {

        if( TL_MODE == 'BE' ) {

            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### STORELOCATOR DETAILS ###';
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

        $this->Template = new \FrontendTemplate($this->storelocator_details_tpl);
        $this->Template->referer = 'javascript:history.go(-1)';
        $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        $alias = NULL;
        $alias = \Input::get('auto_item') ? \Input::get('auto_item') : \Input::get('store');

        $objStore = NULL;
        $objStore = StoresModel::findByIdOrAlias($alias);

        // get store details
        if( $objStore ) {

            StoreLocator::parseStoreData( $objStore );

            // get image
            if( $objStore->singleSRC ) {

                $objFile = NULL;
                $objFile = \FilesModel::findByUuid($objStore->singleSRC);
                $objStore->image = $objFile;
            }

            $this->Template->store = $objStore;

            $this->Template->labelPhone = $GLOBALS['TL_LANG']['tl_storelocator']['field']['phone'];
            $this->Template->labelFax = $GLOBALS['TL_LANG']['tl_storelocator']['field']['fax'];
            $this->Template->labelEMail = $GLOBALS['TL_LANG']['tl_storelocator']['field']['email'];
            $this->Template->labelWWW = $GLOBALS['TL_LANG']['tl_storelocator']['field']['www'];

            $this->Template->mapsURI = sprintf(
                "https://www.google.com/maps/embed/v1/place?q=%s&key=%s"
            ,   rawurlencode($objStore->name.', '.$objStore->street.', '.$objStore->postal.' '.$objStore->city)
            ,   \Config::get('google_maps_browser_key')
            );

            // HOOK: add custom logic
            if( isset($GLOBALS['TL_HOOKS']['parseStoreDetails']) && is_array($GLOBALS['TL_HOOKS']['parseStoreDetails']) ) {

                foreach( $GLOBALS['TL_HOOKS']['parseStoreDetails'] as $callback ) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($objStore, $this);
                }
            }

        // store not found? throw 404
        } else {

            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate('');
        }
    }
}