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
 * Add config to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('\numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage');


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_search'] = '{title_legend},name,headline,type;{config_legend},jumpTo,storelocator_enable_autocomplete,storelocator_search_categories;{template_legend:hide},storelocator_search_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_list'] = '{title_legend},name,headline,type;{config_legend},storelocator_list_categories,storelocator_list_limit,storelocator_limit_distance,storelocator_always_show_results,storelocator_use_filter,jumpTo;{sl_map_legend},storelocator_show_map;{template_legend:hide},storelocator_list_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_filter'] = '{title_legend},name,headline,type;{config_legend},jumpTo,storelocator_search_in,storelocator_sortable;{template_legend:hide},storelocator_filter_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_details'] = '{title_legend},name,type;{template_legend:hide},storelocator_details_tpl;{expert_legend:hide},guests,cssID,space';

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
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_show_map'] = 'storelocator_load_results_on_pan,storelocator_map_interaction,storelocator_list_interaction,storelocator_map_pin';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_load_results_on_pan'] = 'storelocator_limit_marker';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_enable_autocomplete'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_enable_autocomplete']
,   'inputType'           => 'checkbox'
,   'eval'                => array( 'mandatory'=>false, 'tl_class'=>'w50', 'submitOnChange' => true )
,   'sql'                 => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_autocomplete_country'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_autocomplete_country']
,   'inputType'           => 'select'
,   'options_callback'    => array( 'tl_module_storelocator', 'getCountries' )
,   'default'             => 'de'
,   'search'              => true
,   'eval'                => array( 'mandatory'=>false, 'maxlength'=>2, 'tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true )
,   'sql'                 => "varchar(2) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_default_country'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_default_country']
,   'inputType'           => 'select'
,   'options_callback'    => array( 'tl_module_storelocator', 'getCountries' )
,   'default'             => 'de'
,   'search'              => true
,   'eval'                => array( 'mandatory'=>true, 'maxlength'=>2, 'tl_class'=>'w50', 'chosen'=>true )
,   'sql'                 => "varchar(2) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_categories'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_categories']
,   'exclude'             => true
,   'inputType'           => 'checkbox'
,   'options_callback'    => array( 'tl_module_storelocator', 'getCategories' )
,   'eval'                => array( 'mandatory'=>true, 'multiple'=>true, 'tl_class'=>'clr w50' )
,   'sql'                 => "text NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_tpl'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl']
,   'default'             => 'mod_storelocator_search'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => array('tl_module_storelocator', 'getTemplates')
,   'eval'                => array( 'tl_class'=>'w50' )
,   'sql'                 => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] .= ' clr';

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_categories'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories']
,   'exclude'             => true
,   'inputType'           => 'checkbox'
,   'options_callback'    => array( 'tl_module_storelocator', 'getCategories' )
,   'eval'                => array( 'mandatory'=>true, 'multiple'=>true )
,   'sql'                 => "text NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_limit'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit']
,   'default'             => '10'
,   'exclude'             => true
,   'inputType'           => 'text'
,   'eval'                => array( 'rgxp' => 'digit', 'tl_class'=>'w50', 'mandatory'=>true )
,   'sql'                 => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_limit_distance'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_distance']
,   'inputType'           => 'checkbox'
,   'default'             => false
,   'eval'                => array( 'mandatory'=>false, 'tl_class'=>'w50 clr', 'submitOnChange' => true )
,   'sql'                 => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_max_distance'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_max_distance']
,   'default'             => '10'
,   'exclude'             => true
,   'inputType'           => 'text'
,   'eval'                => array( 'rgxp' => 'digit', 'tl_class'=>'w50', 'mandatory'=>true )
,   'sql'                 => "int(5) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_always_show_results'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_always_show_results']
,   'inputType'           => 'checkbox'
,   'default'             => true
,   'eval'                => array( 'mandatory'=>false, 'tl_class'=>'clr w50', 'submitOnChange' => true )
,   'sql'                 => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_show_map'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_show_map']
,   'inputType'           => 'checkbox'
,   'default'             => true
,   'eval'                => array( 'mandatory'=>false, 'tl_class'=>'w50', 'submitOnChange' => true )
,   'sql'                 => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_load_results_on_pan'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_load_results_on_pan']
,   'inputType'           => 'checkbox'
,   'default'             => true
,   'eval'                => array( 'mandatory'=>false, 'tl_class'=>'w50 clr', 'submitOnChange' => true )
,   'sql'                 => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_limit_marker'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_marker']
,   'default'             => '100'
,   'exclude'             => true
,   'inputType'           => 'text'
,   'eval'                => array( 'rgxp' => 'digit', 'tl_class'=>'w50', 'mandatory'=>true )
,   'sql'                 => "int(5) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_map_interaction'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_map_interaction']
,   'default'             => 'nothing'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => array('numero2\StoreLocator\StoreLocatorBackend', 'getMapInteractions')
,   'eval'                => array( 'tl_class'=>'w50 clr' )
,   'sql'                 => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_interaction'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_interaction']
,   'default'             => 'nothing'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => array('numero2\StoreLocator\StoreLocatorBackend', 'getListInteractions')
,   'eval'                => array( 'tl_class'=>'w50' )
,   'sql'                 => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_map_pin'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_map_pin']
,   'inputType'           => 'fileTree'
,   'eval'                => array( 'filesOnly'=>true, 'extensions'=>\Config::get('validImageTypes'), 'fieldType'=>'radio', 'mandatory'=>false, 'tl_class'=>'clr' )
,   'sql'                 => "binary(16) NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_tpl'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl']
,   'default'             => 'mod_storelocator_list'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => array('tl_module_storelocator', 'getTemplates')
,   'eval'                => array( 'tl_class'=>'w50' )
,   'sql'                 => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_details_tpl'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl']
,   'default'             => 'mod_storelocator_details'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => array('tl_module_storelocator', 'getTemplates')
,   'eval'                => array( 'tl_class'=>'w50' )
,   'sql'                 => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_filter_tpl'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_filter_tpl']
,   'default'             => 'mod_storelocator_filter'
,   'exclude'             => true
,   'inputType'           => 'select'
,   'options_callback'    => array('tl_module_storelocator', 'getTemplates')
,   'eval'                => array( 'tl_class'=>'w50' )
,   'sql'                 => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_in'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_in']
,   'inputType'           => 'checkboxWizard'
,   'options_callback'    => array('tl_module_storelocator', 'getStoreFields')
,   'eval'                => array( 'mandatory'=>false, 'multiple'=>true, 'tl_class'=>'clr w50 heightAuto')
,   'sql'                 => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_sortable'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_sortable']
,   'inputType'           => 'checkboxWizard'
,   'options_callback'    => array('tl_module_storelocator', 'getStoreFields')
,   'eval'                => array( 'mandatory'=>false, 'multiple'=>true, 'tl_class'=>'w50 heightAuto')
,   'sql'                 => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_use_filter'] = array(
    'label'               => &$GLOBALS['TL_LANG']['tl_module']['storelocator_use_filter']
,   'inputType'           => 'checkbox'
,   'eval'                => array( 'mandatory'=>false, 'tl_class'=>'clr w50', 'submitOnChange' => true )
,   'sql'                 => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_mod_filter'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['storelocator_mod_filter']
,   'inputType'         => 'select'
,   'exclude'           => true
,   'options_callback'  => array('tl_module_storelocator', 'getFilterModules')
,   'eval'              => array('mandatory'=>true, 'tl_class'=>'w50 wizard')
,   'wizard'            => array( array('tl_module_storelocator', 'editModule') )
,   'sql'               => "int(10) NOT NULL default '0'"
);


class tl_module_storelocator extends \Backend {


    /**
     * Returns a list of all store categories
     *
     * @return array
     */
    public function getCategories() {

        $aCategories = array();

        $oCategories = NULL;
        $oCategories = \numero2\StoreLocator\CategoriesModel::getCategories();

        while( $oCategories->next() ) {

            $aCategories[ $oCategories->id ] = $oCategories->title;
        }

        return $aCategories;
    }


    /**
     * Returns a list of all templates
     *
     * @param  DataContainer $dc
     *
     * @return array
     */
    public function getTemplates( DataContainer $dc ) {

        $intPid = $dc->activeRecord->pid;

        if( \Input::get('act') == 'overrideAll' ) {
            $intPid =\Input::get('id');
        }

        return $this->getTemplateGroup('mod_storelocator_', $intPid);
    }


    /**
     * Returns a list of all templates
     *
     * @param  DataContainer $dc
     *
     * @return array
     */
    public function getStoreFields() {

        self::loadLanguageFile('tl_storelocator_stores');

        $arr = array(
            'name' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['name'][0]
        ,   'email' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['email'][0]
        ,   'url' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['url'][0]
        ,   'phone' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['phone'][0]
        ,   'fax' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['fax'][0]
        ,   'description' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['description'][0]
        ,   'postal' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['postal'][0]
        ,   'city' => $GLOBALS['TL_LANG']['tl_storelocator_stores']['city'][0]
        );

        return $arr;
    }


    /**
     * Generates a list of all Stores with Categorie 1
     *
     * @return array
     */
    public function getFilterModules() {

        $oModule = \ModuleModel::findBy('type', 'storelocator_filter');
        $aModule = array();

        if( $oModule ) {

            foreach( $oModule as $key => $value ) {

                $aModule[$value->id] = $value->name.' (ID: '.$value->id.')';
            }
        }

        return $aModule;
    }


    /**
    * Return the edit module wizard
    *
    * @param DataContainer $dc
    *
    * @return string
    */
    public function editModule(DataContainer $dc) {

        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_module']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_module']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_module']['editalias'][0]) . '</a>';
    }
}
