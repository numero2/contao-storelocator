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

        global $objPage;

		$this->Template = new \FrontendTemplate($this->storelocator_list_tpl);

		if( !isset($_GET['search']) && \Config::get('useAutoItem') && isset($_GET['auto_item']) ) {
			\Input::setGet('search', \Input::get('auto_item'));
		}

		$sSearchVal = $this->Input->get('search') ? $this->Input->get('search') : NULL;

		if( strpos($sSearchVal, ";") !== false ) {
			$sSearchVal = explode(";", $sSearchVal);
		}


        $aEntries = array();
		$aCoordinates = array();

        // check if an empty search is allowed
        if( !$this->storelocator_allow_empty_search && !$sSearchVal && $sSearchCountry ) {

            $this->Template->error = true;

        } else {

			$aCategories = array();
			$aCategories = deserialize($this->storelocator_list_categories);

            $term = NULL;
			if( is_array($sSearchVal) ){

				$term = $sSearchVal[0];

				if( count($sSearchVal) == 3 ){

					$aCoordinates['longitude'] = $sSearchVal[1];
					$aCoordinates['latitude'] = $sSearchVal[2];
				} else if( count($sSearchVal) == 4 ){

					$category = $sSearchVal[1];
					$aCoordinates['longitude'] = $sSearchVal[2];
					$aCoordinates['latitude'] = $sSearchVal[3];

				} else if( count($sSearchVal) == 2 ){

					$category = $sSearchVal[1];
				}
				if( $category ){

					$objCategory = CategoriesModel::findByAlias($category);
					if( $objCategory && $objCategory->count() > 0 && in_array($objCategory->id,$aCategories) ) {
						$category = array($objCategory->id);
					} else {
						$category = null;
					}
				}
			} else{

				$term = $sSearchVal;
			}

			$aCountryNames = $this->getCountries();

            if( !empty($term) || $this->storelocator_allow_empty_search ) {

				// search for longitude and latitude
				if( !empty($term) && (empty($aCoordinates['longitude']) || empty($aCoordinates['latitude'])) ) {
					$sl = new StoreLocator();
					$aCoordinates = $sl->getCoordinatesByString($term);
				}


                $objStores = NULL;
                // search all countries
                if( !empty($term) ) {

					$objStores = StoresModel::searchNearby(
						$aCoordinates['latitude'], $aCoordinates['longitude'],
						($this->storelocator_limit_distance?$this->storelocator_max_distance:0),
						$this->storelocator_list_limit,
						($category?$category:$aCategories));

                // search selected country only
                } else {

                    $objStores = StoresModel::searchCountry(
						$this->storelocator_search_country,
						$this->storelocator_list_limit,
						($category?$category:$aCategories));
                }


                if( !empty($objStores) ) {

                    foreach( $objStores as $entry ) {

                        if( empty($sSearchVal) ) {
                            $entry->distance = NULL;
                        }

			            StoreLocator::parseStoreData( $entry );

                        $entry->class = $entry->highlight ? 'starred' : '';

                        // generate link
                        $link = null;

                        if( $this->jumpTo ) {

                            $objLink = $this->Database->prepare("SELECT * FROM tl_page WHERE id = ?;")->execute($this->jumpTo);

                            $entry->link = $this->generateFrontendUrl(
                                $objLink->fetchAssoc()
                            ,	( !$GLOBALS['TL_CONFIG']['useAutoItem'] ? '/store/' : '/' ).$entry->alias
                            );
                        }

                        $aEntries[] = $entry;
                    }

                    $objPage->cssClass = $objPage->cssClass . 'storelocatorresults';
                }
            }
        }

		$this->Template->entries = $aEntries;
	}
}
