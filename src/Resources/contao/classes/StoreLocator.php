<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
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

        $store->name = StringUtil::restoreBasicEntities($store->name);
        $store->description = StringUtil::restoreBasicEntities($store->description);

        // validate latitude and longitude
        if( !Validator::isNumeric($store->latitude) || !Validator::isNumeric($store->longitude) ) {

            $store->latitude = '';
            $store->longitude = '';

            System::getContainer()->get('monolog.logger.contao.error')->error('Error parsing geocords ('. $store->latitude .','. $store->longitude .') for store ID '.$store->id);
        }

        $store->latitude = floatval($store->latitude);
        $store->longitude = floatval($store->longitude);

        if( $store->latitude < -90 || $store->latitude > 90 || $store->longitude < -180 || $store->longitude > 180 ) {

            $store->latitude = '';
            $store->longitude = '';

            System::getContainer()->get('monolog.logger.contao.error')->error('Error parsing geocords not in range ('. $store->latitude .','. $store->longitude .') for store ID '.$store->id);
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

        // add link to details
        if( $module && $module->jumpTo ) {

            $objLink = null;
            $objLink = PageModel::findById($module->jumpTo);

            if( $objLink ) {
                $store->link = $objLink->getFrontendUrl((!Config::get('useAutoItem')?'/store/':'/').($store->alias?$store->alias:$store->id));
            }
        }

        // add content elements
        $store->elements = [];
        $arrElements = [];
        $elements = ContentModel::findPublishedByPidAndTable($store->id, StoresModel::getTable());

        if( $elements !== null ) {

			while( $elements->next() ) {

                if( version_compare(ContaoCoreBundle::getVersion(),'5.0.0', '>=') ) {

                    $arrElements[] = Controller::getContentElement($elements->id, $module->getModel()->inColumn);

                } else {

                    $arrElements[] = Controller::getContentElement($elements->current(), $module->column);
                }
			}

            $store->elements = $arrElements;
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
     * Find coordinates for given address
     *
     * @param string Street
     * @param string Postal/ZIP Code
     * @param string Name of city
     * @param string 2-letter country code
     * @param string Address string without specific format
     *
     * @return array
     */
    public function getCoordinates( $street=null, $postal=null, $city=null, $country=null, $fullAddress=null ): array {

        trigger_deprecation('numero2/contao-storelocator', '4.3', 'Using StoreLocator::getCoordinates() has been deprecated and will no longer work in Storelocator 5.0. Use the service "numero2_storelocator.util.store_locator" instead.');

        return System::getContainer()->get('numero2_storelocator.util.store_locator')->getCoordinates($street, $postal, $city, $country, $fullAddress, false);
    }


    /**
     * Gets coordinates for an address without a specific format
     *
     * @param string The address
     *
     * @return array
     */
    public function getCoordinatesByString( string $fullAddress=null ): array {

        trigger_deprecation('numero2/contao-storelocator', '4.3', 'Using StoreLocator::getCoordinatesByString() has been deprecated and will no longer work in Storelocator 5.0. Use the service "numero2_storelocator.util.store_locator" instead.');

        return System::getContainer()->get('numero2_storelocator.util.store_locator')->getCoordinatesByString($fullAddress, false);
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

            if( !empty($arrData['longitude']) && !empty($arrData['latitude']) ) {
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
