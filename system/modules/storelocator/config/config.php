<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    storelocator
 * @license    LGPL
 * @filesource
 */

/**
 * Backend Modules
 */
$GLOBALS['BE_MOD']['content']['storelocator'] = array(
	'tables' 		=> array('tl_storelocator_category', 'tl_storelocator_stores')
,	'icon'   		=> 'system/modules/storelocator/assets/images/icon.gif'
,	'stylesheet'	=> 'system/modules/storelocator/themes/default/backend.css'
,	'importStores'  => array( 'ModuleStoreLocatorImporter', 'showImport' )
);


/**
 * Frontend Modules
 */
$GLOBALS['FE_MOD']['storelocator'] = array(
	'storelocator_search'	=> 'ModuleStoreLocatorSearch'
,	'storelocator_list'		=> 'ModuleStoreLocatorList'
,	'storelocator_details'	=> 'ModuleStoreLocatorDetails'
);


/**
 * Register Hooks
 */
$GLOBALS['TL_HOOKS']['generatePage'][] = array('ModuleStoreLocator', 'addResultsBodyClass');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('ModuleStorelocatorInsertTags', 'replaceInsertTags');

?>