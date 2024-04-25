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

use Contao\Backend;
use Contao\Image;
use Contao\System;
use Contao\Widget;


class OpeningTimes extends Widget {


    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = true;

    /**
     * Add a for attribute
     * @var boolean
     */
    protected $blnForAttribute = false;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_widget';


    /**
     * Trim values
     *
     * @param mixed $varInput
     *
     * @return mixed
     */
    protected function validator($varInput) {

        $dcas = [];
        $dcas = self::generateWidgetsDCA();

        if( $varInput && !empty($varInput) ) {
            foreach( $varInput as $row => $rValue ) {
                foreach( $dcas as $key => $field ) {

                    $field['value'] = $rValue[$key];

                    $strClass = $GLOBALS['BE_FFL'][$field['inputType']];
                    if( !class_exists($strClass) ) {
                        continue;
                    }
                    $cField = new $strClass($strClass::getAttributesFromDca(
                        $field,
                        $this->arrConfiguration['strField'].'[0]['.$key.']',
                        ( !empty($rValue[$key])?$rValue[$key]:null )
                    ));

                    $cField->validate();
                    if( $cField->hasErrors() ){
                        $this->class = 'error';
                        $this->arrErrors[$row][$key] = $cField->arrErrors;
                    }
                    $this->blnHasError = $this->blnHasError || $cField->hasErrors();
                }
            }
        }

        return ($this->blnHasError) ? false : (empty($varInput)?'':serialize($varInput));
    }


    /**
     * Generate the widget and returns it as a string
     *
     * @return string
     */
    public function generate(): string {

        if( empty($GLOBALS['TL_CSS']) || array_search('bundles/storelocator/backend.css', $GLOBALS['TL_CSS']) === FALSE ) {
            $GLOBALS['TL_CSS'][] = 'bundles/storelocator/backend.css';
        }

        $dcas = [];
        $dcas = self::generateWidgetsDCA();
        $numFields = 0;

        $html = '<div class="opening_times">';
        $html .= '<table>';
        $html .= '<tr>';

        foreach( $dcas as $key => $field ) {

            $strClass = $GLOBALS['BE_FFL'][$field['inputType']];
            if( !class_exists($strClass) ) {
                continue;
            }

            $strClass = $GLOBALS['BE_FFL'][$field['inputType']];
            if( !class_exists($strClass) ) {
                continue;
            }

            $cField = new $strClass($strClass::getAttributesFromDca($field, $this->arrConfiguration['strField'].'[]['.$key.']'));

            $label = $cField->parse();

            $results = [];
            if( preg_match("/<label(.*)<\\/label>/s", $label, $results) ){
                $html .= '<th><h3>'.$results[0].'</h3></th>';
            } else {
                $html .= '<th><h3>'.$field['label'][0].'</h3></th>';
            }

            unset($field['label']);
            $numFields++;
        }
        $html .= '<th>'.'</th>';
        $html .= '</tr>';

        $this->value = (array)$this->value;

        if( count($this->value) == 0 ){
            $this->value = [[]];
        }
        if( is_string($this->value) ){
            $this->value = deserialize($this->value);
        }

        for( $i=0; $i < count($this->value); $i++ ) {
            $html .= '<tr>';

            foreach( $dcas as $key => $field ) {

                $strClass = $GLOBALS['BE_FFL'][$field['inputType']];
                if( !class_exists($strClass) ) {
                    continue;
                }

                $cField = new $strClass($strClass::getAttributesFromDca(
                    $field,
                    $this->arrConfiguration['strField'].'['.$i.']['.$key.']',
                    (!empty($this->value[$i][$key])?$this->value[$i][$key]:null)
                ));

                if( !empty($this->arrErrors[$i][$key]) ){
                    $cField->arrErrors = $this->arrErrors[$i][$key];
                }

                $cField->label = null;

                $html .=  '<td>'.str_replace("<h3></h3>", "", $cField->parse()).'</td>' ;
            }
            $theme = Backend::getTheme();

            $html .= '<td class="operations">';
            $title = $GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_copy'];
            $html .=      '<a rel="copy" href="#" class="widgetImage" title="'. $title .'">'. Image::getHtml('copy.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $title = $GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_up'];
            $html .=      '<a rel="up" href="#" class="widgetImage sl_flip" title="'. $title .'">'. Image::getHtml('down.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $title = $GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_down'];
            $html .=      '<a rel="down" href="#" class="widgetImage" title="'. $title .'">'. Image::getHtml('down.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $title = $GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_delete'];
            $html .=      '<a rel="delete" href="#" class="widgetImage" title="'. $title .'">'. Image::getHtml('delete.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $html .= '</td>';

            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '
        <script>
        var clickHandler = function(e){
            e.preventDefault();

            var row = this.parentElement.parentElement;
            var table = row.parentElement;
            if( this.rel == "copy" ){
                var clone = row.cloneNode(true);
                table.insertBefore(clone, row);
                var as=clone.querySelectorAll("a");
                for (i = 0; i < as.length; i++) {
                    as[i].addEventListener("click", clickHandler);
                }
                if( window.Stylect ) {
                    Stylect.convertSelects();
                }
            } else if( this.rel == "up" ){
                if( row.previousSibling == null || row.previousSibling.previousSibling == null ) return;
                table.insertBefore(row, row.previousSibling)
            } else if( this.rel == "down" ){
                if( row.nextSibling == null ) return;
                table.insertBefore(row.nextSibling, row)
            } else if( this.rel == "delete" ){
                table.removeChild(row)
            }
            var inputs = table.querySelectorAll("input, select");
            for (i = 0; i < inputs.length; i++) {
                var iRow = Math.floor(i/'.$numFields.');
                inputs[i].id = inputs[i].id.replace(/\[\d+\]/, "["+iRow+"]");
                inputs[i].name = inputs[i].name.replace(/\[\d+\]/, "["+iRow+"]");
            }
        }

        var anchors=document.querySelectorAll(".'.$this->strField.' td.operations > a");
        for (i = 0; i < anchors.length; i++) {
            anchors[i].addEventListener("click", clickHandler );
        }
        </script>';

        $html .= '</div>';

        return $html;
    }


    /**
     * Generates all necessary checkboxes and input fields
     * based on their original dca`s
     *
     * @return array
     */
    protected function generateWidgetsDCA(): array {

        $widgetDCA = [
            'weekday' => [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_weekday']
            ,   'inputType'         => 'select'
            ,   'options_callback'  => [StoreLocator::class, 'getWeekdays']
            ,   'eval'              => ['mandatory'=>true, 'maxlength'=>255, 'style'=>'width:480px']
            ]
        ,   'from' => [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_from']
            ,   'inputType'         => 'text'
            ,   'eval'              => ['maxlength'=>5, 'style'=>'width:60px']
            ]
        ,   'to' => [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_to']
            ,   'inputType'         => 'text'
            ,   'eval'              => ['maxlength'=>5, 'style'=>'width:60px']
            ]
        ];

        return $widgetDCA;
    }


    /**
     * Return a particular error as HTML string
     *
     * @param integer $intIndex The message index
     *
     * @return string The HTML markup of the corresponding error message
     */
    public function getErrorAsHTML( $intIndex=0 ): string {

        $scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
        $requestStack = System::getContainer()->get('request_stack');

        $isBackend = false;

        if( $scopeMatcher->isBackendRequest($requestStack->getCurrentRequest()) ) {
            $isBackend = true;
        }

        return $this->hasErrors() ? sprintf('<p class="%s">%s</p>', (($isBackend == 'BE') ? 'tl_error tl_tip' : 'error'), "FEHLER") : '';
    }
}
