<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   Software Licenses
 * @author    Benny Born <benny.born@numero2.de>
 * @license   StoreLocator
 * @copyright 2015 numero2 - Agentur für Internetdienstleistungen
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
	protected function replaceInsertTags($strBuffer, $blnCache=false) {

		$this->import('Database');

        $aParams = explode('::', $strBuffer);

        switch( $aParams[0] ) {

            case 'store' :

				$this->Template = new FrontendTemplate('mod_storelocator_inserttag');

				// find store
				$objStore = NULL;
				$objStore = $this->Database->prepare("SELECT * FROM `tl_storelocator_stores` WHERE `id` = ? ")->limit(1)->execute( $aParams[1] );

				$entry = NULL;
				$entry = $objStore->fetchAssoc();

				// get opening times
				$entry['opening_times'] = unserialize( $entry['opening_times'] );
				$entry['opening_times'] = !empty($entry['opening_times'][0]['from']) ? $entry['opening_times'] : NULL;

				// set country name
				$aCountryNames = $this->getCountries();
				$entry['country_code'] = $entry['country'];
				$entry['country_name'] = $aCountryNames[$entry['country']];

				if( !$objStore )
					return false;

				$this->Template->entry = $entry;

				$sTemplate = $this->Template->parse();
				$sTemplate = Controller::replaceInsertTags($sTemplate);

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

        $oRequest = NULL;
        $oRequest = new \Request();

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
     *
     * @param string The adress
     * 
     * @return array
     */
    public function getCoordinatesByString( $string=NULL ) {
        return $this->getCoordinates(NULL, NULL, NULL, NULL, $string);
    }
}