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

 
class StoreLocator extends System {
  

	/**
	 * Find coordinates for given adress
	 * @param string Street
	 * @param string Postal/ZIP Code
	 * @param string Name of city
	 * @param string 2-letter country code
	 * @param string Adress string without specific format
	 * @return array
	 */
	public function getCoordinates( $street=NULL, $postal=NULL, $city=NULL, $country=NULL, $fullAdress=NULL ) {
	
		// find coordinates using google maps api
		$sQuery = sprintf(
			"%s %s %s %s"
		,	$street
		,	$postal
		,	$city
		,	$country
		);
		
		$sQuery = $fullAdress ? $fullAdress : $sQuery;

		$oRequest = NULL;
		$oRequest = new Request();

		$oRequest->send("http://maps.googleapis.com/maps/api/geocode/json?address=".rawurlencode($sQuery)."&sensor=false&language=de");
		
		$hasError = false;
		
		if( $oRequest->code == 200 ) {
		
			$aResponse = array();
			$aResponse = json_decode( $oRequest->response,1 );

			if( !empty($aResponse['status']) && $aResponse['status'] == 'OK' ) {
			
				$coords = array();
				$coords['latitude'] = $aResponse['results'][0]['geometry']['location']['lat'];
				$coords['longitude'] = $aResponse['results'][0]['geometry']['location']['lng'];
				
				return $coords;

			} else {
			
				// try alternative api if google blocked us
				$oRequest->send("http://maps.google.com/maps/geo?q=".rawurlencode($sQuery)."&output=json&oe=utf8&sensor=false&hl=de");
				
				if( $oRequest->code == 200 ) {
				
					$aResponse = array();
					$aResponse = json_decode( $oRequest->response,1 );
					
					if( !empty($aResponse['Status']) && $aResponse['Status']['code'] == 200 ) {
					
						$coords = array();
						$coords['latitude'] = $aResponse['Placemark'][0]['Point']['coordinates'][1];
						$coords['longitude'] = $aResponse['Placemark'][0]['Point']['coordinates'][0];
						
						return $coords;

					} else {
						$hasError = true;	
					}
				
				} else {
					$hasError = true;	
				}
			}
			
		} else {
			$hasError = true;
		}
		
		if( $hasError )
			$this->log('Could not find coordinates for adress "'.$sQuery.'"', 'StoreLocator getCoordinates()', TL_ERROR);
		
		return false;
	}
	
	
	/**
	 * Gets coordinates for an adress without a specific format
	 * @param string The adress
	 * @return array
	 */
	public function getCoordinatesByString( $string=NULL ) {
		return $this->getCoordinates(NULL, NULL, NULL, NULL, $string);
	}
}
?>