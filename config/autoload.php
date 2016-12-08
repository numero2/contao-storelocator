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
    'numero2\StoreLocator\StoreLocator'      	        => 'system/modules/storelocator/classes/StoreLocator.php',
	'numero2\StoreLocator\StoreLocatorBackend' 	        => 'system/modules/storelocator/classes/StoreLocatorBackend.php',

	// Modules
    'numero2\StoreLocator\ModuleStoreLocatorSearch'     => 'system/modules/storelocator/modules/ModuleStoreLocatorSearch.php',
    'numero2\StoreLocator\ModuleStoreLocatorList'       => 'system/modules/storelocator/modules/ModuleStoreLocatorList.php',
    'numero2\StoreLocator\ModuleStoreLocatorDetails'    => 'system/modules/storelocator/modules/ModuleStoreLocatorDetails.php',
    'numero2\StoreLocator\ModuleStoreLocatorImporter'   => 'system/modules/storelocator/modules/ModuleStoreLocatorImporter.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'mod_storelocator_details'      => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_inserttag'    => 'system/modules/storelocator/templates/modules',
    'mod_storelocator_list'         => 'system/modules/storelocator/templates/modules',
	'mod_storelocator_search'       => 'system/modules/storelocator/templates/modules',
));