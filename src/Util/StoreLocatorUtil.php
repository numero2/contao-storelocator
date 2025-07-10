<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\Util;

use Contao\Config;
use Contao\Controller;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\SystemUtil;
use Contao\Validator;
use Geocoder\Query\GeocodeQuery;
use numero2\StoreLocator\GeoCoder;
use numero2\StoreLocator\DCAHelper\Stores;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;


class StoreLocatorUtil {


    const GEO_REQUEST_CACHE_PREFIX = 'geo_request_cache_';
    const STATIC_MAP_CACHE_PREFIX = 'static_map_cache_';


    /**
     * @var numero2\StoreLocator\Geocoder
     */
    protected Geocoder $geocoder;

    /**
     * @var Symfony\Contracts\Cache\CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;


    public function __construct( Geocoder $geocoder, CacheInterface $cache, LoggerInterface $logger ) {

        $this->geocoder = $geocoder;
        $this->cache = $cache;
        $this->logger = $logger;
    }


    /**
     * Find coordinates for given address
     *
     * @param string Street
     * @param string Postal/ZIP Code
     * @param string Name of city
     * @param string 2-letter country code
     * @param string Address string without specific format
     * @param bool query for the information uncached and update the cache
     *
     * @return array
     */
    public function getCoordinates( $street=null, $postal=null, $city=null, $country=null, $fullAddress=null, $blnUncached=true ): array {

        // find coordinates using configured geo providers
        $sQuery = sprintf(
            "%s %s %s %s"
        ,   $street
        ,   $postal
        ,   $city
        ,   $country
      );


        $sQuery = $fullAddress ? $fullAddress : $sQuery;
        $sQuery = \trim($sQuery);

        if( !\strlen($sQuery) ) {
            return [];
        }

        $cacheKey = self::GEO_REQUEST_CACHE_PREFIX.md5($sQuery);
        $cachedData = $this->cache->getItem($cacheKey);

        if( !$cachedData->isHit() ) {

            $oResults = null;

            $aProviderNames = $this->geocoder->getAvailableProviders();

            if( !empty($aProviderNames) ) {

                foreach( $aProviderNames as $name ) {

                    $oResults = null;

                    try {
                        $provider = $this->geocoder->getProvider($name);

                        if( $provider ) {
                            $oResults = $provider->geocodeQuery(GeocodeQuery::create($sQuery));
                        }
                    } catch( Exception $e ) {

                        $this->logger->error('Error query geocode with '.$name.': ' . $e->getMessage());
                    }

                    if( $oResults ) {
                        break;
                    }
                }
            }

            if( $oResults && !$oResults->isEmpty() ) {

                $aCoords = [];
                $oCoords = $oResults->first()->getCoordinates();

                $aCoords['query'] = $sQuery;
                $aCoords['latitude'] = $oCoords->getLatitude();
                $aCoords['longitude'] = $oCoords->getLongitude();

                $cachedData->set($aCoords);
                $this->cache->save($cachedData);

            } else {

                if( empty($aProviderNames) ) {

                    $this->logger->error('Could not find coordinates for address "'.$sQuery.'", no geoprovider configured');

                } else {

                    $this->logger->error('Could not find coordinates for address "'.$sQuery.'"');
                }

                return [];
            }
        }

        return $cachedData->get();
    }


    /**
     * Gets coordinates for an address without a specific format
     *
     * @param string The address
     * @param bool query for the information uncached and update the cache
     *
     * @return array
     */
    public function getCoordinatesByString( string $fullAddress=null, bool $blnUncached=true ): array {

        return $this->getCoordinates(null, null, null, null, $fullAddress, $blnUncached);
    }
}
