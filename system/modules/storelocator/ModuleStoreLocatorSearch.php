<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');/** * Contao Open Source CMS * Copyright (C) 2005-2011 Leo Feyer * * Formerly known as TYPOlight Open Source CMS. * * This program is free software: you can redistribute it and/or * modify it under the terms of the GNU Lesser General Public * License as published by the Free Software Foundation, either * version 3 of the License, or (at your option) any later version. *  * This program is distributed in the hope that it will be useful, * but WITHOUT ANY WARRANTY; without even the implied warranty of * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU * Lesser General Public License for more details. *  * You should have received a copy of the GNU Lesser General Public * License along with this program. If not, please visit the Free * Software Foundation website at <http://www.gnu.org/licenses/>. * * PHP version 5 * @copyright  numero2 - Agentur f�r Internetdienstleistungen <www.numero2.de> * @author     Benny Born <benny.born@numero2.de> * @package    StoreLocator * @license    LGPL * @filesource */class ModuleStoreLocatorSearch extends Module {	/**	 * Template	 * @var string	 */	protected $strTemplate = 'mod_storelocator_search';			/**	 * Display a wildcard in the back end	 * @return string	 */	public function generate() {		if( TL_MODE == 'BE' ) {			$objTemplate = new BackendTemplate('be_wildcard');			$objTemplate->wildcard = '### STORELOCATOR SEARCH ###';			$objTemplate->title = $this->headline;			$objTemplate->id = $this->id;			$objTemplate->link = $this->name;			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;			return $objTemplate->parse();		}				return parent::generate();	}			/**	 * Generate module	 */	protected function compile() {		global $objPage;			$this->Template = new FrontendTemplate($this->storelocator_search_tpl);				$this->Template->searchVal = $this->Input->post('storelocator_search_name');		$this->Template->country = $this->storelocator_search_country;		$this->Template->formId = 'tl_storelocator';				// get list of countries		$objCountries = NULL;		$objCountries = $this->Database->execute(" SELECT country FROM tl_storelocator_stores GROUP BY country DESC ");		$this->Template->countries = $objCountries->fetchAllAssoc();		// get form action		$pageID = $this->jumpTo ? $this->jumpTo : $objPage->id;		$objLink = $this->Database->prepare("SELECT * FROM tl_page WHERE id = ?;")->execute($pageID);		$this->Template->action = $this->generateFrontendUrl($objLink->fetchAssoc());	}}?>