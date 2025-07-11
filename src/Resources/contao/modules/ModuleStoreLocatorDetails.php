<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use \stdClass;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\FrontendTemplate;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\FilesModel;
use Contao\Input;
use Contao\Module;
use Contao\System;


class ModuleStoreLocatorDetails extends Module {


    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_storelocator_details';


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

            $objTemplate->wildcard = '### '.$GLOBALS['TL_LANG']['FMD']['storelocator_details'][0].' ###';
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
    protected function compile(): void {

        $this->Template->referer = 'javascript:history.go(-1)';
        $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        if( !isset($_GET['store']) && Config::get('useAutoItem') && isset($_GET['auto_item']) ) {
            Input::setGet('store', Input::get('auto_item'));
        }

        $alias = Input::get('store') ? Input::get('store') : null;

        $objStore = null;
        $objStore = StoresModel::findByIdOrAlias($alias);

        if( !$objStore || !$objStore->published ) {
            throw new PageNotFoundException('store not found');
        }

        // set page title (if empty)
        $request = System::getContainer()->get('request_stack')->getMainRequest();
        $responseContext = System::getContainer()->get('contao.routing.response_context_accessor')->getResponseContext();

        if( $responseContext?->has(HtmlHeadBag::class) && empty($request->get('pageModel')?->pageTitle) ) {

            $htmlHeadBag = $responseContext->get(HtmlHeadBag::class);
            $htmlHeadBag->setTitle($objStore->name);
        }

        // get image
        if( $objStore->singleSRC ) {

            $objFile = null;
            $objFile = FilesModel::findByUuid($objStore->singleSRC);
            $objStore->image = $objFile;
        }

        $this->Template->labelPhone = $GLOBALS['TL_LANG']['tl_storelocator']['field']['phone'];
        $this->Template->labelFax = $GLOBALS['TL_LANG']['tl_storelocator']['field']['fax'];
        $this->Template->labelEMail = $GLOBALS['TL_LANG']['tl_storelocator']['field']['email'];
        $this->Template->labelWWW = $GLOBALS['TL_LANG']['tl_storelocator']['field']['www'];

        $this->Template->maps_provider = $this->storelocator_provider;

        if( $this->storelocator_provider === 'google-maps' ) {

            $this->Template->mapsURI = sprintf(
                "https://www.google.com/maps/embed/v1/place?q=%s&key=%s"
                ,   rawurlencode($objStore->name.', '.$objStore->street.', '.$objStore->postal.' '.$objStore->city)
                ,   Config::get('google_maps_browser_key')
            );

        } else if( $this->storelocator_provider === 'leaflet' ) {

            // setup a canvas for the leaflet map
            $html = "<div id='map-canvas' style='width:600px;height:450px'></div>";

            // create template to load and add the leaflet map
            $leafletTemplate = new FrontendTemplate('script_storelocator_leafletmap_simple');

            // marker data
            $leafletTemplate->latitude = $objStore->latitude;
            $leafletTemplate->longitude = $objStore->longitude;
            $leafletTemplate->markerInfo = $objStore->name.', '.$objStore->street.', '.$objStore->postal.' '.$objStore->city;

            // parse the template
            $html .= $leafletTemplate->parse();
            
            // append the rendered html
            $this->Template->scriptMap = $html;

        }

        if( $objStore->image ) {

            $temp = new stdClass();

            // Contao >= 4.9
            if( method_exists($this, 'addImageToTemplate') ) {

                $this->addImageToTemplate($this->Template, [
                    'singleSRC' => $objStore->image->path
                ,   'size' => $this->imgSize
                ], null, null, $objFile);

            // Contao 5
            } else {

                $figureBuilder = System::getContainer()
                    ->get('contao.image.studio')
                    ->createFigureBuilder()
                    ->from($objStore->image->path)
                    ->setSize($this->imgSize);

                if( null !== ($figure = $figureBuilder->buildIfResourceExists()) ) {
                    $figure->applyLegacyTemplateData($this->Template);
                }
            }
        }

        StoreLocator::parseStoreData($objStore, $this);

        $this->Template->store = $objStore;
    }
}
