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
		
		$sSearchVal = $this->Input->post('storelocator_search_name') ? $this->Input->post('storelocator_search_name') : NULL;
		$sSearchCountry = $this->Input->post('storelocator_search_country') ? $this->Input->post('storelocator_search_country') : NULL;

		// add country code for correct search results
		if( !empty($sSearchCountry) ) {
			$sSearchVal .= ', '.$sSearchCountry;
		}
		
		$aCategories = array();
		$aCategories = deserialize($this->storelocator_list_categories);
		
		$aEntries = array();
		
		if( !empty($sSearchVal) ) {
		
			// get coordinates of searched destination
			$aCoordinates = array();
			
			$sResponse = NULL;
			$sResponse = file_get_contents("http://maps.google.com/maps/geo?q=".rawurlencode($sSearchVal)."&output=json&oe=utf8&sensor=false&hl=de");
			
			if( !empty($sResponse) ) {
			
				$aResponse = array();
				$aResponse = json_decode($sResponse,1);

				if( !empty($aResponse['Status']) && $aResponse['Status']['code'] == '200' ) {
				
					$aCoordinates['latitude'] = $aResponse['Placemark'][0]['Point']['coordinates'][1];
					$aCoordinates['longitude'] = $aResponse['Placemark'][0]['Point']['coordinates'][0];
				}
			}
		
			if( !empty($aCoordinates) ) {

				$objStores = NULL;				
				$objStores = $this->Database->prepare("
					SELECT
						*
					, 3956 * 2 * ASIN(SQRT( POWER(SIN((? -abs(latitude)) * pi()/180 / 2),2) + COS(? * pi()/180 ) * COS( abs(latitude) *  pi()/180) * POWER(SIN((?-longitude) *  pi()/180 / 2), 2) )) AS `distance`
					FROM `tl_storelocator_stores`
					WHERE
							pid IN(".implode(',',$aCategories).")
						AND latitude != '' 
						AND longitude != '' 
					ORDER BY `distance` ASC
				")->limit($this->storelocator_list_limit)->execute(
					$aCoordinates['latitude']
				,	$aCoordinates['latitude']
				,	$aCoordinates['longitude']
				);
				
				while( $objStores->next() ) {
				
					// get opening times
					$times = unserialize($objStores->opening_times);
					$times = !empty($times[0]['from']) ? $times : NULL;
				
					$aEntries[] = array(
						'name'			=> str_replace('&','&amp;',$objStores->name)
					,   'email'			=> $objStores->email
					,   'url'			=> $objStores->url
					,   'phone'			=> $objStores->phone
					,   'fax'			=> $objStores->fax
					,   'street'		=> $objStores->street
					,   'postal'		=> $objStores->postal
					,   'city'			=> $objStores->city
					,   'country_code'	=> $objStores->country
					,   'country_name'	=> $GLOBALS['TL_LANG']['tl_storelocator']['countries'][$objStores->country]
					,   'distance'		=> $objStores->distance
					,	'opening_times'	=> $times
					);
				}
			}
		}
		
		$this->Template->entries = $aEntries;
	}
}

?>