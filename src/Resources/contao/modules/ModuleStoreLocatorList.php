<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2021 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2021 numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Environment;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Patchwork\Utf8;
use Symfony\Component\HttpFoundation\JsonResponse;


class ModuleStoreLocatorList extends Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_list';


    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate(): string {

        if( TL_MODE == 'BE' ) {

            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['storelocator_list'][0]).' ###';
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
    protected function compile(): void {

        global $objPage;

        $this->Template = new FrontendTemplate($this->storelocator_list_tpl?:$this->strTemplate);

        if( !isset($_GET['search']) && Config::get('useAutoItem') && isset($_GET['auto_item']) ) {
            Input::setGet('search', Input::get('auto_item'));
        }

        $sSearchVal = NULL;
        $sSearchVal = $this->Input->get('search') ? $this->Input->get('search') : NULL;

        $aStores = [];
        $filterFields = [];

        if( $this->storelocator_use_filter ) {

            $modFilter = ModuleModel::findById($this->storelocator_mod_filter);

            if( $modFilter ) {
                $filterFields = StringUtil::deserialize($modFilter->storelocator_search_in);
            }
        }

        // do not render list module if no empty search is allowed
        if( !$this->storelocator_always_show_results && !$sSearchVal ) {

            $this->Template->preventRendering = true;

        // normal search
        } else {

            $aCategories = [];
            $aCategories = StringUtil::deserialize($this->storelocator_list_categories);

            $aSearchValues = [];
            $aSearchValues = StoreLocator::parseSearchValue($sSearchVal);

            if( $aSearchValues['category'] ) {

                $objCategory = NULL;
                $objCategory = CategoriesModel::findByAlias($aSearchValues['category']);

                if( $objCategory && $objCategory->count() > 0 && in_array($objCategory->id, $aCategories) ) {
                    $category = [$objCategory->id];
                } else {
                    $category = null;
                }
            }

            // handle ajax request for searching markers in map
            if( Environment::get('isAjaxRequest') ) {

                if( Input::get('action') == "getMarkers" ) {

                    $oStores = StoresModel::searchBetweenCoords(
                        Input::get('fromlng'), Input::get('tolng'),
                        Input::get('fromlat'), Input::get('tolat'),
                        $this->storelocator_limit_marker,
                        ($category?$category:$aCategories),
                        (!empty($aSearchValues['filter'])&&$this->storelocator_use_filter&&count($filterFields))?StoreLocator::createFilterWhereClause($aSearchValues['filter'], $filterFields):NULL
                    );

                    $aJson = [];

                    if( $oStores && $oStores->count() > 0 ) {

                        $aStores = [];
                        foreach( $oStores as $entry ) {

                            StoreLocator::parseStoreData($entry, $this);
                            $aStores[] = $entry;
                        }

                        // HOOK: add custom logic to modify the entries of the list
                        if( is_array($GLOBALS['N2SL_HOOKS']['modifyListEntries']) ) {

                            foreach( $GLOBALS['N2SL_HOOKS']['modifyListEntries'] as $callback ) {

                                if( is_array($callback) ) {

                                    $this->import($callback[0]);
                                    $aStores = $this->{$callback[0]}->{$callback[1]}($aStores, $this);
                                }
                            }
                        }

                        $oTemplateInfoWindow = new FrontendTemplate('mod_storelocator_infowindow');
                        $oTemplateInfoWindow->labelPhone = $GLOBALS['TL_LANG']['tl_storelocator']['field']['phone'];
                        $oTemplateInfoWindow->labelFax = $GLOBALS['TL_LANG']['tl_storelocator']['field']['fax'];
                        $oTemplateInfoWindow->labelEMail = $GLOBALS['TL_LANG']['tl_storelocator']['field']['email'];
                        $oTemplateInfoWindow->labelWWW = $GLOBALS['TL_LANG']['tl_storelocator']['field']['www'];
                        $oTemplateInfoWindow->labelDistance = $GLOBALS['TL_LANG']['tl_storelocator']['field']['distance'];
                        $oTemplateInfoWindow->labelMore = $GLOBALS['TL_LANG']['tl_storelocator']['field']['more'];

                        foreach( $aStores as $key => $value ) {

                            $oTemplateInfoWindow->entry = $value;

                            $aJson[] = [
                                "id" => $value->id
                            ,   "pid" => $value->pid
                            ,   "lat" => $value->latitude
                            ,   "lng" => $value->longitude
                            ,   "info" => $this->replaceInsertTags($oTemplateInfoWindow->parse())
                            ];
                        }
                    }

                    $response = new JsonResponse($aJson);

                    throw new ResponseException($response);
                }
            }

            $aCountryNames = [];
            $aCountryNames = $this->getCountries();

            if( !empty($aSearchValues['term']) || $this->storelocator_always_show_results ) {

                // search for longitude and latitude
                if( !empty($aSearchValues['term']) && (empty($aSearchValues['longitude']) || empty($aSearchValues['latitude'])) ) {

                    $oSL = NULL;
                    $oSL = new StoreLocator();

                    $aCoordinates = [];
                    $aCoordinates = $oSL->getCoordinatesByString($aSearchValues['term']);

                    if( !empty($aCoordinates) ) {

                        $aSearchValues['latitude'] = $aCoordinates['latitude'];
                        $aSearchValues['longitude'] = $aCoordinates['longitude'];
                    }
                }

                $objStores = NULL;

                // search all countries
                if( !empty($aSearchValues['term']) ) {

                    $objStores = StoresModel::searchNearby(
                        $aSearchValues['latitude'], $aSearchValues['longitude'],
                        ($this->storelocator_limit_distance?$this->storelocator_max_distance:0),
                        $this->storelocator_list_limit,
                        ($category?$category:$aCategories),
                        (!empty($aSearchValues['filter'])&&$this->storelocator_use_filter)?StoreLocator::createFilterWhereClause($aSearchValues['filter'], $filterFields):NULL,
                        (!empty($aSearchValues['order'])&&!empty($aSearchValues['sort']))?$aSearchValues['order'].' '.strtoupper($aSearchValues['sort']):NULL
                    );

                // search selected country only
                } else {

                    // default sorting
                    if( !empty($this->storelocator_list_sort_field) && empty($aSearchValues['order']) ) {

                        $aSearchValues['order'] = $this->storelocator_list_sort_field;

                        if( $this->storelocator_list_sort_direction ) {
                            $aSearchValues['sort'] = substr($this->storelocator_list_sort_direction,0,3);
                        }
                    }

                    $objStores = StoresModel::searchCountry(
                        $this->storelocator_default_country,
                        $this->storelocator_list_limit,
                        ($category?$category:$aCategories),
                        (!empty($aSearchValues['filter'])&&$this->storelocator_use_filter)?StoreLocator::createFilterWhereClause($aSearchValues['filter'], $filterFields):NULL,
                        (!empty($aSearchValues['order'])&&!empty($aSearchValues['sort']))?$aSearchValues['order'].' '.strtoupper($aSearchValues['sort']):NULL
                    );
                }

                if( count($objStores) ) {

                    foreach( $objStores as $entry ) {

                        if( empty($sSearchVal) ) {
                            $entry->distance = NULL;
                        }

                        StoreLocator::parseStoreData($entry, $this);

                        $entry->class = $entry->highlight ? 'starred' : '';

                        // get image
                        if( $entry->singleSRC ) {

                            $objFile = NULL;
                            $objFile = FilesModel::findByUuid($entry->singleSRC);
                            $entry->image = $objFile;

                            $aImage = [
                                'id'         => $objFile->id
                            ,   'name'       => $objFile->basename
                            ,   'singleSRC'  => $objFile->path
                            ,   'title'      => StringUtil::specialchars($objFile->basename)
                            ,   'filesModel' => $objFile
                            ,   'size'       => $this->imgSize
                            ];

                            $this->addImageToTemplate($this->Template, $aImage, null, null, $aImage['filesModel']);

                            $entry->picture = $this->Template->picture;
                            unset($this->Template->picture);
                        }

                        if( $this->jumpTo ) {

                            $objLink = NULL;
                            $objLink = PageModel::findById($this->jumpTo);

                            if( $objLink ) {
                                $entry->link = $objLink->getFrontendUrl((!Config::get('useAutoItem')?'/store/':'/').($entry->alias?$entry->alias:$entry->id));
                            }
                        }

                        $aStores[] = $entry;
                    }

                    // HOOK: add custom logic to modify the entries of the list
                    if( is_array($GLOBALS['N2SL_HOOKS']['modifyListEntries']) ) {

                        foreach( $GLOBALS['N2SL_HOOKS']['modifyListEntries'] as $callback ) {

                            if( is_array($callback) ) {

                                $this->import($callback[0]);
                                $aStores = $this->{$callback[0]}->{$callback[1]}($aStores, $this);
                            }
                        }
                    }

                    if( $this->storelocator_show_map ) {

                        if( $aStores && count($aStores) > 0 ) {

                            $oTemplateInfoWindow = new FrontendTemplate('mod_storelocator_infowindow');
                            $oTemplateInfoWindow->labelPhone = $GLOBALS['TL_LANG']['tl_storelocator']['field']['phone'];
                            $oTemplateInfoWindow->labelFax = $GLOBALS['TL_LANG']['tl_storelocator']['field']['fax'];
                            $oTemplateInfoWindow->labelEMail = $GLOBALS['TL_LANG']['tl_storelocator']['field']['email'];
                            $oTemplateInfoWindow->labelWWW = $GLOBALS['TL_LANG']['tl_storelocator']['field']['www'];
                            $oTemplateInfoWindow->labelDistance = $GLOBALS['TL_LANG']['tl_storelocator']['field']['distance'];
                            $oTemplateInfoWindow->labelMore = $GLOBALS['TL_LANG']['tl_storelocator']['field']['more'];

                            foreach( $aStores as $key => $value ) {

                                $oTemplateInfoWindow->entry = $value;

                                $aStores[$key]->info = json_encode($this->replaceInsertTags($oTemplateInfoWindow->parse()));
                            }
                        }

                        if( $this->storelocator_provider === 'google-maps' ) {
                            $this->addGoogleMap($aStores);
                        } else {
                            // HOOK for adding custom javascript provider
                        }
                    }
                }

                if( !count($aStores) ) {
                    $this->Template->noResults = true;
                }
            }
        }

        $this->Template->labelPhone = $GLOBALS['TL_LANG']['tl_storelocator']['field']['phone'];
        $this->Template->labelFax = $GLOBALS['TL_LANG']['tl_storelocator']['field']['fax'];
        $this->Template->labelEMail = $GLOBALS['TL_LANG']['tl_storelocator']['field']['email'];
        $this->Template->labelWWW = $GLOBALS['TL_LANG']['tl_storelocator']['field']['www'];
        $this->Template->labelDistance = $GLOBALS['TL_LANG']['tl_storelocator']['field']['distance'];
        $this->Template->labelMore = $GLOBALS['TL_LANG']['tl_storelocator']['field']['more'];
        $this->Template->msgNoResults = $GLOBALS['TL_LANG']['tl_storelocator']['noresults'];

        $this->Template->stores = $aStores;
    }


    /**
     * Add necessary template for google map
     *
     * @param array $aStores
     */
    private function addGoogleMap( $aStores=NULL ): void {

        global $objPage;

        $this->Template->showMap = true;

        $oTemplateGoogleMap = new FrontendTemplate('script_storelocator_googlemap');
        $oTemplateGoogleMap->country = $this->storelocator_default_country;
        $oTemplateGoogleMap->mapsKey = Config::get('google_maps_browser_key');
        $mapPins = [];

        if( !$oTemplateGoogleMap->mapsKey ) {
            return;
        }

        if( $this->storelocator_map_pin ) {
            $mapPins['default'] = $this->storelocator_map_pin;
        }

        // gather pins graphics
        $oMapPins = NULL;
        $oMapPins = CategoriesModel::getMapPins();
        $oMapPins = $oMapPins->fetchAll();

        foreach( $oMapPins as $key => $value ) {

            if( !empty($value['map_pin']) ) {
                $mapPins[$value['id']] = $value['map_pin'];
            }
        }

        foreach( $mapPins as $key => $value ) {

            $oFile = NULL;
            $oFile = FilesModel::findByUuid($value);

            if( !empty($oFile->path) ) {
                $mapPins[$key] = $oFile->path;
            } else {
                unset($mapPins[$key]);
            }
        }

        $oTemplateGoogleMap->mapPins = $mapPins;

        $oTemplateGoogleMap->loadMoreResults = $this->storelocator_load_results_on_pan;
        $oTemplateGoogleMap->mapInteraction = $this->storelocator_map_interaction;
        $oTemplateGoogleMap->listInteraction = $this->storelocator_list_interaction;
        $oTemplateGoogleMap->markerclusterer = $this->storelocator_markerclusterer;
        $oTemplateGoogleMap->loadedMapsApi = $objPage->loadedMapsApi;
        $oTemplateGoogleMap->entries = array_slice($aStores,0,500,true);

        $this->Template->scriptMap = $oTemplateGoogleMap->parse();
    }
}
