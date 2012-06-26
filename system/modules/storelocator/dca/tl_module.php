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
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_list'] = '{title_legend},name,headline,type;{config_legend:hide},storelocator_list_categories,storelocator_list_limit;{template_legend:hide},storelocator_list_tpl;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['storelocator_search'] = '{title_legend},name,headline,type;{config_legend:hide},jumpTo,storelocator_search_country;{template_legend:hide},storelocator_search_tpl;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl'],
	'default'                 => 'mod_storelocator_list',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => $this->getTemplateGroup('mod_storelocator_'),
	'eval'                    => array('tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_categories'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_storelocator', 'getCategories'),
	'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_list_limit'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit'],
	'default'                 => '10',
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp' => 'digit', 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_tpl'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl'],
	'default'                 => 'mod_storelocator_search',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => $this->getTemplateGroup('mod_storelocator_'),
	'eval'                    => array('tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_module']['fields']['storelocator_search_country'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['storelocator_search_country'],
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_storelocator', 'getCountries'),
	'search'                  => true,
	'eval'                    => array('mandatory'=>true, 'maxlength'=>2, 'tl_class'=>'w50')
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