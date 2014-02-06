<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Storelocator
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'StoreLocator'                 => 'system/modules/storelocator/classes/StoreLocator.php',

	// Models
	'StoreLocatorCategoryModel'    => 'system/modules/storelocator/models/StoreLocatorCategoryModel.php',

	// Modules
	'ModuleStoreLocatorSearch'     => 'system/modules/storelocator/modules/ModuleStoreLocatorSearch.php',
	'ModuleStoreLocatorList'       => 'system/modules/storelocator/modules/ModuleStoreLocatorList.php',
	'ModuleStoreLocatorImporter'   => 'system/modules/storelocator/modules/ModuleStoreLocatorImporter.php',
	'ModuleStoreLocator'           => 'system/modules/storelocator/modules/ModuleStoreLocator.php',
	'ModuleStorelocatorInsertTags' => 'system/modules/storelocator/modules/ModuleStorelocatorInsertTags.php',
	'ModuleStoreLocatorDetails'    => 'system/modules/storelocator/modules/ModuleStoreLocatorDetails.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_storelocator_inserttag' => 'system/modules/storelocator/templates',
	'mod_storelocator_list'      => 'system/modules/storelocator/templates',
	'mod_storelocator_search'    => 'system/modules/storelocator/templates',
	'mod_storelocator_details'   => 'system/modules/storelocator/templates',
));
