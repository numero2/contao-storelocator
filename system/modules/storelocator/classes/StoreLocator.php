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


class StoreLocator extends \System
{

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
     * @param string The adress
     * @return array
     */
    public function getCoordinatesByString( $string=NULL ) {
        return $this->getCoordinates(NULL, NULL, NULL, NULL, $string);
    }
}