<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2020 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2020 numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator\DCAHelper;

use Contao\Backend;
use Contao\Input;
use Contao\StringUtil;
use Contao\Image;
use Contao\DataContainer;
use Contao\Config;
use numero2\StoreLocator\StoresModel;
use numero2\StoreLocator\Geocoder;


class Stores extends Backend {

    /**
     * Return the "highlight/unhighlight store" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function iconHighlight( $row, $href, $label, $title, $icon, $attributes ) {

        if( strlen(Input::get('fid')) ) {

            $this->toggleFeatured( Input::get('fid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null) );
            $this->redirect( $this->getReferer() );
        }

        $href .= '&amp;fid='.$row['id'].'&amp;state='.($row['highlight'] ? '' : 1);

        if( !$row['highlight'] ) {
            $icon = 'featured_.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['highlight'] ? 1 : 0) . '"').'</a> ';
    }


    /**
     * Highlight/unhighlight a store
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     *
     * @return string
     */
    public function toggleFeatured( $intId, $blnVisible, DataContainer $dc=null ) {

        $oStore = NULL;
        $oStore = StoresModel::findById( $intId );

        if( $oStore ) {
            $oStore->highlight = ($blnVisible ? 1 : 0);
            $oStore->save();
        }
    }


    /**
     * Auto-generate an category alias if it has not been set yet
     *
     * @param mixed         $varValue
     * @param DataContainer $dc
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateAlias( $varValue, DataContainer $dc ) {

        $autoAlias = false;

        // Generate an alias if there is none
        if( $varValue == '' ) {
            $autoAlias = true;
            $varValue = StringUtil::generateAlias($dc->activeRecord->name);
        }

        $oAlias = NULL;
        $oAlias = $this->Database->prepare("SELECT id FROM tl_storelocator_stores WHERE id=? OR alias=?")
                                   ->execute($dc->activeRecord->id, $varValue);

        // Check whether the alias exists
        if( $oAlias && $oAlias->count() > 1 ) {

            if( !$autoAlias ) {
                throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
            }

            $varValue .= '-' . $dc->id;
        }

        return $varValue;
    }


    /**
     * Generates button to show if coordinates are available
     *
     * @param array    $row
     * @param srting   $href
     * @param array    $label
     * @param string   $title
     * @param mixed    $icon
     * @param array    $attributes
     *
     * @return string
     */
    public function coordsButton( $row=NULL, $href=NULL, $label=NULL, $title=NULL, $icon=NULL, $attributes=NULL ) {

        $icon  = ($row['latitude'] || $row['longitude']) ? $icon[1] : $icon[0];
        $label = ($row['latitude'] || $row['longitude']) ? $title : $label;

        return '<span title="'.specialchars($label).'"'.$attributes.'>'.$this->generateImage($icon,$label).'</span> ';
    }


    /**
     * Listing for overview
     *
     * @param array $arrRow
     *
     * @return string
     */
    public function listStores( $arrRow ) {
        return '<div class="limit_height block">
            <p>' . $arrRow['name'] . ' <span style="color:#b3b3b3;"><em>(' . $arrRow['postal'] . ' ' . $arrRow['city'] . ')</em></span></p>'
            . '</div>' . "\n";
    }


    /**
     * Displays a little static Google Map with position of the address
     *
     * @param Contao\DataContainer $dc
     *
     * @return string
     */
    public function showMap( DataContainer $dc ) {

        $imgPath = '';

        if( Geocoder::getInstance()->hasProvider('google-maps') ) {
            if( !empty($dc->activeRecord->latitude) && !empty($dc->activeRecord->longitude) ) {

                $sCoords = sprintf(
                    "%s,%s"
                    ,    $dc->activeRecord->latitude
                    ,    $dc->activeRecord->longitude
                );

                $imgPath = 'https://maps.google.com/maps/api/staticmap?center='.$sCoords.
                    '&zoom=16&size=320x139&maptype=roadmap&markers=color:red|label:|'.$sCoords.'&key='.Config::get('google_maps_browser_key');
            }
        }


        return '<div class="google-map">'
        .'<h3><label>'.$GLOBALS['TL_LANG']['tl_storelocator_stores']['map'][0].'</label></h3> '
        .($imgPath?'<img width="320" height="139" src="'.$imgPath.'" />':'<div class="img" style="width:320px;height:139px;"></div>')
        .'</div>';
    }


    /**
     * Shows a little info text what coordinates are
     *
     * @return string
     */
    public function showGeoExplain() {

        return '<div class="widget clr"><p class="tl_help tl_tip heightAuto">'.$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_explain'][0].'</p></div>';
    }


    /**
     * Add leading "http://" if missing
     *
     * @param mixed            $varValue
     * @param DataContainer    $dc
     *
     * @return string
     */
    public function checkURL( $varValue, DataContainer $dc ) {

        return ( $varValue && strpos($varValue,'http') === FALSE ) ? 'http://'.$varValue : $varValue;
    }


    /**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon( $row, $href, $label, $title, $icon, $attributes ) {

        if( Input::get('tid') ) {

            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if( !$row['published'] ) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"').'</a> ';
    }


    /**
     * publish/unpublish a store
     *
     * @param integer              $intId
     * @param boolean              $blnVisible
     * @param Contao\DataContainer $dc
     */
    public function toggleVisibility( $intId, $blnVisible, DataContainer $dc=null ) {

        $oStore = NULL;
        $oStore = StoresModel::findById( $intId );

        if( $oStore ) {
            $oStore->published = ($blnVisible ? 1 : 0);
            $oStore->save();
        }
    }
}
