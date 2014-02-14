<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
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


$GLOBALS['TL_LANG']['tl_storelocator_stores'] = array
(
    // Fields
    'name'          => array('Name', 'Name des Händlers ein.'),
    'email'         => array('E-Mail', 'E-Mail-Adresse zur Kontaktaufnahme'),
    'url'           => array('Webseite', 'Geben Sie die URL zur Webseite des Händlers an'),
    'phone'         => array('Telefon', 'Telefonnummer'),
    'fax'           => array('Fax', 'Faxnummer'),
    'street'        => array('Strasse', 'Name der Strasse und Hausnummer'),
    'postal'        => array('Postleitzahl', 'Postleitzahl'),
    'city'          => array('Stadt', 'Name der Stadt'),
    'country'       => array('Land', 'Bitte wähle ein Land aus.'),
    'logo'          => array('Händlerlogo', 'Wählen Sie ein Händlerlogo aus.'),
    'opening_times' => array('Öffnungszeiten', ''),
    'times_weekday' => array('Wochentag', ''),
    'times_from'    => array('von', ''),
    'times_to'      => array('bis', ''),
    'longitude'     => array('Längengrad', 'Wird automatisch ausgefüllt...'),
    'latitude'      => array('Breitengrad', 'Wird automatisch ausgefüllt...'),
    'map'           => array('Kartenansicht'),
    'geo_explain'   => array('Die geographischen Koordinaten werden benötigt damit der Besucher später nach einem Händler in seiner Nähe suchen kann. Diese beiden Felder werden automatisch beim Speichern ausgefüllt, können aber bei Bedarf manuell korrigiert werden.'),
    'description'   => array('Beschreibung', 'Beschreibung des Händlers'),

    // Legends
    'common_legend' => 'Allgemein',
    'adress_legend' => 'Adressdaten',
    'geo_legend' 	=> 'Koordinaten',
    'times_legend' 	=> 'Öffnungszeiten',

    // Buttons
    'new'           => array('Neuer Händler', 'Neuen Händler anlegen'),
    'coords'   		=> array('Noch keine Geo-Koordinaten vorhanden!', 'Geo-Koordinaten vorhanden'),
    'edit'   		=> array('Händler bearbeiten', 'Händler mit der ID %s bearbeiten'),
    'copy'   		=> array('Händler kopieren', 'Händler mit der ID %s kopieren'),
    'delete' 		=> array('Händler löschen', 'Händler mit der ID %s löschen'),
    'importStores' 	=> array('CSV-Import', 'Händler aus CSV-Datei importieren')
);
