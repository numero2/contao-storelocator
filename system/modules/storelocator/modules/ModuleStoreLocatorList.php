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


class ModuleStoreLocatorList extends \Module {


	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_storelocator_list';
	
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate() {

		if( TL_MODE == 'BE' ) {

			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### STORELOCATOR LIST ###';
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

		$this->Template = new \FrontendTemplate($this->storelocator_list_tpl);
		
		$sSearchVal = $this->Input->get('search') ? $this->Input->get('search') : NULL;
		$sSearchCountry = $this->Input->get('country') ? $this->Input->get('country') : NULL;

        $aEntries = array();
        
        // check if an empty search is allowed
        if( !$this->storelocator_allow_empty_search && !$sSearchVal && $sSearchCountry ) {
        
            $this->Template->error = true;
            
        } else {
		
            $term = NULL;
        
            // add country code for correct search results
            if( !empty($sSearchCountry) ) {
            
                if( !empty($sSearchVal) ) {
                    $term = $sSearchVal.', '.$sSearchCountry;
                } else {
                    $term = $aCountryNames[$entry['country']];
                }
            }

            $aCategories = array();
            $aCategories = deserialize($this->storelocator_list_categories);
            
			$aCountryNames = $this->getCountries();
			
            if( !empty($term) ) {
			
                // get coordinates of searched destination
				$sl = new StoreLocator();
                $aCoordinates = array();
				$aCoordinates = $sl->getCoordinatesByString($term);

                if( !empty($aCoordinates) ) {

                    $objStores = NULL;

                    // search all countries
                    if( !empty($sSearchVal) ) {
                    
                        $objStores = $this->Database->prepare("
                            SELECT
                                *
                            , 3956 * 1.6 * 2 * ASIN(SQRT( POWER(SIN((? -abs(latitude)) * pi()/180 / 2),2) + COS(? * pi()/180 ) * COS( abs(latitude) *  pi()/180) * POWER(SIN((?-longitude) *  pi()/180 / 2), 2) )) AS `distance`
                            FROM `tl_storelocator_stores`
                            WHERE
                                    pid IN(".implode(',',$aCategories).")
                                AND latitude != '' 
                                AND longitude != ''
                                ".(($this->storelocator_limit_distance) ? "HAVING distance < {$this->storelocator_max_distance} ": '')."
                            ORDER BY `highlight` DESC ,`distance` ASC
                        ")->limit($this->storelocator_list_limit)->execute(
                            $aCoordinates['latitude']
                        ,	$aCoordinates['latitude']
                        ,	$aCoordinates['longitude']
                        );

                    // search selected country only
                    } else {
                    
                        $objStores = $this->Database->prepare("
                            SELECT
                                *
                            , 3956 * 1.6 * 2 * ASIN(SQRT( POWER(SIN((? -abs(latitude)) * pi()/180 / 2),2) + COS(? * pi()/180 ) * COS( abs(latitude) *  pi()/180) * POWER(SIN((?-longitude) *  pi()/180 / 2), 2) )) AS `distance`
                            FROM `tl_storelocator_stores`
                            WHERE
                                    pid IN(".implode(',',$aCategories).")
                                AND latitude != '' 
                                AND longitude != '' 
                                ".(($this->storelocator_limit_distance) ? "HAVING distance < {$this->storelocator_max_distance} ": '')."
                                AND country = ?
                            ORDER BY `highlight` DESC ,`distance` ASC
                        ")->limit($this->storelocator_list_limit)->execute(
                            $aCoordinates['latitude']
                        ,	$aCoordinates['latitude']
                        ,	$aCoordinates['longitude']
                        ,   strtoupper($sSearchCountry)
                        );

                    }

                    $entries = array();
                    $entries = $objStores->fetchAllAssoc();

                    if( !empty($entries) ) {
					
                        foreach( $entries as $entry ) {

                            if( empty($sSearchVal) ) {
                                $entry['distance'] = NULL;
                            }

                            $entry['class'] = $entry['highlight'] ? 'starred' : '';

                            $entry['country_code'] = $entry['country'];
                            $entry['country_name'] = $aCountryNames[$entry['country']];
                        
                            // generate link
                            $link = null;
                            
                            if( $this->jumpTo ) {

                                $objLink = $this->Database->prepare("SELECT * FROM tl_page WHERE id = ?;")->execute($this->jumpTo);

                                $entry['link'] = $this->generateFrontendUrl(
                                    $objLink->fetchAssoc()
                                ,	( !$GLOBALS['TL_CONFIG']['useAutoItem'] ? '/store/' : '/' ).$entry['id'].'-'.standardize($entry['name'].' '.$entry['city'])
                                );
                            }	

                            // get opening times
                            $entry['opening_times'] = unserialize( $entry['opening_times'] );
                            $entry['opening_times'] = !empty($entry['opening_times'][0]['from']) ? $entry['opening_times'] : NULL;

                        
                            $aEntries[] = $entry;
                        }
                    }
                }
            }
        }

		$this->Template->entries = $aEntries;
	}
}

?>