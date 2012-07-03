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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_storelocator_category']['title'] = array('Bezeichnung', 'Bitte gib eine Bezeichnung für die Liste ein.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_storelocator_category']['title_legend'] = 'Allgemein';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_storelocator_category']['new']    = array('Neue Liste', 'Eine neue Liste anlegen');
$GLOBALS['TL_LANG']['tl_storelocator_category']['show']   = array('Details', 'Details der Liste mit der ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_storelocator_category']['edit']   = array('Liste bearbeiten', 'Liste mit der ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_storelocator_category']['copy']   = array('Liste kopieren', 'Liste mit der ID %s kopieren');
$GLOBALS['TL_LANG']['tl_storelocator_category']['delete'] = array('Liste löschen', 'Liste mit der ID %s löschen');


/**
 * Messages
 */
$GLOBALS['TL_LANG']['tl_storelocator_category']['allow_url_fopen_disabled'] = 'Damit die Erweiterung korrekt funktioniert muss die PHP-Direktive allow_url_fopen auf den Wert "On" gesetzt werden!';
$GLOBALS['TL_LANG']['tl_storelocator_category']['file_get_contents'] = 'Ihre PHP-Version ist zu alt oder falsch konfiguriert. Bitte aktualisieren Sie auf eine Version die die Funktion "file_get_contents" unterstützt!';

?>