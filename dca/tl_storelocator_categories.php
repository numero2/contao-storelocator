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
 * Table tl_storelocator_categories
 */
$GLOBALS['TL_DCA']['tl_storelocator_categories'] = array(

    'config' => array(
        'dataContainer'               => 'Table'
    ,   'ctable'                      => array('tl_storelocator_stores')
    ,   'switchToEdit'                => true
    ,   'onload_callback'             => array( array('numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage') )
    ,   'sql' => array (
            'keys' => array (
                'id' => 'primary'
            )
        )
    )
,   'list' => array(
        'sorting' => array(
            'mode'                    => 1
        ,   'fields'                  => array('title')
        ,   'flag'                    => 1
        ,   'panelLayout'             => 'filter;search,limit'
        )
    ,   'label' => array(
            'fields'                  => array('title')
        ,   'format'                  => '%s'
        )
    ,   'global_operations' => array(
            'all' => array(
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all']
            ,   'href'                => 'act=select'
            ,   'class'               => 'header_edit_all'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();"'
            )
        )
    ,   'operations' => array(
            'edit' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['edit']
            ,   'href'                => 'table=tl_storelocator_stores'
            ,   'icon'                => 'edit.svg'
            )
        ,   'editheader' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['editheader']
            ,   'href'                => 'act=edit'
            ,   'icon'                => 'header.svg'
            )
        ,   'copy' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['copy']
            ,   'href'                => 'act=copy'
            ,   'icon'                => 'copy.svg'
            )
        ,   'delete' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['delete']
            ,   'href'                => 'act=delete'
            ,   'icon'                => 'delete.svg'
            ,   'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            )
        ,   'show' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['show']
            ,   'href'                => 'act=show'
            ,   'icon'                => 'show.svg'
            )
        )
    )
,   'palettes' => array(
        'default'                     => '{title_legend},title,alias;{map_legend},map_pin;'
    )
,   'fields' => array(
        'id' => array(
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        )
    ,   'tstamp' => array(
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        )
    ,   'title' => array(
            'label'         => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['title']
        ,   'inputType'     => 'text'
        ,   'search'        => true
        ,   'eval'          => array( 'mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50' )
        ,   'sql'           => "varchar(64) NOT NULL default ''"
        )
    ,   'alias' => array(
            'label'         => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['alias']
        ,   'exclude'       => true
        ,   'inputType'     => 'text'
        ,   'eval'          => array( 'rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50' )
        ,   'save_callback' => array(
                array('tl_storelocator_categories', 'generateAlias')
            )
        ,   'sql'           => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        )
    ,   'map_pin' => array(
            'label'         => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['map_pin']
        ,   'inputType'     => 'fileTree'
        ,   'eval'          => array( 'filesOnly'=>true, 'extensions'=>\Config::get('validImageTypes'), 'fieldType'=>'radio', 'mandatory'=>false )
        ,   'sql'           => "binary(16) NULL"
        )
    )
);


class tl_storelocator_categories extends \Backend {


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
            $varValue = StringUtil::generateAlias($dc->activeRecord->title);
        }

        $oAlias = NULL;
        $oAlias = \numero2\StoreLocator\CategoriesModel::findBy( array('id=? OR alias=?'), array($dc->activeRecord->id,$varValue) );

        // Check whether the alias exists
        if( $oAlias && $oAlias->count() > 1 ) {

            if( !$autoAlias ) {
                throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
            }

            $varValue .= '-' . $dc->id;
        }

        return $varValue;
    }
}