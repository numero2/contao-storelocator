<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2019 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2019 numero2 - Agentur für digitales Marketing
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class ModuleStoreLocatorStaticMap extends \Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_static_map';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate() {

        if( TL_MODE == 'BE' ) {

            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### STORELOCATOR STATIC MAP ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile() {

        global $objPage;

        $this->Template = new \FrontendTemplate($this->storelocator_static_map_tpl?:$this->strTemplate);
        $mapApiBase = "https://maps.googleapis.com/maps/api/staticmap";

        $parameters = array();

        if( !empty($this->storelocator_center) ) {
            $parameters[] = "center=".$center;
        }

        if( !empty($this->storelocator_zoom) ) {
            $parameters[] = "zoom=".$this->storelocator_zoom;
        }

        $size = deserialize($this->storelocator_size,1);
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
        $cats = deserialize($this->storelocator_search_categories);
        if( !empty($cats) ){

            $limit = $this->storelocator_limit_marker_static;
            if( $limit < 0 || $limit >=50 ){
                $limit = 50;
            }
            $objMarkers = StoresModel::findBy(array("pid in (".implode(',',array_pad(array(), count($cats), "?")).")"), $cats, array('limit'=>$limit));

            $aMarkers = array();
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
        $parameters[] = "key=".\Config::get('google_maps_browser_key');

        $this->Template->mapLink = $mapApiBase."?".implode("&", $parameters);


        if( $this->jumpTo ) {

            $objLink = NULL;
            $objLink = \PageModel::findById($this->jumpTo);

            $this->Template->href = $this->generateFrontendUrl($objLink->row());
        }

    }
}
