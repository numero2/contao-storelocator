<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2019 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2020 numero2 - Agentur für digitales Marketing GbR
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'numero2\StoreLocator',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'numero2\StoreLocator\StoreLocator'                 => 'system/modules/storelocator/classes/StoreLocator.php',
    'numero2\StoreLocator\StoreLocatorBackend'          => 'system/modules/storelocator/classes/StoreLocatorBackend.php',

    // Models
    'numero2\StoreLocator\StoresModel'                  => 'system/modules/storelocator/models/StoresModel.php',
    'numero2\StoreLocator\CategoriesModel'              => 'system/modules/storelocator/models/CategoriesModel.php',

    // Modules
    'numero2\StoreLocator\ModuleStoreLocatorSearch'     => 'system/modules/storelocator/modules/ModuleStoreLocatorSearch.php',
    'numero2\StoreLocator\ModuleStoreLocatorList'       => 'system/modules/storelocator/modules/ModuleStoreLocatorList.php',
    'numero2\StoreLocator\ModuleStoreLocatorFilter'     => 'system/modules/storelocator/modules/ModuleStoreLocatorFilter.php',
    'numero2\StoreLocator\ModuleStoreLocatorDetails'    => 'system/modules/storelocator/modules/ModuleStoreLocatorDetails.php',
    'numero2\StoreLocator\ModuleStoreLocatorImporter'   => 'system/modules/storelocator/modules/ModuleStoreLocatorImporter.php',
    'numero2\StoreLocator\ModuleStoreLocatorStaticMap'   => 'system/modules/storelocator/modules/ModuleStoreLocatorStaticMap.php',

    // Widgets
    'numero2\StoreLocator\OpeningTimes'                 => 'system/modules/storelocator/widgets/OpeningTimes.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'mod_storelocator_details'         => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_inserttag'       => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_list'            => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_filter'          => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_infowindow'      => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_search'          => 'system/modules/storelocator/templates/modules',
    'script_storelocator_autocomplete' => 'system/modules/storelocator/templates/modules',
    'script_storelocator_googlemap'    => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_static_map'      => 'system/modules/storelocator/templates/modules',
));
