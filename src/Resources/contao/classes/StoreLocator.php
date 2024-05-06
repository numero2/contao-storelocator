<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\Controller;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use Geocoder\Query\GeocodeQuery;
use numero2\StoreLocator\DCAHelper\Stores;


class StoreLocator {


    /**
     * Replace matching Insert Tags
     *
     * @param string $strBuffer
     * @param bool $blnCache
     *
     * @return string|boolean
     */
    public function replaceInsertTags( $strBuffer, $blnCache=false ) {

        Controller::loadDataContainer(StoresModel::getTable());

        $aParams = [];
        $aParams = explode('::', $strBuffer);

        switch( $aParams[0] ) {

            case 'store' :

                $aDCAFields = [];
                $aDCAFields = array_keys($GLOBALS['TL_DCA'][StoresModel::getTable()]['fields']);

                // get data from current store
                if( !empty($aParams[1]) && in_array($aParams[1], $aDCAFields) ) {

                    $alias = null;
                    $alias = Input::get('auto_item') ? Input::get('auto_item') : Input::get('store');

                    // find store
                    $objStore = null;
                    $objStore = StoresModel::findByIdOrAlias($alias);

                    if( !$objStore ) {
                        return false;
                    }

                    self::parseStoreData($objStore);

                    return $objStore->{$aParams[1]};

                // get specific store
                } else {

                    $oTemplate = new FrontendTemplate('mod_storelocator_inserttag');

                    // find store
                    $objStore = null;
                    $objStore = StoresModel::findByIdOrAlias($aParams[1]);

                    if( !$objStore ) {
                        return false;
                    }

                    self::parseStoreData($objStore);

                    $oTemplate->store = $objStore;

                    $sTemplate = $oTemplate->parse();
                    $sTemplate = Controller::replaceInsertTags($sTemplate);

                    return $sTemplate;
                }

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
     * @param numero2\StoreLocator\StoresModel $store
     * @param Contao\Module $module
     */
    public static function parseStoreData( StoresModel $store, ?Module $module=null ): void {

        // validate latitude and longitude
        if( !Validator::isNumeric($store->latitude) || !Validator::isNumeric($store->longitude) ) {
            if( System::getContainer()->has('monolog.logger.contao.error') ) {
                System::getContainer()->get('monolog.logger.contao.error')->error('Error parsing geocords ('. $store->latitude .','. $store->longitude .') for store ID '.$store->id);
            } else {
                System::log('Error parsing geocords ('. $store->latitude .','. $store->longitude .') for store ID '.$store->id, __METHOD__, TL_ERROR);
            }
        }

        $store->latitude = floatval($store->latitude);
        $store->longitude = floatval($store->longitude);

        if( $store->latitude < -90 || $store->latitude > 90 || $store->longitude < -180 || $store->longitude > 180 ) {
            if( System::getContainer()->has('monolog.logger.contao.error') ) {
                System::getContainer()->get('monolog.logger.contao.error')->error('Error parsing geocords not in range ('. $store->latitude .','. $store->longitude .') for store ID '.$store->id);
            } else {
                System::log('Error geocords not in range ('. $store->latitude .','. $store->longitude .') for store ID '.$store->id, __METHOD__, TL_ERROR);
            }
        }

        // get opening times
        $aTimes = StringUtil::deserialize( $store->opening_times );
        $aTimes = !empty($aTimes[0]['from']) ? $aTimes : null;

        if( !empty($aTimes) ) {

            $aWeekdays = [];
            $aWeekdays = StoreLocator::getWeekdays();

            foreach( $aTimes as $i => $day ) {
                $aTimes[$i]['label'] = $aWeekdays[ $day['weekday'] ];
            }
        }

        $store->opening_times = $aTimes;

        // set country name
        $aCountryNames = [];
        $aCountryNames = Stores::getCountries();

        $store->country_code = $store->country;
        $store->country_name = $aCountryNames[ $store->country ];

        // create a clickable link for telephone number
        if( !empty($store->phone) ) {
            $store->phoneLink = 'tel:'.preg_replace("|[^\+0-9]|", "", $store->phone);
        }

        // create a clickable link for fax number
        if( !empty($store->fax) ) {
            $store->faxLink = 'fax:'.preg_replace("|[^\+0-9]|", "", $store->fax);
        }

        // create a "pretty" url
        if( !empty($store->url) ) {

            $aURL = [];
            $aURL = parse_url($store->url);

            if( !empty($aURL['host']) ) {
                $store->prettyUrl = $aURL['host'];
            }
        }

        // HOOK: add custom logic to parse the store details
        if( isset($GLOBALS['N2SL_HOOKS']['parseStoreData']) && is_array($GLOBALS['N2SL_HOOKS']['parseStoreData']) ) {

            foreach( $GLOBALS['N2SL_HOOKS']['parseStoreData'] as $callback ) {

                if( is_array($callback) ) {
                    System::importStatic($callback[0])->{$callback[1]}($store, $module);
                }
            }
        }
    }


    /**
     * Returns a list of weekdays
     *
     * @return array
     */
    public static function getWeekdays(): array {

        return [
            'MO' => &$GLOBALS['TL_LANG']['DAYS'][1]
        ,   'TU' => &$GLOBALS['TL_LANG']['DAYS'][2]
        ,   'WE' => &$GLOBALS['TL_LANG']['DAYS'][3]
        ,   'TH' => &$GLOBALS['TL_LANG']['DAYS'][4]
        ,   'FR' => &$GLOBALS['TL_LANG']['DAYS'][5]
        ,   'SA' => &$GLOBALS['TL_LANG']['DAYS'][6]
        ,   'SU' => &$GLOBALS['TL_LANG']['DAYS'][0]
        ];
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
    public function getCoordinates( $street=null, $postal=null, $city=null, $country=null, $fullAdress=null ): array {

        // find coordinates using configured geo providers
        $sQuery = sprintf(
            "%s %s %s %s"
        ,   $street
        ,   $postal
        ,   $city
        ,   $country
        );

        $sQuery = $fullAdress ? $fullAdress : $sQuery;

        $oGeo = Geocoder::getInstance();
        $oResults = null;

        $aProviderNames = $oGeo->getAvailableProviders();
        if( !empty($aProviderNames) ) {

            foreach( $aProviderNames as $name ) {

                $oResults = null;

                try {
                    $provider = $oGeo->getProvider($name);

                    if( $provider ) {
                        $oResults = $provider->geocodeQuery(GeocodeQuery::create($sQuery));
                    }
                } catch( \Exception $e ) {

                    if( System::getContainer()->has('monolog.logger.contao.error') ) {
                        System::getContainer()->get('monolog.logger.contao.error')->error('Error query geocode with '.$name.': ' . $e->getMessage());
                    } else {
                        System::log('Error query geocode with '.$name.': ' . $e->getMessage(), __METHOD__, TL_ERROR);
                    }
                }

                if( $oResults ) {
                    break;
                }
            }
        }


        if( $oResults && !$oResults->isEmpty() ) {

            $aCoords = [];
            $oCoords = $oResults->first()->getCoordinates();

            $aCoords['latitude'] = $oCoords->getLatitude();
            $aCoords['longitude'] = $oCoords->getLongitude();

            return $aCoords;
        }


        if( System::getContainer()->has('monolog.logger.contao.error') ) {
            System::getContainer()->get('monolog.logger.contao.error')->error('Could not find coordinates for adress "'.$sQuery.'", maybe no geoprovider configured');
        } else {
            System::log('Could not find coordinates for adress "'.$sQuery.'", maybe no geoprovider configured', __METHOD__, TL_ERROR);
        }
        return [];
    }


    /**
     * Gets coordinates for an adress without a specific format
     *
     * @param string The adress
     *
     * @return array
     */
    public function getCoordinatesByString( string $fullAdress=null ): array {
        return $this->getCoordinates(null, null, null, null, $fullAdress);
    }


    /**
     * Parses the given search value into its components.
     *
     * @param string The generated search string
     *
     * @return array
     */
    public static function parseSearchValue( $searchVal=null ): array {

        if( !$searchVal ) {
            return [];
        }

        if( strpos($searchVal, ";") !== false ) {
            $searchVal = explode(";", $searchVal);
        }

        $ret = [];

        if( is_array($searchVal) ) {

            if( count($searchVal) == 3 ) {

                $ret['filter'] = $searchVal[0];
                $ret['order'] = $searchVal[1];
                $ret['sort'] = $searchVal[2];

            } else if( count($searchVal) == 4 ) {

                $ret['filter'] = $searchVal[0];
                $ret['order'] = $searchVal[1];
                $ret['sort'] = $searchVal[2];
                $ret['tags'] = $searchVal[3];

            } else {

                if( !empty($searchVal[0]) ) $ret['term'] = $searchVal[0];
                if( !empty($searchVal[1]) ) $ret['category'] = $searchVal[1];
                if( !empty($searchVal[2]) ) $ret['longitude'] = $searchVal[2];
                if( !empty($searchVal[3]) ) $ret['latitude'] = $searchVal[3];

                if( !empty($searchVal[4]) ) $ret['filter'] = $searchVal[4];
                if( !empty($searchVal[5]) ) $ret['order'] = $searchVal[5];
                if( !empty($searchVal[6]) ) $ret['sort'] = $searchVal[6];
                if( !empty($searchVal[7]) ) $ret['tags'] = $searchVal[7];
            }

        } else {

            $ret['term'] = $searchVal;
        }

        foreach( $ret as $i => $d ) {
            $ret[$i] = urldecode($d);
        }

        return $ret;
    }


    /**
     * Generates the search string
     *
     * @param array $arrData
     *
     * @return string
     */
    public static function generateSearchValue( $arrData ): string {

        if( !is_array($arrData) ) {
            return '';
        }

        $aData = [];

        if( !empty($arrData['term']) ) {

            $aData[0] = html_entity_decode($arrData['term']);

            if( !empty($arrData['category']) ) {
                $aData[1] = $arrData['category'];
            }

            if( $arrData['longitude'] && $arrData['latitude'] ) {
                $aData[1] = !empty($aData[1])?$aData[1]:'';
                $aData[2] = $arrData['longitude'];
                $aData[3] = $arrData['latitude'];
                $aData[4] = '';
            }
        }

        if( !empty($arrData['filter']) || !empty($arrData['order']) || !empty($arrData['sort']) || !empty($arrData['tags']) ) {

            if( count($aData) == 0 ) {

                $aData[0] = $arrData['filter']??'';
                $aData[1] = $arrData['order']??'';
                $aData[2] = $arrData['sort']??'';

                if( !empty($arrData['tags']) ) {
                    $aData[3] = $arrData['tags'];
                }

            } else {

                $aData[0] = $aData[0]??'';
                $aData[1] = $aData[1]??'';
                $aData[2] = $aData[2]??'';
                $aData[3] = $aData[3]??'';
                $aData[4] = $arrData['filter']??'';
                $aData[5] = $arrData['order']??'';
                $aData[6] = $arrData['sort']??'';
                $aData[7] = $arrData['tags']??'';
            }
        }

        foreach( $aData as $i => $d ) {
            $aData[$i] = urlencode($d);
        }

        $strData = ( count($aData) > 1 ) ? implode(';',$aData) : ($aData[0]??'');

        return $strData;
    }


    /**
     * Create filter where from value and field list and an optional tag id
     *
     * @param string $value
     * @param array $fields
     * @param string $tagId
     *
     * @return string
     */
    public static function createFilterWhereClause( string $searchValue, array $fields, ?string $tagId=null ): string {

        $ret = [];

        if( !empty($searchValue) ) {
            foreach( $fields as $key => $field ) {
                $ret[] = $field." LIKE '%%".$searchValue."%%'";
            }
        }

        if( !empty($ret) ) {
            $ret = '('.implode(" OR ", $ret).')';
        } else {
            $ret = '';
        }

        if( !empty($tagId) ) {

            if( strlen($ret) ) {
                $ret .= "AND ";
            }

            $ret .= "id IN ( SELECT s.id
                FROM tl_storelocator_stores AS s
                JOIN tl_tags_rel as r on (r.pid = s.id AND r.ptable = 'tl_storelocator_stores' AND r.field = 'tags' AND r.tag_id = $tagId)
            )";
        }

        return $ret;
    }
}
