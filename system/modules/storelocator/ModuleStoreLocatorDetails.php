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


class ModuleStoreLocatorDetails extends Module {

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_storelocator_details';
	
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate() {

		if( TL_MODE == 'BE' ) {

			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### STORELOCATOR DETAILS ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		return parent::generate();
	}
	
	
	/**
	 * Generate module
	 */
	protected function compile() {

		$this->Template = new FrontendTemplate($this->storelocator_details_tpl);
		
		$storeID = $this->Input->get('auto_item') ? $this->Input->get('auto_item') : $this->Input->get('store');
		$storeID = substr( $storeID, 0, strpos($storeID,'-') );
		
		$objStore = NULL;
		$objStore = $this->Database->prepare(" SELECT * FROM `tl_storelocator_stores` WHERE `id` = ? ")->limit(1)->execute($storeID);
		
		// get store details
		if( $objStore->next() ) {
		
			$entry = NULL;
			$entry = $objStore->fetchAllAssoc();
			$entry = $entry[0];
			
			// get opening times
			$entry['opening_times'] = unserialize( $entry['opening_times'] );
			$entry['opening_times'] = !empty($entry['opening_times'][0]['from']) ? $entry['opening_times'] : NULL;
		
			$this->Template->entry = $entry;
		
		// store not found? throw 404
		} else {
		
			$this->_redirect404();
		}

	}
	
	private function _redirect404() {
	
		$obj404 = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE type='error_404' AND published=1 AND pid=?")->limit(1)->execute($this->getRootIdFromUrl());
		$a404 = $obj404->fetchAssoc();

		if( !empty($a404) ) {
		
			$this->redirect( $this->generateFrontendUrl($a404), 404);            
			return;

		} else {
		
			header('HTTP/1.1 404 Not Found');
			die('Page not found');
		}
	}
}

?>