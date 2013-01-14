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
 * Table tl_storelocator_stores
 */
$GLOBALS['TL_DCA']['tl_storelocator_stores'] = array(

	'config' => array (
		'dataContainer'               => 'Table'
	,	'ptable'                      => 'tl_storelocator_category'
	,	'onsubmit_callback'   	  	  => array(
			array('tl_storelocator_stores', 'fillCoordinates')
		)
	)
,	'list' => array(
		'sorting' => array (
			'mode'                    => 4
		,	'fields'                  => array('city')
		,	'flag'                    => 1
		,	'headerFields'            => array('title')
		,	'panelLayout'             => 'search,limit'
		,	'child_record_callback'   => array('tl_storelocator_stores', 'listStores')
		)
	,	'global_operations' => array(
			'all' => array(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all']
			,	'href'                => 'act=select'
			,	'class'               => 'header_edit_all'
			,	'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		,	'importStores' => array (
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['importStores']
			,	'href'                => 'key=importStores'
			,	'class'               => 'header_stores_import'
			,	'attributes'          => 'onclick="Backend.getScrollOffset()"'
			)
		)
	,	'operations' => array(
            'edit' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['edit']
			,	'href'                => 'act=edit'
			,	'icon'                => 'edit.gif'
            )
		,	'copy' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['copy']
			,	'href'                => 'act=copy'
			,	'icon'                => 'copy.gif'
            )
		,	'delete' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['delete']
			,	'href'                => 'act=delete'
			,	'icon'                => 'delete.gif'
			,	'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            )
		,	'show' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['show']
			,	'href'                => 'act=show'
			,	'icon'                => 'show.gif'
            )
        )
	)
,	'palettes' => array(
		'default'                     => '{common_legend},name,email,url,phone,fax;{adress_legend},street,postal,city,country;{times_legend},opening_times;{geo_legend},geo_explain,longitude,map,latitude;'
	)
,	'fields' => array(
        'name' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['name']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50' )
        )
	,	'email' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['email']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'email ', 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'url' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['url']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'url ', 'maxlength'=>255, 'tl_class'=>'w50')
		,	'save_callback' 		  => array( array('tl_storelocator_stores', 'checkURL') )
        )
	,	'phone' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['phone']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'phone ', 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'fax' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['fax']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'phone ', 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'street' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['street']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'postal' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['postal']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'city' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['city']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'country' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['country']
		,	'inputType'               => 'select'
		,	'options_callback'        => array('tl_storelocator_stores', 'getCountries')
		,	'default'				  => 'de'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50', 'chosen'=>true)
        )
	,	'opening_times' => array(
			'label'						=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['opening_times']
		,	'exclude' 					=> true
		,	'inputType' 				=> 'multiColumnWizard'
		,	'eval' 						=> array(
				'columnFields' => array(
					'weekday' => array(
						'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_weekday']
					,	'exclude'                 	=> false
					,	'inputType'               	=> 'select'
					,	'options'        			=> &$GLOBALS['TL_LANG']['tl_storelocator']['weekdays']
					,	'search'                  	=> true
					,	'eval'                    	=> array( 'mandatory'=>true, 'maxlength'=>255, 'style'=>'width:480px' )
					)
				,	'from' => array(
						'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_from']
					,	'exclude'                 	=> false
					,	'inputType'               	=> 'text'
					,	'eval'                    	=> array( 'mandatory'=>false, 'maxlength'=>5, 'style'=>'width:50px' )
					)
				,	'to' => array(
						'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_to']
					,	'exclude'                 	=> false
					,	'inputType'               	=> 'text'
					,	'eval'                    	=> array( 'mandatory'=>false, 'maxlength'=>5, 'style'=>'width:50px' )
					)
				)
			)
		)
	,	'longitude' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['longitude']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'latitude' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50')
        )
	,	'map' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
		,	'input_field_callback'    => array('tl_storelocator_stores', 'showMap')
        )
	,	'geo_explain' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
		,	'input_field_callback'    => array('tl_storelocator_stores', 'showGeoExplain'),
        )
	,	'file' => array (
			'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator']['import']['file']
		,	'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'csv', 'class'=>'mandatory')
		)
	)
);


class tl_storelocator_stores extends Backend {
	

	/**
	 * Listing for overview
	 * @param array
	 * @return string
	 */
	public function listStores($arrRow) {
		return '<div class="limit_height block">
			<p>' . $arrRow['name'] . ' <span style="color:#b3b3b3;"><em>(' . $arrRow['postal'] . ' ' . $arrRow['city'] . ')</em></span></p>'
			. '</div>' . "\n";
	}
	
	
	/**
	 * Returns list of countries
	 * @return array
	 */
	public function getCountries() {
	
		return parent::getCountries();
	}
	
	
	/**
	 * Fills coordinates if not already set and saving
	 * @param DataContainer
	 * @return bool
	 */
	public function fillCoordinates( DataContainer $dc ) {
	
		if( !$dc->activeRecord ) {
			return;
		}
		
		// find coordinates using google maps api
		$coords = StoreLocator::getCoordinates(
			$dc->activeRecord->street
		,	$dc->activeRecord->postal
		,	$dc->activeRecord->city
		,	$dc->activeRecord->country
		);
		
		if( !empty($coords) ) {
			$this->Database->prepare("UPDATE tl_storelocator_stores %s WHERE id=?")->set($coords)->execute($dc->id);
			return $true;
		}
		
		return false;
	}


	/**
	 * Returns geographical coordinates
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return array
	 */	
	public function getCoordinates( $street=NULL, $postal=NULL, $city=NULL, $country=NULL ) {

		return StoreLocator::getCoordinates( $street, $postal, $city, $country );
	}
	
	
	/**
	 * Displays a little static Google Map with position of the address
	 * @param DataContainer
	 * @return string
	 */
	public function showMap( DataContainer $dc ) {
	
		$sCoords = sprintf(
			"%s,%s"
		,	$dc->activeRecord->latitude
		,	$dc->activeRecord->longitude
		);
	
		return '<div style="float: right; height: 139px; margin-right: 23px; overflow: hidden; width: 320px;">'
		.'<h3><label>'.$GLOBALS['TL_LANG']['tl_storelocator_stores']['map'][0].'</label></h3> '
		.'<img style="margin-top: 1px;" src="http://maps.google.com/maps/api/staticmap?center='.$sCoords.'&zoom=16&size=320x139&maptype=roadmap&markers=color:red|label:|'.$sCoords.'&sensor=false" />'
		.'</div>';
	}


	/**
	 * Shows a little info text what coordinates are
	 * @return string
	 */	
	public function showGeoExplain() {
	
		return '<div class="tl_help">'.$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_explain'][0].'</div>';
	}
	
	
	/**
	 * Add leading "http://" if missing
	 * @param mixed
	 * @param DataContainer
	 * @return string
	 */
	public function checkURL( $varValue, DataContainer $dc ) {
	
		return ( $varValue && strpos($varValue,'http') === FALSE ) ? 'http://'.$varValue : $varValue;
	}
}

?>