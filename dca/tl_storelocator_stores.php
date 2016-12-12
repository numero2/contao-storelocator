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
 * Table tl_storelocator_stores
 */
$GLOBALS['TL_DCA']['tl_storelocator_stores'] = array(

	'config' => array (
		'dataContainer'               => 'Table'
	,	'ptable'                      => 'tl_storelocator_categories'
	,	'onsubmit_callback'   	  	  => array(
			array('numero2\StoreLocator\StoreLocatorBackend', 'fillCoordinates')
		)
	,	'onload_callback' 		 	  => array( array('numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage') )
    ,   'sql' => array (
            'keys' => array (
                'id' => 'primary'
            )
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
		,	'fillCoordinates' => array (
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['fillCoordinates']
			,	'href'                => 'key=fillCoordinates'
			,	'class'               => 'header_fill_coordinates'
			,	'attributes'          => 'onclick="Backend.getScrollOffset()"'
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
        ,   'highlight' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['highlight']
            ,   'icon'                => 'featured.gif'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleFeatured(this,%s)"'
            ,   'button_callback'     => array('tl_storelocator_stores', 'iconHighlight')
			)
		,	'coords' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['coords']
			,	'href'                => 'act=show'
			,	'icon'                => array( 'system/modules/storelocator/assets/coords0.png', 'system/modules/storelocator/assets/coords1.png' )
			,	'button_callback'     => array( 'tl_storelocator_stores', 'coordsButton' )
            )
        )
	)
,	'palettes' => array(
		'default'                     => '{common_legend},name,alias,email,url,phone,fax,description;{adress_legend},street,postal,city,country;{times_legend},opening_times;{geo_legend},geo_explain,longitude,map,latitude;{publish_legend},highlight;'
	)
,	'fields' => array(
        'id' => array(
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        )
    ,   'pid' => array(
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        )
    ,   'tstamp' => array(
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        )
    ,   'name' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['name']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50' )
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
    ,   'alias' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['alias']
        ,   'exclude'                 => true
        ,   'inputType'               => 'text'
        ,   'eval'                    => array( 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50' )
        ,   'save_callback'           => array(
				array('tl_storelocator_stores', 'generateAlias')
			)
        ,   'sql'           => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
		)
	,	'email' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['email']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'email ', 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'url' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['url']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'url ', 'maxlength'=>255, 'tl_class'=>'w50')
		,	'save_callback' 		  => array( array('tl_storelocator_stores', 'checkURL') )
        ,   'sql'                     => "varchar(255) NOT NULL default ''"
        )
	,	'phone' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['phone']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'phone ', 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'fax' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['fax']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('rgxp' => 'phone ', 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
    ,   'description' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['description']
        ,   'inputType'               => 'textarea'
        ,   'eval'                    => array('rte'=>'tinyMCE', 'tl_class'=>'clr')
        ,   'sql'                     => "text NULL"
        )
	,	'street' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['street']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'postal' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['postal']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'city' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['city']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'country' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['country']
		,	'inputType'               => 'select'
		,	'options_callback'        => array('tl_storelocator_stores', 'getCountries')
		,	'default'				  => 'de'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50', 'chosen'=>true)
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'opening_times' => array(
			'label'					  => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['opening_times']
		,	'exclude' 				  => true
		,	'inputType' 			  => 'multiColumnWizard'
		,	'eval' 					  => array(
				'columnFields' => array(
					'weekday' => array(
						'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_weekday']
					,	'exclude'                 	=> false
					,	'inputType'               	=> 'select'
					,	'options_callback'          => array( '\numero2\StoreLocator\StoreLocator', 'getWeekdays' )
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
        ,   'sql'                     => "text NULL"
		)
	,	'longitude' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['longitude']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
        )
	,	'latitude' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50')
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
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
    ,   'highlight' => array(
            'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['highlight']
        ,   'inputType'               => 'checkbox'
        ,   'search'                  => true
        ,   'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50')
        ,   'sql'                     => "char(1) NOT NULL default '0'"
        )
	)
);


class tl_storelocator_stores extends \Backend {


    /**
	 * Return the "highlight/unhighlight store" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function iconHighlight( $row, $href, $label, $title, $icon, $attributes ) {

		if( strlen(Input::get('fid')) ) {

			$this->toggleFeatured( Input::get('fid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null) );
			$this->redirect( $this->getReferer() );
		}

		$href .= '&amp;fid='.$row['id'].'&amp;state='.($row['highlight'] ? '' : 1);

		if( !$row['highlight'] ) {
			$icon = 'featured_.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['highlight'] ? 1 : 0) . '"').'</a> ';
	}


    /**
	 * Highlight/unhighlight a store
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function toggleFeatured( $intId, $blnVisible, DataContainer $dc=null ) {

        $oStore = NULL;
        $oStore = \numero2\StoreLocator\StoresModel::findById( $intId );

        if( $oStore ) {
            $oStore->highlight = ($blnVisible ? 1 : 0);
            $oStore->save();
        }
	}


    /**
	 * Auto-generate an category alias if it has not been set yet
	 *
	 * @param mixed         $varValue
	 * @param DataContainer $dc
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	public function generateAlias( $varValue, DataContainer $dc ) {

		$autoAlias = false;

		// Generate an alias if there is none
		if( $varValue == '' ) {
			$autoAlias = true;
			$varValue = StringUtil::generateAlias($dc->activeRecord->name);
		}

        $oAlias = NULL;
        $oAlias = \numero2\StoreLocator\StoresModel::findByAlias( $varValue );

		// Check whether the alias exists
		if( $oAlias && count($oAlias) > 0 ) {

			if( !$autoAlias ) {
				throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
			}

			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}


	/**
	 * Generates button to show if coordinates are available
	 *
	 * @param array    $row
	 * @param srting   $href
	 * @param array    $label
	 * @param string   $title
	 * @param mixed    $icon
	 * @param array    $attributes
	 *
	 * @return string
	 */
	public function coordsButton( $row=NULL, $href=NULL, $label=NULL, $title=NULL, $icon=NULL, $attributes=NULL ) {

        $oStore = NULL;
        $oStore = \numero2\StoreLocator\StoresModel::findById( $row['id'] );

		$icon  = ($oStore && ($oStore->latitude || $oStore->longitude)) ? $icon[1] : $icon[0];
		$label = ($oStore && ($oStore->latitude || $oStore->longitude)) ? $label[1] : $label[0];

		return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon,$label).'</a> ';
	}


	/**
	 * Listing for overview
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function listStores( $arrRow ) {
		return '<div class="limit_height block">
			<p>' . $arrRow['name'] . ' <span style="color:#b3b3b3;"><em>(' . $arrRow['postal'] . ' ' . $arrRow['city'] . ')</em></span></p>'
			. '</div>' . "\n";
	}


	/**
	 * Displays a little static Google Map with position of the address
	 *
	 * @param DataContainer $dc
	 *
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
	 *
	 * @return string
	 */
	public function showGeoExplain() {

		return '<div class="tl_help">'.$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_explain'][0].'</div>';
	}


	/**
	 * Add leading "http://" if missing
	 *
	 * @param mixed            $varValue
	 * @param DataContainer    $dc
	 *
	 * @return string
	 */
	public function checkURL( $varValue, DataContainer $dc ) {

		return ( $varValue && strpos($varValue,'http') === FALSE ) ? 'http://'.$varValue : $varValue;
	}
}
