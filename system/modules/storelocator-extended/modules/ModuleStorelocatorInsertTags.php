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


class ModuleStorelocatorInsertTags extends Controller
{

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
				$aCountryNames = Contao\System::getCountries();
				$entry['country_code'] = $entry['country'];
				$entry['country_name'] = $aCountryNames[$entry['country']];

                // get store logo
                $objLogo = FilesModel::findByUuid($entry['logo']);
                if ( $objLogo !== null )
                {
                    $arrLogo = $objLogo->row();
                    $arrLogo['meta'] = unserialize($arrLogo['meta']);

                    $strLogo = sprintf('<img src="%s" alt="%s" title="%s">',
                            $arrLogo['path'],
                            $arrLogo['meta'][ $GLOBALS['TL_LANGUAGE'] ]['caption'],
                            $arrLogo['meta'][ $GLOBALS['TL_LANGUAGE'] ]['title']
                    );

                    $this->Template->logo    = $strLogo;
                    $this->Template->arrLogo = $arrLogo;
                }

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