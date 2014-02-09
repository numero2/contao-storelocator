<?php

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
 * @copyright  2014 Tastaturberuf <mail@tastaturberuf.de>,
 *             2013 numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Daniel Jahnsmüller <mail@jahnsmueller.net>,
 *             Benny Born <benny.born@numero2.de>
 * @package    storelocator
 * @license    LGPL
 * @filesource
 */


/**
 * Add palettes to tl_module
 */
array_insert($GLOBALS['TL_DCA']['tl_module']['palettes'], 1337, array
(
    'storelocator_search' => '
        {title_legend},name,headline,type;
        {config_legend:hide},jumpTo,storelocator_search_country,storelocator_show_full_country_names;
        {template_legend:hide},storelocator_search_tpl;
        {expert_legend:hide},guests,cssID,space
    ',
    'storelocator_list' => '
        {title_legend},name,headline,type;
        {config_legend:hide},storelocator_list_categories,storelocator_list_limit,storelocator_allow_empty_search,storelocator_limit_distance,jumpTo;
        {template_legend:hide},storelocator_list_tpl;
        {expert_legend:hide},guests,cssID,space
    ',
    'storelocator_details' => '
        {title_legend},name,type;
        {slmap_legend:hide},storelocator_details_maptype;
        {template_legend:hide},storelocator_details_tpl;
        {expert_legend:hide},guests,cssID,space
    '
));

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'storelocator_limit_distance';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['storelocator_limit_distance'] = 'storelocator_max_distance';

/**
 * Add fields to tl_module
 */
array_insert($GLOBALS['TL_DCA']['tl_module']['fields'], 1337, array
(
    'storelocator_search_country' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_country'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => System::getCountries(),
        'default'	=> 'de',
        'search'    => true,
        'eval'      => array
        (
            'mandatory' => true,
            'maxlength' => 2,
            'tl_class'  => 'w50'
        ),
        'sql' => "varchar(2) NOT NULL default ''"
    ),
    'storelocator_show_full_country_names' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_show_full_country_names'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array
        (
            'mandatory' => false,
            'tl_class'=>'w50 m12'
        ),
        'sql' => "char(1) NOT NULL default ''"
    ),
    'storelocator_search_tpl' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => Controller::getTemplateGroup('mod_storelocator_'),
        'default'   => 'mod_storelocator_search',
        'eval'      => array('tl_class' => 'w50'),
        'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'storelocator_list_tpl' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => Controller::getTemplateGroup('mod_storelocator_'),
        'default'   => 'mod_storelocator_list',
        'eval'      => array('tl_class' => 'w50'),
        'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'storelocator_list_categories' => array
    (
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories'],
        'exclude'          => true,
        'inputType'        => 'checkbox',
        'options_callback' => function()
        {
            //@todo: add model support
            $arrCalendars = array();
            $objCalendars = $this->Database->execute("SELECT id, title FROM tl_storelocator_category ORDER BY title");

            while ($objCalendars->next())
            {
                $arrCalendars[$objCalendars->id] = $objCalendars->title;
            }

            return $arrCalendars;
        },
        'eval' => array
        (
            'mandatory' => true,
            'multiple' => true
        ),
        'sql' => "text NULL"
    ),
    'storelocator_list_limit' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit'],
        'default'   => '10',
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => array
        (
            'mandatory' => true ,
            'rgxp'      => 'digit',
            'tl_class'  => 'w50'
        ),
        'sql' => "varchar(255) NOT NULL default ''"
    ),
    'storelocator_allow_empty_search' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_allow_empty_search'],
        'exclude'   => true,
    	'inputType' => 'checkbox',
    	'default'   => true,
    	'eval'      => array
        (
            'mandatory' => false,
            'tl_class'  => 'w50 m12',
        ),
        'sql' => "char(1) NOT NULL default ''"
    ),
    'storelocator_details_tpl' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl'],
        'exclude'   => true,
        'inputType' => 'select',
        'options'   => Controller::getTemplateGroup('mod_storelocator_'),
        'default'   => 'mod_storelocator_details',
        'eval'      => array('tl_class' => 'w50'),
        'sql'       => "varchar(255) NOT NULL default ''"
    ),
    'storelocator_details_maptype' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptype'],
        'exclude'   => true,
        'inputType' => 'select',
        // try with reference	&$GLOBALS['TL_LANG'] (string)
        'options'   => array
        (
            'static'    => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'][0],
            'dynamic'   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'][1]
        ),
        'sql' => "char(10) NOT NULL default 'static'"
    ),
    'storelocator_limit_distance' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_limit_distance'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array
        (
            'submitOnChange' => true,
            'tl_class'       => 'w50 clr'
        ),
        'sql' => "char(1) NOT NULL default ''"
    ),
    'storelocator_max_distance' => array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['storelocator_max_distance'],
        'default'   => '10',
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => array
        (
            'mandatory' => true,
            'rgxp' => 'digit',
            'tl_class' => 'w50'
        ),
        'sql' => "int(5) unsigned NOT NULL default '0'"
    )
));

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] .= ' clr';
