<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   Ads
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   Commercial
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
 */


/**
 * Namespace
 */
namespace numero2\StoreLocator;


class OpeningTimes extends \Widget {


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
	 * Initialize the FileUpload object
	 *
	 * @param array $arrAttributes
	 */
	public function __construct($arrAttributes=null) {
		parent::__construct($arrAttributes);
	}


	/**
	 * Trim values
	 *
	 * @param mixed $varInput
	 *
	 * @return mixed
	 */
	protected function validator($varInput) {

        $dcas = array();
		$dcas = self::generateWidgetsDCA();


		echo "<pre>".print_r($varInput,1)."</pre>";
		echo "<pre>".print_r($dcas,1)."</pre>";

        foreach( $varInput as $row => $rValue ) {
	        foreach( $dcas as $key => $field ) {

				$field['value'] = $rValue[$key];

				$strClass = $GLOBALS['TL_FFL'][$field['inputType']];
				if( !class_exists($strClass) ) {
					continue;
				}
				$cField = new $strClass($strClass::getAttributesFromDca($field, $this->arrConfiguration['strField'].'[0]['.$key.']'));
				if( !empty($rValue[$key]) ){
					$cField->value = $rValue[$key];
				} else {
					$cField->value = "";
				}
    			// TODO check why no validation
    			echo "<pre>".print_r(var_dump($cField->validate()),1)."</pre>";
    			echo "<pre>".print_r(var_dump($cField->hasErrors()),1)."</pre>";

	        }
		}

        return ($this->blnHasError) ? false : empty($varInput)?'':serialize($varInput);
	}


	/**
	 * Generate the widget and returns it as a string
	 *
	 * @return string
	 */
	public function generate() {

        if( empty($GLOBALS['TL_CSS']) || array_search('system/modules/storelocator/assets/backend.css', $GLOBALS['TL_CSS']) === FALSE ) {
            $GLOBALS['TL_CSS'][] = 'system/modules/storelocator/assets/backend.css';
        }

        $dcas = array();
		$dcas = self::generateWidgetsDCA();
		$numFields = 0;

        $html = '<div class="opening_times">';
		$html .= '<table>';
		$html .= '<tr>';

		foreach( $dcas as $key => $field ) {

			$strClass = $GLOBALS['TL_FFL'][$field['inputType']];
			if( !class_exists($strClass) ) {
				continue;
			}

			$strClass = $GLOBALS['TL_FFL'][$field['inputType']];
			if( !class_exists($strClass) ) {
				continue;
			}

			$cField = new $strClass($strClass::getAttributesFromDca($field, $this->arrConfiguration['strField'].'['.$i.']['.$key.']'));
			$cField->tableless = true;
			$label = $cField->parse();

			$results = array();
			if( preg_match("/<label(.*)<\\/label>/s", $label, $results) ){
				$html .= '<th>'.$results[0].'</th>';
			} else {
				$html .= '<th>'.$field['label'][0].'</th>';
			}

			unset($field['label']);
			$numFields++;
		}
		$html .= '<th>'.'</th>';
		$html .= '</tr>';


		if( count($this->value) == 0 ){
			$this->value = array(array());
		}

		for ($i = 0; $i < count($this->value); $i++) {
			$html .= '<tr>';

	        foreach( $dcas as $key => $field ) {

				$strClass = $GLOBALS['TL_FFL'][$field['inputType']];
				if( !class_exists($strClass) ) {
					continue;
				}

				$cField = new $strClass($strClass::getAttributesFromDca($field, $this->arrConfiguration['strField'].'['.$i.']['.$key.']'));
				if( !empty($this->value[$i][$key]) ){
					$cField->value = $this->value[$i][$key];
				}

				$cField->tableless = true;
				$cField->label = null;

				$html .=  '<td>'.$cField->parse().'</td>' ;
	        }
			$html .= '<td class="operations">';
			$html .=  	'<a rel="copy" href="#" class="widgetImage" title=""><img src="system/themes/default/images/copy.gif" width="14" height="16" alt="Die Reihe duplizieren" class="tl_listwizard_img"></a>';
			$html .=  	'<a rel="up" href="#" class="widgetImage" title=""><img src="system/themes/default/images/up.gif" width="13" height="16" alt="Die Reihe eine Position nach oben verschieben" class="tl_listwizard_img"></a>';
			$html .=  	'<a rel="down" href="#" class="widgetImage" title=""><img src="system/themes/default/images/down.gif" width="13" height="16" alt="Die Reihe eine Position nach unten verschieben" class="tl_listwizard_img"></a>';
			$html .=  	'<a rel="delete" href="#" class="widgetImage" title=""><img src="system/themes/default/images/delete.gif" width="14" height="16" alt="Die Reihe löschen" class="tl_listwizard_img"></a>';
			$html .= '</td>';

			$html .= '</tr>';
		}

		$html .= '</table>';
		$html .= '
		<script>
		var clickHandler = function(e){
			e.preventDefault();

			var row = this.parentElement.parentElement
			if( this.rel == "copy" ){
				var clone = row.cloneNode(true);
				row.parentElement.insertBefore(clone, row);
				var as=clone.querySelectorAll("a");
				for (i = 0; i < as.length; i++) {
					as[i].addEventListener("click", clickHandler);
				}
				if( window.Stylect ) {
	                Stylect.convertSelects();
		        }
			} else if( this.rel == "up" ){
				if( row.previousSibling == null ) return;
				row.parentElement.insertBefore(row, row.previousSibling)
			} else if( this.rel == "down" ){
				if( row.nextSibling == null ) return;
				row.parentElement.insertBefore(row.nextSibling, row)
			} else if( this.rel == "delete" ){
				row.parentElement.removeChild(row)
			}
			var inputs = row.parentElement.querySelectorAll("input, select");
			for (i = 0; i < inputs.length; i++) {
				var iRow = Math.floor(i/'.$numFields.');
				inputs[i].id = inputs[i].id.replace(/\[\d\]/, "["+iRow+"]");
				inputs[i].name = inputs[i].name.replace(/\[\d\]/, "["+iRow+"]");
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
	protected function generateWidgetsDCA() {

		$widgetDCA = array(
			'weekday' => array(
				'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_weekday']
			,	'inputType'               	=> 'select'
			,	'options_callback'          => array( '\numero2\StoreLocator\StoreLocator', 'getWeekdays' )
			,	'eval'                    	=> array( 'mandatory'=>true, 'maxlength'=>255, 'style'=>'width:480px' )
			)
		,	'from' => array(
				'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_from']
			,	'inputType'               	=> 'text'
			,	'eval'                    	=> array( 'mandatory'=>true, 'maxlength'=>5, 'style'=>'width:60px' )
			)
		,	'to' => array(
				'label'                   	=> &$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_to']
			,	'inputType'               	=> 'text'
			,	'eval'                    	=> array( 'mandatory'=>true, 'maxlength'=>5, 'style'=>'width:60px' )
			)
		);

		return $widgetDCA;
	}
}
