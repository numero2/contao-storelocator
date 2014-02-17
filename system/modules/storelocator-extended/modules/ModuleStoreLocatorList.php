<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    storelocator
 * @license    LGPL
 * @filesource
 */


class ModuleStoreLocatorList extends Module {


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

			$objTemplate = new BackendTemplate('be_wildcard');

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

		$this->Template = new FrontendTemplate($this->storelocator_list_tpl);

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

			$aCountryNames = Contao\System::getCountries();

            if( !empty($term) ) {

                // get coordinates of searched destination
                $aCoordinates = array();
				$aCoordinates = StoreLocator::getCoordinatesByString($term);

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
                            ORDER BY `distance` ASC
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
                            ORDER BY `distance` ASC
                        ")->limit($this->storelocator_list_limit)->execute(
                            $aCoordinates['latitude']
                        ,	$aCoordinates['latitude']
                        ,	$aCoordinates['longitude']
                        ,   strtoupper($sSearchCountry)
                        );

                    }
                    
                    // get store logo
	            $objLogo = FilesModel::findByUuid($store['logo']);
	            if ( $objLogo !== null )
	            {
	                $arrLogo = $objLogo->row();
	                $arrLogo['meta'] = unserialize($arrLogo['meta']);
	
	                $strLogo = sprintf('<img src="%s" alt="%s" title="%s">',
	                        $arrLogo['path'],
	                        $arrLogo['meta'][ $GLOBALS['TL_LANGUAGE'] ]['caption'],
	                        $arrLogo['meta'][ $GLOBALS['TL_LANGUAGE'] ]['title']
	                );
	
	                $this->Template->logo    = $strLogo;
	                $this->Template->arrLogo = $arrLogo;
	            }

                    $entries = array();
                    $entries = $objStores->fetchAllAssoc();

                    if( !empty($entries) ) {

                        foreach( $entries as $entry ) {

                            if( empty($sSearchVal) ) {
                                $entry['distance'] = NULL;
                            }

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

		$this->Template->stores = $aEntries;
	}
}

?>
