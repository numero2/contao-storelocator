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
 * Namespace
 */
namespace numero2\StoreLocator;


class ModuleStorelocatorInsertTags extends \Controller {


	/**
	 * Replace matching inserttags
	 * @param string InsertTag
	 * @param bool Use cache
	 * @return string
	 */
	protected function replaceInsertTags($strBuffer, $blnCache=false) {

		$this->import('Database');

        $aParams = explode('::', $strBuffer);

        switch( $aParams[0] ) {

            case 'store' :

				$this->Template = new FrontendTemplate('mod_storelocator_inserttag');

				// find store
				$objStore = NULL;
				$objStore = $this->Database->prepare("SELECT * FROM `tl_storelocator_stores` WHERE `id` = ? ")->limit(1)->execute( $aParams[1] );

				$entry = NULL;
				$entry = $objStore->fetchAssoc();

				// get opening times
				$entry['opening_times'] = unserialize( $entry['opening_times'] );
				$entry['opening_times'] = !empty($entry['opening_times'][0]['from']) ? $entry['opening_times'] : NULL;

				// set country name
				$aCountryNames = $this->getCountries();
				$entry['country_code'] = $entry['country'];
				$entry['country_name'] = $aCountryNames[$entry['country']];

				if( !$objStore )
					return false;

				$this->Template->entry = $entry;

				$sTemplate = $this->Template->parse();
				$sTemplate = Controller::replaceInsertTags($sTemplate);

				return $sTemplate;

            break;

            // not our insert tag?
            default :
                return false;
            break;
        }

        return false;
    }
}

?>