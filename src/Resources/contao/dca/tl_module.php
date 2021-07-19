<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2021 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2021 numero2 - Agentur für digitales Marketing GbR
 */


/**
 * Add config to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = ['\numero2\StoreLocator\StoreLocatorBackend', 'showGoogleKeysMissingMessage'];
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = ['\numero2\StoreLocator\DCAHelper\Module', 'hideProviderDependentField'];

/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_search'] = '{title_legend},name,headline,type;{config_legend},storelocator_provider,jumpTo,storelocator_enable_autocomplete,storelocator_search_categories;{template_legend:hide},storelocator_search_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_list'] = '{title_legend},name,headline,type;{config_legend},storelocator_list_categories,storelocator_list_limit,storelocator_list_sort_field,storelocator_list_sort_direction,storelocator_limit_distance,storelocator_always_show_results,storelocator_use_filter,jumpTo;{sl_map_legend},storelocator_show_map;{source_legend},imgSize;{template_legend:hide},storelocator_list_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_filter'] = '{title_legend},name,headline,type;{config_legend},jumpTo,storelocator_search_in,storelocator_sortable;{template_legend:hide},storelocator_filter_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_details'] = '{title_legend},name,type;{config_legend},storelocator_provider;{source_legend},imgSize;{template_legend:hide},storelocator_details_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_static_map'] = '{title_legend},name,headline,type;{config_legend},jumpTo,storelocator_center,storelocator_zoom,storelocator_search_categories,storelocator_limit_marker_static;{template_legend:hide},storelocator_static_map_tpl,storelocator_maptype,storelocator_size,storelocator_scale,storelocator_format;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_limit_distance';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_enable_autocomplete';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_always_show_results';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_show_map';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_load_results_on_pan';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_use_filter';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_limit_distance'] = 'storelocator_max_distance';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_use_filter'] = 'storelocator_mod_filter';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_enable_autocomplete'] = 'storelocator_autocomplete_country';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_always_show_results'] = 'storelocator_default_country';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_show_map'] = 'storelocator_provider';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_provider-google-maps'] = 'storelocator_provider,storelocator_markerclusterer,storelocator_map_interaction,storelocator_list_interaction,storelocator_map_pin,storelocator_load_results_on_pan';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_load_results_on_pan'] = 'storelocator_limit_marker';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_provider'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_provider']
,   'inputType'           => 'select'
,   'options_callback'    => ['numero2\StoreLocator\DCAHelper\Module', 'getJavascriptProviders']
,   'reference'           => &$GLOBALS['TL_LANG']['tl_module']['storelocator_providers']
,   'eval'                => ['includeBlankOption'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(32) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_enable_autocomplete'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_enable_autocomplete']
,   'inputType'           => 'checkbox'
,   'eval'                => ['submitOnChange'=>true, 'tl_class'=>'w50']
,   'sql'                 => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_autocomplete_country'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_autocomplete_country']
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getCountries']
,   'default'             => 'de'
,   'search'              => true
,   'eval'                => ['maxlength'=>2, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true]
,   'sql'                 => "varchar(2) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_default_country'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_default_country']
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getCountries']
,   'default'             => 'de'
,   'search'              => true
,   'eval'                => ['includeBlankOption'=>true, 'maxlength'=>2, 'tl_class'=>'w50', 'chosen'=>true]
,   'sql'                 => "varchar(2) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_categories'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_categories']
,   'exclude'             => true
,   'inputType'           => 'checkbox'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getCategories']
,   'eval'                => ['mandatory'=>true, 'multiple'=>true, 'tl_class'=>'clr w50']
,   'sql'                 => "text NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_tpl'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl']
,   'default'             => 'mod_storelocator_search'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getTemplates']
,   'eval'                => ['includeBlankOption'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] .= ' clr';

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_categories'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories']
,   'exclude'             => true
,   'inputType'           => 'checkbox'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getCategories']
,   'eval'                => ['mandatory'=>true, 'multiple'=>true]
,   'sql'                 => "text NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_limit'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit']
,   'default'             => '10'
,   'exclude'             => true
,   'inputType'           => 'text'
,   'eval'                => ['rgxp'=>'digit', 'tl_class'=>'w50', 'mandatory'=>true]
,   'sql'                 => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_sort_field'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_sort_field']
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getSortableFields']
,   'exclude'             => true
,   'eval'                => ['tl_class'=>'clr w50', 'chosen'=>true, 'includeBlankOption'=>true]
,   'sql'                 => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_sort_direction'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_sort_direction']
,   'inputType'           => 'select'
,   'options'             => ['ascending', 'descending']
,   'reference'           => &$GLOBALS['TL_LANG']['MSC']
,   'exclude'             => true
,   'eval'                => ['tl_class'=>'w50']
,   'sql'                 => "varchar(10) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_limit_distance'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_distance']
,   'inputType'           => 'checkbox'
,   'default'             => false
,   'eval'                => ['tl_class'=>'w50 clr', 'submitOnChange'=>true]
,   'sql'                 => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_max_distance'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_max_distance']
,   'default'             => '10'
,   'exclude'             => true
,   'inputType'           => 'text'
,   'eval'                => ['rgxp'=>'digit', 'tl_class'=>'w50', 'mandatory'=>true]
,   'sql'                 => "int(5) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_always_show_results'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_always_show_results']
,   'inputType'           => 'checkbox'
,   'default'             => true
,   'eval'                => ['tl_class'=>'clr w50', 'submitOnChange'=>true]
,   'sql'                 => "char(1) NOT NULL default '1'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_show_map'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_show_map']
,   'inputType'           => 'checkbox'
,   'default'             => true
,   'eval'                => ['tl_class'=>'w50', 'submitOnChange'=>true]
,   'sql'                 => "char(1) NOT NULL default '1'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_markerclusterer'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_markerclusterer']
,   'inputType'           => 'checkbox'
,   'eval'                => ['tl_class'=>'w50 cbx']
,   'sql'                 => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_load_results_on_pan'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_load_results_on_pan']
,   'inputType'           => 'checkbox'
,   'default'             => true
,   'eval'                => ['tl_class'=>'w50 cbx m12', 'submitOnChange'=>true]
,   'sql'                 => "char(1) NOT NULL default '1'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_limit_marker'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_marker']
,   'inputType'           => 'text'
,   'default'             => '100'
,   'exclude'             => true
,   'eval'                => ['rgxp'=>'digit', 'tl_class'=>'w50', 'mandatory'=>true]
,   'sql'                 => "int(5) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_map_interaction'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_map_interaction']
,   'default'             => 'nothing'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['numero2\StoreLocator\StoreLocatorBackend', 'getMapInteractions']
,   'eval'                => [ 'tl_class'=>'w50 clr']
,   'sql'                 => "varchar(64) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_interaction'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_interaction']
,   'default'             => 'nothing'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['numero2\StoreLocator\StoreLocatorBackend', 'getListInteractions']
,   'eval'                => ['tl_class'=>'w50']
,   'sql'                 => "varchar(64) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_map_pin'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_map_pin']
,   'inputType'           => 'fileTree'
,   'eval'                => ['filesOnly'=>true, 'extensions'=>\Config::get('validImageTypes'), 'fieldType'=>'radio', 'tl_class'=>'clr']
,   'sql'                 => "binary(16) NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_tpl'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl']
,   'default'             => 'mod_storelocator_list'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getTemplates']
,   'eval'                => ['includeBlankOption'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_details_tpl'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl']
,   'default'             => 'mod_storelocator_details'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getTemplates']
,   'eval'                => ['includeBlankOption'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_filter_tpl'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_filter_tpl']
,   'default'             => 'mod_storelocator_filter'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getTemplates']
,   'eval'                => ['includeBlankOption'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_in'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_in']
,   'inputType'           => 'checkboxWizard'
,   'options'             => ['name', 'email', 'url', 'phone', 'fax', 'description', 'postal', 'city']
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getStoreFields']
,   'eval'                => ['multiple'=>true, 'tl_class'=>'clr w50 heightAuto']
,   'sql'                 => "blob NULL"
];
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_sortable'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_sortable']
,   'inputType'           => 'checkboxWizard'
,   'options'             => ['name', 'email', 'url', 'phone', 'fax', 'description', 'postal', 'city']
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getStoreFields']
,   'eval'                => ['multiple'=>true, 'tl_class'=>'w50 heightAuto']
,   'sql'                 => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_use_filter'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_use_filter']
,   'inputType'           => 'checkbox'
,   'eval'                => ['tl_class'=>'clr w50', 'submitOnChange'=>true]
,   'sql'                 => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_mod_filter'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['storelocator_mod_filter']
,   'inputType'         => 'select'
,   'exclude'           => true
,   'options_callback'  => ['\numero2\StoreLocator\DCAHelper\Module', 'getFilterModules']
,   'eval'              => ['mandatory'=>true, 'tl_class'=>'w50 wizard']
,   'wizard'            => [['\numero2\StoreLocator\DCAHelper\Module', 'editModule']]
,   'sql'               => "int(10) NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_center'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_center']
,   'inputType'           => 'text'
,   'exclude'             => true
,   'eval'                => ['maxlength'=>32, 'tl_class'=>'w50']
,   'sql'                 => "varchar(32) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_zoom'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_zoom']
,   'inputType'           => 'text'
,   'exclude'             => true
,   'eval'                => ['rgxp'=>'digit', 'maxlength'=>2, 'tl_class'=>'w50']
,   'sql'                 => "varchar(2) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_limit_marker_static'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_marker_static']
,   'inputType'           => 'text'
,   'exclude'             => true
,   'eval'                => ['mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50']
,   'sql'                 => "varchar(3) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_maptype'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_maptype']
,   'inputType'           => 'select'
,   'default'             => 'roadmap'
,   'exclude'             => true
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getMapType']
,   'eval'                => ['mandatory'=>true, 'tl_class'=>'w50 clr']
,   'sql'                 => "varchar(16) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_size'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_size']
,   'inputType'           => 'text'
,   'exclude'             => true
,   'eval'                => ['mandatory'=>true, 'multiple'=>true, 'size'=>2, 'rgxp'=>'natural', 'nospace'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(64) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_format'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_format']
,   'inputType'           => 'select'
,   'default'             => 'jpg'
,   'exclude'             => true
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getMapFormat']
,   'eval'                => ['mandatory'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(16) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_scale'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_scale']
,   'inputType'           => 'select'
,   'default'             => '1'
,   'exclude'             => true
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getMapScale']
,   'eval'                => ['mandatory'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(16) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_static_map_tpl'] = [
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_static_map_tpl']
,   'default'             => 'mod_storelocator_static_map'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => ['\numero2\StoreLocator\DCAHelper\Module', 'getTemplates']
,   'eval'                => ['includeBlankOption'=>true, 'tl_class'=>'w50']
,   'sql'                 => "varchar(255) NOT NULL default ''"
];
