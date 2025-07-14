<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\Input;


if( Input::get('do') === 'storelocator' ) {
    $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = ['name', 'postal', 'city', 'tstamp'];
}