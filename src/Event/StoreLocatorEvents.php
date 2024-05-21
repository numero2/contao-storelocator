<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\Event;


final class StoreLocatorEvents {


    /**
     * The contao.contact_person_parse event is triggered during parsing a contact person entry.
     *
     * @see numero2\StoreLocatorBundle\Event\StoreImportEvent
     */
    public const STORE_IMPORT = 'contao.storelocator_store_import';
}
