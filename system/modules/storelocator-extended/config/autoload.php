<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Storelocator-extended
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'StoreLocatorCategoryModel'    => 'system/modules/storelocator-extended/models/StoreLocatorCategoryModel.php',

	// Classes
	'StoreLocator'                 => 'system/modules/storelocator-extended/classes/StoreLocator.php',

	// Modules
	'ModuleStoreLocatorImporter'   => 'system/modules/storelocator-extended/modules/ModuleStoreLocatorImporter.php',
	'ModuleStoreLocatorList'       => 'system/modules/storelocator-extended/modules/ModuleStoreLocatorList.php',
	'ModuleStoreLocator'           => 'system/modules/storelocator-extended/modules/ModuleStoreLocator.php',
	'ModuleStorelocatorInsertTags' => 'system/modules/storelocator-extended/modules/ModuleStorelocatorInsertTags.php',
	'ModuleStoreLocatorSearch'     => 'system/modules/storelocator-extended/modules/ModuleStoreLocatorSearch.php',
	'ModuleStoreLocatorDetails'    => 'system/modules/storelocator-extended/modules/ModuleStoreLocatorDetails.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_storelocator_inserttag' => 'system/modules/storelocator-extended/templates',
	'mod_storelocator_search'    => 'system/modules/storelocator-extended/templates',
	'mod_storelocator_details'   => 'system/modules/storelocator-extended/templates',
	'mod_storelocator_list'      => 'system/modules/storelocator-extended/templates',
));
