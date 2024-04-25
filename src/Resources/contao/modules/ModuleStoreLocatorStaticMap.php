<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;


class ModuleStoreLocatorStaticMap extends Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_static_map';


    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate(): string {

        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
        $requestStack = System::getContainer()->get('request_stack');

        if( $scopeMatcher->isBackendRequest($requestStack->getCurrentRequest()) ) {

            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.$GLOBALS['TL_LANG']['FMD']['storelocator_static_map'][0].' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = System::getContainer()->get('router')->generate(
                'contao_backend',
                ['do' => 'themes', 'table' => 'tl_module', 'act' => 'edit', 'id' => $this->id],
            );

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile() {

        global $objPage;

        $mapApiBase = "https://maps.googleapis.com/maps/api/staticmap";

        $parameters = [];

        if( !empty($this->storelocator_center) ) {
            $parameters[] = "center=".$this->storelocator_center;
        }

        if( !empty($this->storelocator_zoom) ) {
            $parameters[] = "zoom=".$this->storelocator_zoom;
        }

        $size = StringUtil::deserialize($this->storelocator_size,1);
        if( !empty($size) ){
            $parameters[] = "size=".$size[0]."x".$size[1];
            $this->Template->size = $size;
        }

        // don't sent parameters with default values
        if( $this->storelocator_scale !== "1" ) {
            $parameters[] = "scale=".$this->storelocator_scale;
        }
        if( $this->storelocator_maptype !== "roadmap" ) {
            $parameters[] = "maptype=".$this->storelocator_maptype;
        }
        if( $this->storelocator_format !== "png" ) {
            $parameters[] = "format=".$this->storelocator_format;
        }
        if( $objPage->rootLanguage !== "en" ) {
            $parameters[] = "language=".$objPage->rootLanguage;
        }

        // resolve markers
        $cats = StringUtil::deserialize($this->storelocator_search_categories);
        if( !empty($cats) ){

            $limit = $this->storelocator_limit_marker_static;
            if( $limit < 0 || $limit >=50 ) {
                $limit = 50;
            }
            $objMarkers = StoresModel::findBy(["pid in (".implode(',',array_pad([], count($cats), "?")).")"], $cats, ['limit'=>$limit]);

            $aMarkers = [];
            foreach( $objMarkers as $marker ) {

                if( !empty($marker->latitude) && !empty($marker->longitude) ){

                    $aMarkers[] = $marker->latitude.",".$marker->longitude;
                }
            }

            if( !empty($aMarkers) ){
                $parameters[] = "markers=".implode('|', $aMarkers);
            }
        }

        // generate imageUrl
        $parameters[] = "key=".Config::get('google_maps_browser_key');

        $this->Template->mapLink = $mapApiBase."?".implode("&", $parameters);

        if( $this->jumpTo ) {

            $objLink = PageModel::findById($this->jumpTo);

            if( $objLink ) {
                $this->Template->href = $objLink->getFrontendUrl();
            }
        }
    }
}
