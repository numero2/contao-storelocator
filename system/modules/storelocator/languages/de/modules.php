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
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['storelocator'] = array('Händler', 'Händler-Listen verwalten und geographische Suche ermöglichen.');


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['storelocator'] = array('Händler', '');
$GLOBALS['TL_LANG']['FMD']['storelocator_search'] = array('Händlersuche', 'fügt eine Suchmaske ein.');
$GLOBALS['TL_LANG']['FMD']['storelocator_list'] = array('Händlerliste', 'fügt eine Liste aller Händler zur Seite hinzu.');
$GLOBALS['TL_LANG']['FMD']['storelocator_details'] = array('Händlerdetails', 'zeigt Details zum ausgewählten Händler an.');

$GLOBALS['TL_LANG']['tl_module']['storelocator_search_country'] = array('Standardland', 'In welchem Land sollen die Ergebnisse gesucht werden (falls der Benutzer kein anderes ausgewählt hat)?');
$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl'] = array('Template', 'Wählen Sie mit welchen Template die Suchmaske dargestellt werden soll.');

$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl'] = array('Template', 'Wählen Sie mit welchen Template die Händler dargestellt werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories'] = array('Händlerkategorien', 'Aus welchen Händlerlisten sollen Einträge angezeigt werden?');
$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit'] = array('Anzahl der Ergebnisse', 'Wieviele Ergebnisse sollen maximal angezeigt werden?');

$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl'] = array('Template', 'Wählen Sie mit welchen Template der Händler dargestellt werden soll.');
?>