<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
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
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    storelocator
 * @license    LGPL
 * @filesource
 */
 
 
class ModuleStoreLocatorImporter extends Backend {


	/**
	 * Generates a form to start import from csv file
	 */
	public function showImport() {
	
		if( $this->Input->post('FORM_SUBMIT') == 'tl_storelocator_stores_import' ) {
		
			$source = $this->Input->post('file', true);

			// check the file names
			if( !$source ) {
				$this->addErrorMessage($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}
			
			// skip folders
			if( is_dir(TL_ROOT . '/' . $source) ) {
				$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($source)));
				continue;
			}
			
			$objFile = new File($source);

			// skip anything but .csv files
			if( $objFile->extension != 'csv' ) {
				$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
				continue;
			}
			
			ini_set("max_execution_time",0);

			// read entries		
			if( $objFile->handle !== FALSE ) {
			
				$pid = $this->Input->get('id');
				
				$oStores = null;
				$oStores = new tl_storelocator_stores();
				$count = 0;
				
				while( ($data = fgetcsv($objFile->handle, 1000)) !== FALSE ) {

					if( empty($data[0]) )
						continue;

					$count++;
					
					// get coordinates
					$sl = new StoreLocator();
					$coords = $sl->getCoordinates(
						$data[5]
					,	$data[6]
					,	$data[7]
					,	$data[8]
					);

					// add "http" in front of url
					$data[2] = ( $data[2] && strpos($data[2],'http') === FALSE ) ? 'http://'.$data[2] : $data[2];

					try {
						$this->Database->prepare("INSERT INTO `tl_storelocator_stores` (`pid`,`tstamp`,`name`,`email`,`url`,`phone`,`fax`,`street`,`postal`,`city`,`country`,`longitude`,`latitude`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute(
							$pid
						,	time()
						,	$data[0]
						,	$data[1]
						,	$data[2]
						,	$data[3]
						,	$data[4]
						,	$data[5]
						,	$data[6]
						,	$data[7]
						,	strtolower($data[8])
						,	$coords ? $coords['longitude'] : ''
						,	$coords ? $coords['latitude'] : ''
						);
					} catch( Exception $e ) {
						continue;
					}
					
					if( $count > 5 ){
						sleep(2);
						$count = 0;
					}
				}

				$objFile->close();

				// Redirect
				setcookie('BE_PAGE_OFFSET', 0, 0, '/');
				$this->redirect(str_replace('&key=importStores', '', $this->Environment->request));
				return;
			}
		}

		$objTree = new FileTree(
			$this->prepareForWidget(
				$GLOBALS['TL_DCA']['tl_storelocator_stores']['fields']['file']
			, 	'file'
			, 	null
			,	'file'
			,	'tl_storelocator_stores'
			)
		);

		// Return the form
		return '
			<div id="tl_buttons">
				<a href="'.ampersand(str_replace('&key=importStores', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
			</div>

			<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_storelocator']['import']['head'].'</h2>
			'.$this->getMessages().'

			<form action="'.ampersand($this->Environment->request, true).'" id="tl_storelocator_stores_import" class="tl_form" method="post">
				<div class="tl_formbody_edit">
					<input type="hidden" name="FORM_SUBMIT" value="tl_storelocator_stores_import">
					<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

					<div class="tl_tbox">
						<h3><label for="source">'.$GLOBALS['TL_LANG']['tl_storelocator']['import']['file'][0].'</label> <a href="contao/files.php" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['fileManager']) . '" data-lightbox="files 765 80%">' . $this->generateImage('filemanager.gif', $GLOBALS['TL_LANG']['MSC']['fileManager'], 'style="vertical-align:text-bottom"') . '</a></h3>'.$objTree->generate().(strlen($GLOBALS['TL_LANG']['tl_storelocator']['import']['file'][1]) ? '
						<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_storelocator']['import']['file'][1].'</p>' : '').'
					</div>
				</div>

				<div class="tl_formbody_submit">
					<div class="tl_submit_container">
						<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_storelocator']['import']['start']).'">
					</div>
				</div>
			</form>
		';
	}
}

?>