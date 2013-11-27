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
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
		
		$storeID = $this->Input->get('auto_item') ? $this->Input->get('auto_item') : $this->Input->get('store');
		$storeID = substr( $storeID, 0, strpos($storeID,'-') );
		
		$objStore = NULL;
		$objStore = $this->Database->prepare(" SELECT * FROM `tl_storelocator_stores` WHERE `id` = ? ")->limit(1)->execute($storeID);
		
		$entry = NULL;
        
        $GLOBALS['TL_CSS'][] = 'system/modules/storelocator/html/style.css';

		// get store details
		if( $entry = $objStore->fetchAssoc() ) {
			
			// get opening times
			$entry['opening_times'] = unserialize( $entry['opening_times'] );
			$entry['opening_times'] = !empty($entry['opening_times'][0]['from']) ? $entry['opening_times'] : NULL;

			// set country name
			$aCountryNames = $this->getCountries();
			$entry['country_code'] = $entry['country'];
			$entry['country_name'] = $aCountryNames[$entry['country']];
		
			$this->Template->entry = $entry;
			$this->Template->gMap = null;
            
            // generate google map
            if( $entry['latitude'] != '' && $entry['longitude'] != '' ) {
            
                // static map
                if( $this->storelocator_details_maptype == 'static' ) {
                
                    $this->Template->gMap = sprintf(
                        '<img src="http://maps.google.com/maps/api/staticmap?center=%s,%s&amp;zoom=15&amp;size=%sx%s&amp;maptype=roadmap&amp;markers=color:red|label:|%s,%s&amp;sensor=false" alt="Google Maps" />'
                    ,   $entry['latitude']
                    ,   $entry['longitude']
                    ,   400
                    ,   220
                    ,   $entry['latitude']
                    ,   $entry['longitude']
                    );
                
                // dynamic map
                } else {
         
                    $GLOBALS['TL_JAVASCRIPT'][] = 'https://maps.google.com/maps/api/js?sensor=false';
                    $this->Template->gMap = '<div id="map_canvas"></div>'."\n"
                        .'<script type="text/javascript">'."\n"
                        .'  function initSLGMap() {'."\n"
                        .'      var latlng = new google.maps.LatLng('.$entry['latitude'].', '.$entry['longitude'].');'."\n"
                        .'      var options = {'."\n"
                        .'          zoom: 15'."\n"
                        .'      ,   center: latlng'."\n"
                        .'      ,   mapTypeId: google.maps.MapTypeId.ROADMAP'."\n"
                        .'      };'."\n"
                        .'      var map = new google.maps.Map(document.getElementById("map_canvas"),options);'."\n"
                        .'      var marker = new google.maps.Marker({'."\n"
                        .'          position: latlng'."\n"
                        .'      ,   map: map'."\n"
                        .'      ,   title: "'.$entry['name'].'"'."\n"
                        .'      });'."\n"
                        .'  } '."\n"
                        .'  initSLGMap(); '."\n"
                        .'</script>'."\n";
                }
            }
		
		// store not found? throw 404
		} else {
		
			$this->_redirect404();
		}

	}
	
	
	/**
	 * Redirect to 404 page if entry not found
	 */
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