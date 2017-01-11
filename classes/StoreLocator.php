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


class StoreLocator extends \System {


    /**
	 * Replace matching inserttags
	 *
	 * @param string InsertTag
	 * @param bool Use cache
	 *
	 * @return string
	 */
	public function replaceInsertTags( $strBuffer, $blnCache=false ) {

        $aParams = array();
        $aParams = explode('::', $strBuffer);

        switch( $aParams[0] ) {

            case 'store' :

				$this->Template = new \FrontendTemplate('mod_storelocator_inserttag');

				// find store
				$objStore = NULL;
				$objStore = StoresModel::findByIdOrAlias($aParams[1]);

                if( !$objStore ) {
                    return false;
                }

                self::parseStoreData( $objStore );

				$this->Template->store = $objStore;

				$sTemplate = $this->Template->parse();
				$sTemplate = \Controller::replaceInsertTags($sTemplate);

				return $sTemplate;

            break;

            // not our insert tag?
            default :
                return false;
            break;
        }

        return false;
    }


    /**
     * Parses the given store so we can use it directly to
     * display the details template
     *
     * @param  StoresModel    $store
     *
     * @return none
     */
    public static function parseStoreData( StoresModel &$store ) {

        // get opening times
        $aTimes = unserialize( $store->opening_times );
        $aTimes = !empty($aTimes[0]['from']) ? $aTimes : NULL;

        if( !empty($aTimes) ) {

            $aWeekdays = array();
            $aWeekdays = StoreLocator::getWeekdays();

            foreach( $aTimes as $i => $day ) {
                $aTimes[$i]['label'] = $aWeekdays[ $day['weekday'] ];
            }
        }

        $store->opening_times = $aTimes;

        // set country name
        $aCountryNames = array();
        $aCountryNames = \System::getCountries();

        $store->country_code = $store->country;
        $store->country_name = $aCountryNames[ $store->country ];
    }


    /**
     * Returns a list of weekdays
     *
     * @return array
     */
    public static function getWeekdays() {

        return array(
            'MO' => &$GLOBALS['TL_LANG']['DAYS'][1]
        ,   'TU' => &$GLOBALS['TL_LANG']['DAYS'][2]
        ,   'WE' => &$GLOBALS['TL_LANG']['DAYS'][3]
        ,   'TH' => &$GLOBALS['TL_LANG']['DAYS'][4]
        ,   'FR' => &$GLOBALS['TL_LANG']['DAYS'][5]
        ,   'SA' => &$GLOBALS['TL_LANG']['DAYS'][6]
        ,   'SU' => &$GLOBALS['TL_LANG']['DAYS'][0]
        );
    }


    /**
     * Find coordinates for given adress
     *
     * @param string Street
     * @param string Postal/ZIP Code
     * @param string Name of city
     * @param string 2-letter country code
     * @param string Adress string without specific format
     *
     * @return array
     */
    public function getCoordinates( $street=NULL, $postal=NULL, $city=NULL, $country=NULL, $fullAdress=NULL ) {

        // find coordinates using google maps api
        $sQuery = sprintf(
            "%s %s %s %s"
        ,   $street
        ,   $postal
        ,   $city
        ,   $country
        );

        $sQuery = $fullAdress ? $fullAdress : $sQuery;

        $apiKey = \Config::get('google_maps_server_key');

        $oRequest = NULL;
        $oRequest = new \Request();

        $oRequest->send("https://maps.googleapis.com/maps/api/geocode/json?address=".rawurlencode($sQuery)."&key=".$apiKey."&language=de");

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

                $hasError = true;

                // TODO: Find alternative geocoding service
            }

        } else {
            $hasError = true;
        }

        if( $hasError ) {
            $this->log('Could not find coordinates for adress "'.$sQuery.'"', 'StoreLocator getCoordinates()', TL_ERROR);
        }

        return false;
    }


    /**
     * Gets coordinates for an adress without a specific format
     *
     * @param string The adress
     *
     * @return array
     */
    public function getCoordinatesByString( $string=NULL ) {
        return $this->getCoordinates(NULL, NULL, NULL, NULL, $string);
    }


    /**
     * Parses the given search value into its components.
     *
     * @param string The generated search string
     *
     * @return array
     */
    public function parseSearchValue( $searchVal=NULL ) {

        if( !$searchVal ) {
            return null;
        }

		if( strpos($searchVal, ";") !== false ) {
			$searchVal = explode(";", $searchVal);
		}

        $ret['term'] = array();

        if( is_array($searchVal) ) {

            $ret['term'] = $searchVal[0];

            if( count($searchVal) == 3 ) {

                $ret['longitude'] = $searchVal[1];
                $ret['latitude'] = $searchVal[2];

            } else if( count($searchVal) == 4 ) {

                $ret['category'] = $searchVal[1];
                $ret['longitude'] = $searchVal[2];
                $ret['latitude'] = $searchVal[3];

            } else if( count($searchVal) == 2 ) {

                $ret['category'] = $searchVal[1];
            }

        } else{

            $ret['term'] = $searchVal;
        }

        return $ret;
    }
}