<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
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

class StorelocatorUpdater extends Controller {


	public function __construct() {
		parent::__construct();
		$this->import('Database');
	}
   
	public function run() {
   
		// make sure that all country codes are lowercase
		$this->Database->prepare("UPDATE tl_storelocator_stores SET country = LOWER(country) WHERE 1;")->execute();
		$this->Database->prepare("UPDATE tl_module SET storelocator_search_country = LOWER(storelocator_search_country) WHERE 1;")->execute();
   }
}

$oSLUpdate = new StorelocatorUpdater();
$oSLUpdate->run(); 

?>