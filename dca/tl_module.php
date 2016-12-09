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


/* CONFIG */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('\numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage');


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_search'] = '{title_legend},name,headline,type;{config_legend:hide},jumpTo,storelocator_enable_autocomplete;{template_legend:hide},storelocator_search_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_list'] = '{title_legend},name,headline,type;{config_legend:hide},storelocator_list_categories,storelocator_list_limit,storelocator_allow_empty_search,storelocator_limit_distance,jumpTo;{template_legend:hide},storelocator_list_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_details'] = '{title_legend},name,type;{slmap_legend:hide},storelocator_details_maptype;{template_legend:hide},storelocator_details_tpl;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_limit_distance';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_enable_autocomplete';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_limit_distance'] = 'storelocator_max_distance';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_enable_autocomplete'] = 'storelocator_search_country';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_country'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_country']
,	'inputType'               => 'select'
,	'options_callback'        => array( 'tl_module_storelocator', 'getCountries' )
,	'default'				  => 'de'
,	'search'                  => true
,	'eval'                    => array( 'mandatory'=>true, 'maxlength'=>2, 'tl_class'=>'w50' )
,   'sql'                     => "varchar(2) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_enable_autocomplete'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_enable_autocomplete']
,	'inputType'               => 'checkbox'
,	'default'                 => true
,	'eval'                    => array( 'mandatory'=>false, 'tl_class'=>'w50', 'style'=>'margin-top:12px;', 'submitOnChange' => true )
,   'sql'                     => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl']
,	'default'                 => 'mod_storelocator_search'
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options_callback'        => array('tl_module_storelocator', 'getTemplates')
,   'eval'                    => array( 'tl_class'=>'w50' )
,	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] .= ' clr';

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl']
,	'default'                 => 'mod_storelocator_list'
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options_callback'        => array('tl_module_storelocator', 'getTemplates')
,	'eval'                    => array( 'tl_class'=>'w50' )
,   'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_categories'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories']
,	'exclude'                 => true
,	'inputType'               => 'checkbox'
,	'options_callback'        => array( 'tl_module_storelocator', 'getCategories' )
,	'eval'                    => array( 'mandatory'=>true, 'multiple'=>true )
,   'sql'                     => "text NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_limit'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit']
,	'default'                 => '10'
,	'exclude'                 => true
,	'inputType'               => 'text'
,	'eval'                    => array( 'rgxp' => 'digit', 'tl_class'=>'w50', 'mandatory'=>true )
,   'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_allow_empty_search'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_allow_empty_search']
,	'inputType'               => 'checkbox'
,	'default'                 => true
,	'eval'                    => array( 'mandatory'=>false, 'tl_class'=>'w50', 'style'=>'margin-top:12px;' )
,   'sql'                     => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_details_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl']
,	'default'                 => 'mod_storelocator_details'
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options_callback'        => array('tl_module_storelocator', 'getTemplates')
,	'eval'                    => array( 'tl_class'=>'w50' )
,   'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_details_maptype'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptype']
,   'exclude'                 => true
,   'inputType'               => 'select'
,   'options'                 => array(
        'static'    => $GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'][0]
    ,   'dynamic'   => $GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'][1]
    )
,   'sql'                     => "char(10) NOT NULL default 'static'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_limit_distance'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_distance']
,	'inputType'               => 'checkbox'
,	'default'                 => false
,	'eval'                    => array( 'mandatory'=>false, 'tl_class'=>'w50 clr', 'submitOnChange' => true)
,   'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_max_distance'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_max_distance']
,	'default'                 => '10'
,	'exclude'                 => true
,	'inputType'               => 'text'
,	'eval'                    => array( 'rgxp' => 'digit', 'tl_class'=>'w50', 'mandatory'=>true )
,   'sql'                     => "int(5) unsigned NOT NULL default '0'"
);

class tl_module_storelocator extends \Backend {


    public function getCategories() {

		$arrCalendars = array();
		$objCalendars = $this->Database->execute("SELECT id, title FROM tl_storelocator_category ORDER BY title");

		while ($objCalendars->next())
		{
				$arrCalendars[$objCalendars->id] = $objCalendars->title;
		}

		return $arrCalendars;
    }

	public function getTemplates( DataContainer $dc ) {

		$intPid = $dc->activeRecord->pid;

		if( $this->Input->get('act') == 'overrideAll' ) {
			$intPid = $this->Input->get('id');
		}

		return $this->getTemplateGroup('mod_storelocator_', $intPid);
	}
}
