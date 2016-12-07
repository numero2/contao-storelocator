<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL
 * @copyright 2015 numero2 - Agentur für Internetdienstleistungen
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class ModuleStoreLocatorSearch extends \Module {


	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_storelocator_search';
	
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate() {

		if( TL_MODE == 'BE' ) {

			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### STORELOCATOR SEARCH ###';
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
	
		$this->Template = new \FrontendTemplate($this->storelocator_search_tpl);
		
		$this->Template->searchVal = $this->Input->post('storelocator_search_name') ? $this->_escapeSearchVal( $this->Input->post('storelocator_search_name') ) : $this->Input->get('search');
		$this->Template->country = $this->Input->post('storelocator_search_country') ? $this->Input->post('storelocator_search_country') : $this->Input->get('country');
		$this->Template->country = $this->Template->country ? $this->Template->country : $this->storelocator_search_country;
		$this->Template->formId = 'tl_storelocator';
        $this->Template->moduleId = $this->id;
        $this->Template->action = '';
        
        // redirect to results page
        if( $this->Template->searchVal && ($this->Template->searchVal != $this->Input->get('search')) ) {

            $pageID = $this->jumpTo ? $this->jumpTo : $objPage->id;
            $objLink = $this->Database->prepare("SELECT * FROM tl_page WHERE id = ?;")->execute($pageID);

            $results = $this->generateFrontendUrl(
                $objLink->fetchAssoc()
            ,	'/search/'.$this->Template->searchVal.'/country/'.strtolower($this->Template->country)
            );
            
            $this->redirect( $results, 302);
            die();
        }
		
		// get list of countries
		$objCountries = NULL;
        $objCountries = $this->Database->execute(" SELECT country FROM tl_storelocator_stores GROUP BY country ASC ");
		
        $aCountries = array();
        $aCountries = $objCountries->fetchAllAssoc();
        
        if( $aCountries ) {
            
            $temp = array();
            $aCountryNames = $this->getCountries();
			
            foreach( $aCountries as $i => $v ) {
            
                if( $this->storelocator_show_full_country_names ) {
                    $temp[ $v['country'] ] = $aCountryNames[ $v['country'] ];
                } else {
                    $temp[ $v['country'] ] = $v['country'];
                }
            }

            asort($temp);
            $aCountries = $temp;
        }
        
        $this->Template->countries = $aCountries;
	}

	private function _escapeSearchVal( $val=NULL ) {

		return str_replace( array('?','/'), '', $val );
	}
}