<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator\DCAHelper;

use Contao\Config;
use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Exception;
use numero2\StoreLocator\StoresModel;


class Stores {


    /**
     * Return the "highlight/unhighlight store" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param array|string|null $icon
     * @param string $attributes
     *
     * @return string
     */
    public function iconHighlight( array $row, ?string $href, ?string $label, ?string $title, $icon=null, ?string $attributes=null ): string {

        if( strlen(Input::get('fid')) ) {

            $this->toggleFeatured(Input::get('fid'), (Input::get('state') == 1));
            Controller::redirect(System::getReferer());
        }

        $href .= '&amp;fid='.$row['id'].'&amp;state='.($row['highlight'] ? '' : 1);

        if( !$row['highlight'] ) {
            $icon = 'featured_.gif';
        }

        return '<a href="'.Controller::addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['highlight'] ? 1 : 0) . '"').'</a> ';
    }


    /**
     * Highlight/unhighlight a store
     *
     * @param string $intId
     * @param bool $blnFeatured
     * @param Contao\DataContainer $dc
     *
     * @return string
     */
    public function toggleFeatured( string $intId, bool $blnFeatured, ?DataContainer $dc=null ): void {

        $oStore = null;
        $oStore = StoresModel::findById( $intId );

        if( $oStore ) {
            $oStore->highlight = ($blnFeatured ? '1' : '');
            $oStore->save();
        }
    }


    /**
     * Auto-generate a store alias if it has not been set yet
     *
     * @param mixed $varValue
     * @param Contao\DataContainer $dc
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateAlias( $varValue, DataContainer $dc ): string {

        $autoAlias = false;

        // Generate an alias if there is none
        if( $varValue == '' ) {
            $autoAlias = true;
            $varValue = StringUtil::generateAlias($dc->activeRecord->name);
        }

        $oAlias = null;
        $oAlias = Database::getInstance()->prepare("SELECT id FROM tl_storelocator_stores WHERE id=? OR alias=?")
            ->execute($dc->activeRecord->id, $varValue);

        // Check whether the alias exists
        if( $oAlias && $oAlias->count() > 1 ) {

            if( !$autoAlias ) {
                throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
            }

            $varValue .= '-' . $dc->id;
        }

        return $varValue;
    }


    /**
     * Generates button to show if coordinates are available
     *
     * @param array $row
     * @param srting $href
     * @param string $label
     * @param string $title
     * @param array|string|null $icon
     * @param string $attributes
     *
     * @return string
     */
    public function coordsButton( array $row, ?string $href, ?string $label, ?string $title, $icon=null, ?string $attributes=null ): string {

        $icon  = ($row['latitude'] || $row['longitude']) ? $icon[1] : $icon[0];
        $label = ($row['latitude'] || $row['longitude']) ? $title : $label;

        return '<span title="'.StringUtil::specialchars($label).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</span> ';
    }


    /**
     * Listing for overview
     *
     * @param array $arrRow
     *
     * @return string
     */
    public function listStores( array $arrRow ): string {
        return '<div class="block">'
            . $arrRow['name'] . ' <span style="color:#b3b3b3;"><em>(' . $arrRow['postal'] . ' ' . $arrRow['city'] . ')</em></span>'
            . '</div>' . "\n";
    }


    /**
     * Displays a little static map with the position of the stores address
     *
     * @param Contao\DataContainer $dc
     *
     * @return string
     */
    public function showMap( DataContainer $dc ): string {

        $imgPath = '';
        $provider = Config::get('sl_provider_backend');

        if( $provider === 'hide' ) {
            return '';
        }

        if( !empty($dc->activeRecord->latitude) && !empty($dc->activeRecord->longitude) ) {

            $sCoords = sprintf(
                "%s,%s"
                ,    $dc->activeRecord->latitude
                ,    $dc->activeRecord->longitude
            );

            $geocoder = System::getContainer()->get('numero2_storelocator.geocoder');

            if( (empty($provider) || $provider === 'google-maps' ) && $geocoder->hasProvider('google-maps') && Config::get('google_maps_browser_key') ) {

                $imgPath = '//maps.google.com/maps/api/staticmap?center='.$sCoords
                .'&zoom=16&size=565x150&maptype=roadmap&markers=color:red|label:|'.$sCoords.'&key='.Config::get('google_maps_browser_key');

            } else if( (empty($provider) || $provider === 'bing-map' ) && $geocoder->hasProvider('bing-map') ) {

                $imgPath = '//dev.virtualearth.net/REST/v1/Imagery/Map/Road/'.$sCoords.'/16?mapSize=565,150&pp='.$sCoords.';66&mapLayer=Basemap,Buildings&key='.Config::get('bing_map_server_key');

            } else if( (empty($provider) || $provider === 'here' ) && $geocoder->hasProvider('here') ) {

                $imgPath = '//image.maps.ls.hereapi.com/mia/1.6/mapview?z=16&w=565&h=150&f=0&poi='.$sCoords.'&apiKey='.Config::get('here_server_key');
            }
        }

        $html = '<div class="widget sl-google-map">';
        if( !empty($imgPath) ) {
            $html .= '<img width="565" height="150" src="'.$imgPath.'" />';
        } else {
            $html .= '<div class="img"><p>'.$GLOBALS['TL_LANG']['tl_storelocator']['backend_map_error'].'</p></div>';
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * Shows a little info text what coordinates are
     *
     * @return string
     */
    public function showGeoExplain(): string {

        return '<div class="widget clr"><p class="tl_help tl_tip heightAuto">'.$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_explain'][0].'</p></div>';
    }


    /**
     * Add leading "https://" if missing
     *
     * @param mixed $varValue
     * @param Contao\DataContainer $dc
     *
     * @return string
     */
    public function checkURL( $varValue, DataContainer $dc ): string {

        return ( $varValue && strpos($varValue,'http') !== 0 ) ? 'https://'.$varValue : $varValue;
    }


    /**
     * Return the "toggle visibility" button
     *
     * @param array $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param array|string|null $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon( array $row, ?string $href, ?string $label, ?string $title, $icon=null, ?string $attributes=null ): string {

        if( !$row['published'] ) {
            $icon = 'invisible.svg';
        }

        if( Input::get('tid') ) {

            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            Controller::redirect(System::getReferer());
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);
        return '<a href="'.Controller::addToUrl($href).'" title="'.StringUtil::specialchars($title).'" onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,\''.$row['id'].'\')">'.Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"').'</a> ';

    }


    /**
     * Publish / unpublish a store
     *
     * @param string $intId
     * @param bool $blnVisible
     * @param Contao\DataContainer $dc
     */
    public function toggleVisibility( string $intId, $blnVisible, DataContainer $dc=null ): void {

        $oStore = null;
        $oStore = StoresModel::findById( $intId );

        if( $oStore ) {
            $oStore->published = ($blnVisible ? '1' : '');
            $oStore->save();
        }
    }


    /**
     * Generate options for countries
     *
     * @return array
     */
    public static function getCountries(): array {

        if( System::getContainer()->has('contao.intl.countries') ) {
            $countries = System::getContainer()->get('contao.intl.countries')->getCountries();
            return array_change_key_case($countries, CASE_LOWER);
        } else {
            return System::getCountries();
        }
    }
}