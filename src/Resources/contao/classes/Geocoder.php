<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2021 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2021 numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\System;
use Contao\Config;
use Http\Discovery\HttpClientDiscovery;
use Geocoder\Provider\Provider;


class Geocoder extends System {


    protected static $oInstance = null;


    private $aProviders;


    private function __construct() {

        $httpClient = HttpClientDiscovery::find();

        $this->aProviders = [];

        foreach( $GLOBALS['N2SL']['geocoder_providers'] as $name => $settings ) {
            if( class_exists($settings['class']) ) {

                $this->aProviders[$name] = null;

                if( is_callable($settings['init_callback'])) {

                    try {

                        $provider = $settings['init_callback']($httpClient);

                        if( $provider && $provider instanceof Provider ) {
                            $this->aProviders[$name] = $provider;
                        }

                    } catch( \Geocoder\Exception\InvalidCredentials $e ) {

                    } catch( \Exception $e ) {
                        System::log('Error initialize '.$name.': ' . $e->getMessage(), __METHOD__, TL_ERROR);
                    }
                }
            }
        }
    }


    /**
     * Return the one instance of this singleton
     *
     * @return numero2\StoreLocator\Geocoder
     */
    public static function getInstance(): Geocoder {

        if( self::$oInstance === null ) {

            self::$oInstance = new self();
        }

        return self::$oInstance;
    }


    /**
     * Get all available providers
     *
     * @return array
     */
    public function getAvailableProvider(): array {
        return array_keys($this->aProviders);
    }



    /**
     * Checks if the given provider name is available
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProvider( $name ): bool {

        if( !$name ) {
            return false;
        }

        if( array_key_exists($name, $this->aProviders) ) {
            return true;
        }

        return false;
    }


    /**
     * Get a provider by name
     * @param  [type] $name
     * @return [type]
     */
    public function getProvider( $name ): ?Provider {

        if( $name === null ) {
            return null;
        }


        // TODO return provider with cacheProvider and rate limiter
        if( array_key_exists($name, $this->aProviders) ) {
            return $this->aProviders[$name];
        }

        return null;
    }
}