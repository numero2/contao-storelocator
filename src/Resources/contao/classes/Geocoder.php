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

use Contao\System;
use Exception;
use Geocoder\Provider\Provider;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;


class Geocoder {


    /**
     * @var Psr\Http\Client\ClientInterface
     */
    protected ClientInterface $client;

    /**
     * @var Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var array
     */
    private array $aProviders;


    public function __construct( ClientInterface $client, LoggerInterface $logger ) {

        $this->client = $client;
        $this->logger = $logger;

        $this->aProviders = [];

        foreach( $GLOBALS['N2SL']['geocoder_providers'] as $name => $settings ) {

            if( class_exists($settings['class']) ) {

                $this->aProviders[$name] = null;

                if( is_callable($settings['init_callback']) ) {

                    try {

                        $provider = $settings['init_callback']($this->client);

                        if( $provider && $provider instanceof Provider ) {
                            $this->aProviders[$name] = $provider;
                        }

                    } catch( Exception $e ) {

                        $this->logger->error('Error initializing '.$name.': ' . $e->getMessage());
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

        trigger_deprecation('numero2/contao-storelocator', '4.3', 'Using Geocoder::getInstance() has been deprecated and will no longer work in Storelocator 5.0. Use the service "numero2_storelocator.geocoder" instead.');

        return System::getContainer()->get('numero2_storelocator.geocoder');
    }


    /**
     * Get all available providers
     *
     * @return array
     */
    public function getAvailableProviders(): array {

        return array_keys($this->aProviders);
    }


    /**
     * Checks if the given provider name is available
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProvider( string $name ): bool {

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
     *
     * @param string $name
     *
     * @return Geocoder\Provider\Provider|null
     */
    public function getProvider( string $name ): ?Provider {

        if( $name === null ) {
            return null;
        }

        // TODO return provider with cacheProvider and rate limiter
        if( array_key_exists($name, $this->aProviders) ) {
            return $this->aProviders[$name];
        }

        return null;
    }


    /**
     * Generates a list of all available javascript providers
     *
     * @return array
     */
    public function getJavascriptProviders(): array {

        $aProviders = [];

        foreach( $GLOBALS['N2SL']['javascript_providers'] as $name => $settings ) {

            $isAvailable = $settings['init_callback']();

            if( $isAvailable ) {
                $aProviders[] = $name;
            }
        }

        return $aProviders;
    }
}
