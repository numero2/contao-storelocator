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


namespace numero2\StoreLocatorBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use numero2\StoreLocatorBundle\StoreLocatorBundle;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;


class Plugin implements BundlePluginInterface, ExtensionPluginInterface {


    /**
     * {@inheritdoc}
     */
    public function getBundles( ParserInterface $parser ): array {

        return [
            BundleConfig::create(StoreLocatorBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                ])
        ];
    }


    /**
     * Add a new cache pool
     *
     * @param string $extensionName
     * @param array $extensionConfigs
     * @param Contao\ManagerPlugin\Config\ContainerBuilder $container
     *
     * @return array
     */
    public function getExtensionConfig( $extensionName, array $extensionConfigs, ContainerBuilder $container ) {

        if( $extensionName === 'framework' ) {

            foreach( $extensionConfigs as &$extensionConfig ) {

                if( isset($extensionConfig['cache']) ) {

                    // creates a "contao.store_locator_cache" service
                    // uses the "app" cache configuration
                    $extensionConfig['cache']['pools']['contao.store_locator_cache'] = [
                        'adapter' => 'cache.app'
                    ,   'default_lifetime' => '1 month'
                    ];

                    break;
                }
            }
        }

        return $extensionConfigs;
    }
}
