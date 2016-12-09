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

		// store not found? throw 404
		} else {

            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate('');
		}
	}
}