<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2026, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\Config;
use Contao\Date;
use Contao\Image;
use Contao\StringUtil;
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
                        $this->arrConfiguration['strField'].'['.$row.']['.$key.']',
                        ( !empty($rValue[$key])?$rValue[$key]:null )
                    ));

                    $cField->validate();

                    if( $cField->hasErrors() ) {

                        $this->class = 'error';
                        $this->arrErrors[$row][$key] = $cField->arrErrors;

                    } else {

                        $value = $cField->value;

                        if( !empty($field['eval']['rgxp']) && $field['eval']['rgxp'] == 'date' ) {
                            $value = (new Date($value, Config::get('dateFormat')))?->tstamp ?? '';
                        }

                        $varInput[$row][$key] = $value;
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

        $html = '<div class="opening_times">';
        $html .= '<table class="'.$this->strField.'">';
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
            if( preg_match("/<label(.*)<\\/label>/s", $label, $results) ) {
                $html .= '<th><h3>'.$results[0].'</h3></th>';
            } else {
                $html .= '<th><h3>'.($field['label'][0] ?? 'MISSING').'</h3></th>';

            }

            unset($field['label']);
        }
        $html .= '<th>'.'</th>';
        $html .= '</tr>';

        $this->value = (array)$this->value;

        if( count($this->value) == 0 ) {
            $this->value = [[]];
        }
        if( is_string($this->value) ) {
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

                if( !empty($this->arrErrors[$i][$key]) ) {
                    $cField->arrErrors = $this->arrErrors[$i][$key];
                }

                $cField->label = null;

                $wizard = '';
                if( $field['eval']['datepicker'] ?? null ) {

                    $rgxp = $arrData['eval']['rgxp'] ?? 'date';
                    $format = Date::formatToJs(Config::get($rgxp . 'Format'));

                    switch( $rgxp ) {
                        case 'datim':
                            $time = ",\n        timePicker: true";
                            break;

                        case 'time':
                            $time = ",\n        pickOnly: \"time\"";
                            break;

                        default:
                            $time = '';
                            break;
                    }

                    $wizard .= ' ' . Image::getHtml('assets/datepicker/images/icon.svg', '', 'title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['datepicker']) . '" style="cursor:pointer"') . '
                    <script>
                        (()=>{

                            let input = document.currentScript.parentNode.querySelector("input");
                            let toggle = input.parentNode.querySelector("img");
                            let table = input.closest("table");

                            if( table.pickerDefaultOptions === undefined ) {

                                table.pickerDefaultOptions = {
                                    draggable: false,
                                    format: "' . $format . '",
                                    positionOffset: {x:-211,y:-209}' . $time . ',
                                    pickerClass: "datepicker_bootstrap",
                                    startDay: ' . $GLOBALS['TL_LANG']['MSC']['weekOffset'] . ',
                                    titleFormat: "' . $GLOBALS['TL_LANG']['MSC']['titleFormat'] . '"
                                }
                            }

                            let pickerOpts = Object.assign({ toggle: toggle }, table.pickerDefaultOptions);
                            new Picker.Date(input, pickerOpts);

                            input.pickerInitialized = true;

                        })();
                    </script>';
                }

                $html .=  '<td'.(strlen($wizard)?' class="wizard"':'').'>'.str_replace("<h3></h3>", "", $cField->parse()).$wizard.'</td>' ;
            }

            $html .= '<td class="operations">';
            $title = StringUtil::specialchars($GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_copy']);
            $html .=      '<a rel="copy" href="#" class="widgetImage" title="'. $title .'">'. Image::getHtml('copy.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $title = StringUtil::specialchars($GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_up']);
            $html .=      '<a rel="up" href="#" class="widgetImage sl_flip" title="'. $title .'">'. Image::getHtml('down.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $title = StringUtil::specialchars($GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_down']);
            $html .=      '<a rel="down" href="#" class="widgetImage" title="'. $title .'">'. Image::getHtml('down.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $title = StringUtil::specialchars($GLOBALS['TL_LANG']['tl_storelocator_stores']['times_operations_delete']);
            $html .=      '<a rel="delete" href="#" class="widgetImage" title="'. $title .'">'. Image::getHtml('delete.svg', $title, 'class="tl_listwizard_img"') .'</a>';
            $html .= '</td>';

            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '
        <script>
        (()=>{

            const clickHandler = function(e) {

                e.preventDefault();

                let row = this.parentElement.parentElement;
                let table = row.parentElement;

                if( this.rel == "copy" ) {

                    let clone = row.cloneNode(true);
                    table.insertBefore(clone, row);
                    let as = clone.querySelectorAll("a");

                    for( let i=0; i<as.length; i++ ) {
                        as[i].addEventListener("click", clickHandler);
                    }

                    if( window.Stylect ) {
                        Stylect.convertSelects();
                    }

                } else if( this.rel == "up" ) {

                    if( row.previousSibling == null || row.previousSibling.previousSibling == null ) {
                        return;
                    }

                    table.insertBefore(row, row.previousSibling);

                } else if( this.rel == "down" ) {

                    if( row.nextSibling == null ) {
                        return;
                    }

                    table.insertBefore(row.nextSibling, row);

                } else if( this.rel == "delete" ) {
                    table.removeChild(row)
                }

                const inputs = table.querySelectorAll("input, select");

                for( let i=0; i<inputs.length; i++ ) {

                    const iRow = [... inputs[i].closest("tbody").children].indexOf( inputs[i].closest("tr") ) - 1;

                    inputs[i].id = inputs[i].id.replace(/\[\d+\]/, "["+iRow+"]");
                    inputs[i].name = inputs[i].name.replace(/\[\d+\]/, "["+iRow+"]");

                    let pickerToggleImg = inputs[i].parentNode.querySelector("img");

                    if( pickerToggleImg && inputs[i].pickerInitialized === undefined && this.rel == "copy" ) {

                        let table = inputs[i].closest("table");

                        let pickerOpts = Object.assign({ toggle: pickerToggleImg }, table.pickerDefaultOptions);
                        new Picker.Date(inputs[i], pickerOpts);

                        inputs[i].pickerInitialized = true;
                    }
                }
            }

            const anchors=document.querySelectorAll(".'.$this->strField.' td.operations > a");

            for( let i=0; i<anchors.length; i++ ) {
                anchors[i].addEventListener("click", clickHandler);
            }

        })();
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
            ,   'eval'              => ['mandatory'=>true, 'maxlength'=>255]
            ]
        ,   'from' => [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_from']
            ,   'inputType'         => 'text'
            ,   'eval'              => ['maxlength'=>5]
            ]
        ,   'to' => [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_to']
            ,   'inputType'         => 'text'
            ,   'eval'              => ['maxlength'=>5]
            ]
        ];

        if( ($this->arrConfiguration['addClosed']??null) === true ) {

            $widgetDCA['closed'] = [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_closed']
            ,   'inputType'         => 'checkbox'
            ];
        }

        if( ($this->arrConfiguration['addByAppointment']??null) === true ) {

            $widgetDCA['by_appointment'] = [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_by_appointment']
            ,   'inputType'         => 'checkbox'
            ];
        }

        if( ($this->arrConfiguration['specificDates']??null) === true ) {

            $widgetDCA['weekday'] = [
                'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_date']
            ,   'inputType'         => 'text'
            ,   'eval'              => ['mandatory'=>false, 'rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'wizard']
            ];
        }

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
