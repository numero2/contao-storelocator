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
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_search'] = '{title_legend},name,headline,type;{config_legend:hide},jumpTo,storelocator_search_country,storelocator_show_full_country_names;{template_legend:hide},storelocator_search_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_list'] = '{title_legend},name,headline,type;{config_legend:hide},storelocator_list_categories,storelocator_list_limit,storelocator_allow_empty_search,jumpTo;{template_legend:hide},storelocator_list_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_details'] = '{title_legend},name,type;{slmap_legend:hide},storelocator_details_maptype;{template_legend:hide},storelocator_details_tpl;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_country'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_country']
,	'inputType'               => 'select'
,	'options_callback'        => array( 'tl_module_storelocator', 'getCountries' )
,	'search'                  => true
,	'eval'                    => array( 'mandatory'=>true, 'maxlength'=>2, 'tl_class'=>'w50' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_show_full_country_names'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_show_full_country_names']
,	'inputType'               => 'checkbox'
,	'default'                 => false
,	'eval'                    => array( 'mandatory'=>false, 'tl_class'=>'w50', 'style'=>'margin-top:12px;' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl']
,	'default'                 => 'mod_storelocator_search'
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options'                 => $this->getTemplateGroup('mod_storelocator_search')
,	'eval'                    => array( 'tl_class'=>'w50' )
); 

$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] .= ' clr';
 
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl']
,	'default'                 => 'mod_storelocator_list'
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options'                 => $this->getTemplateGroup('mod_storelocator_list')
,	'eval'                    => array( 'tl_class'=>'w50' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_categories'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories']
,	'exclude'                 => true
,	'inputType'               => 'checkbox'
,	'options_callback'        => array( 'tl_module_storelocator', 'getCategories' )
,	'eval'                    => array( 'mandatory'=>true, 'multiple'=>true )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_limit'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit']
,	'default'                 => '10'
,	'exclude'                 => true
,	'inputType'               => 'text'
,	'eval'                    => array( 'rgxp' => 'digit', 'tl_class'=>'w50', 'mandatory'=>true )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_allow_empty_search'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_allow_empty_search']
,	'inputType'               => 'checkbox'
,	'default'                 => true
,	'eval'                    => array( 'mandatory'=>false, 'tl_class'=>'w50', 'style'=>'margin-top:12px;' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_details_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl']
,	'default'                 => 'mod_storelocator_details'
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options'                 => $this->getTemplateGroup('mod_storelocator_details')
,	'eval'                    => array( 'tl_class'=>'w50' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_details_maptype'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptype']
,   'exclude'                 => true
,   'inputType'               => 'select'
,   'options'                 => array(
        'static'    => $GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'][0]
    ,   'dynamic'   => $GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'][1]
    )
);


class tl_module_storelocator extends Backend {


    public function getCategories() {
    
		$arrCalendars = array();
		$objCalendars = $this->Database->execute("SELECT id, title FROM tl_storelocator_category ORDER BY title");

		while ($objCalendars->next())
		{
				$arrCalendars[$objCalendars->id] = $objCalendars->title;
		}

		return $arrCalendars;
    }
	
	public function getCountries() {
	
		return $GLOBALS['TL_LANG']['tl_storelocator']['countries'];
	}
}
?>