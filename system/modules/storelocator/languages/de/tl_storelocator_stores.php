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
$GLOBALS['TL_LANG']['tl_storelocator_stores']['name']   		= array('Name', 'Name des Händlers ein.');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['email']   		= array('E-Mail', 'E-Mail-Adresse zur Kontaktaufnahme');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['url']   			= array('Webseite', 'Geben Sie die URL zur Webseite des Händlers an');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['phone']   		= array('Telefon', 'Telefonnummer');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['fax']   			= array('Fax', 'Faxnummer');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['street']   		= array('Strasse', 'Name der Strasse und Hausnummer');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['postal']   		= array('Postleitzahl', 'Postleitzahl');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['city']   		= array('Stadt', 'Name der Stadt');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['country']		= array('Land', 'Bitte wähle ein Land aus.');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['opening_times']	= array('Öffnungszeiten', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_weekday']	= array('Wochentag', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_from']		= array('von', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_to']		= array('bis', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['longitude']		= array('Längengrad', 'Wird automatisch ausgefüllt...');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']		= array('Breitengrad', 'Wird automatisch ausgefüllt...');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['map']			= array('Kartenansicht');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_explain']	= array('Die geographischen Koordinaten werden benötigt damit der Besucher später nach einem Händler in seiner Nähe suchen kann. Diese beiden Felder werden automatisch beim Speichern ausgefüllt, können aber bei Bedarf manuell korrigiert werden.');
 

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_storelocator_stores']['common_legend']  = 'Allgemein';
$GLOBALS['TL_LANG']['tl_storelocator_stores']['adress_legend']  = 'Adressdaten';
$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_legend'] 	= 'Koordinaten';
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_legend'] 	= 'Öffnungszeiten';
 

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_storelocator_stores']['new']    		= array('Neuer Händler', 'Neuen Händler anlegen');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['coords']   		= array('Noch keine Geo-Koordinaten vorhanden!', 'Geo-Koordinaten vorhanden');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['edit']   		= array('Händler bearbeiten', 'Händler mit der ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['copy']   		= array('Händler kopieren', 'Händler mit der ID %s kopieren');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['delete'] 		= array('Händler löschen', 'Händler mit der ID %s löschen');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['importStores'] 	= array('CSV-Import', 'Händler aus CSV-Datei importieren');
?>