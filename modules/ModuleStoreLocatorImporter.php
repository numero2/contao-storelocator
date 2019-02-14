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


class ModuleStoreLocatorImporter extends \Backend {


    /**
     * Generates a form to start import from csv file
     */
    public function showImport() {

        ini_set('max_execution_time', 0);

        $this->import('BackendUser', 'User');
        $this->import('StoreLocator', 'SL');
        $class = $this->User->uploader;

        if( !class_exists($class) || $class == 'DropZone' ) {
            $class = 'FileUpload';
        }

        $objUploader = new $class();

        if( \Input::post('FORM_SUBMIT') == 'tl_storelocator_import' ) {

            $arrUploaded = $objUploader->uploadTo('system/tmp');

            if( empty($arrUploaded) ) {
                \Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
                $this->reload();
            }

            $arrFiles = array();

            foreach( $arrUploaded as $strFile ){

                // Skip folders
                if( is_dir(TL_ROOT . '/' . $strFile) ) {
                    \Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['importFolder'], basename($strFile)));
                    continue;
                }

                $objFile = new \File($strFile, true);

                // Skip anything but .cto files
                if( $objFile->extension != 'csv' ) {
                    \Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
                    continue;
                }

                $arrFiles[] = $strFile;
            }

            if( !empty($arrFiles) ) {

                $autoIncrement = $this->Database->prepare("
                        SELECT `AUTO_INCREMENT`
                        FROM  INFORMATION_SCHEMA.TABLES
                        WHERE TABLE_SCHEMA = ?
                            AND TABLE_NAME   = ?;
                    ")->execute( \Config::get('dbDatabase'), "tl_storelocator_stores" );

                $autoIncrement = $autoIncrement->AUTO_INCREMENT;

                foreach( $arrFiles as $file ) {

                    $objFile = new \File($strFile, true);

                    while( ($data = fgetcsv($objFile->handle)) !== FALSE ) {

                        if( empty($data[0]) || empty($data[5]) || empty($data[6]) || empty($data[7]) || empty($data[8]) )
                            continue;

                        // generate alias
                        $alias = \StringUtil::generateAlias($data[0]);

                        $oAlias = NULL;
                        $oAlias = StoresModel::findByAlias( $alias );

                        // Check whether the alias exists
                        if( $oAlias && count($oAlias) > 0 ) {

                            $alias .= '-' . $autoIncrement;
                        }

                        // get coordinates
                        $aCoords = $this->SL->getCoordinates(
                            $data[5]
                        ,   $data[6]
                        ,   $data[7]
                        ,   $data[8]
                        );

                        // add "http" in front of website url
                        $data[2] = ( $data[2] && strpos($data[2],'http') === FALSE ) ? 'http://'.$data[2] : $data[2];

                        $pid = $this->Input->get('id');

                        try {
                            $this->Database->prepare("INSERT INTO `tl_storelocator_stores` (`pid`,`tstamp`,`name`,`alias`,`email`,`url`,`phone`,`fax`,`street`,`postal`,`city`,`country`,`longitude`,`latitude`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute(
                                $pid
                            ,   time()
                            ,   $data[0]
                            ,   $alias
                            ,   $data[1]
                            ,   $data[2]
                            ,   $data[3]
                            ,   $data[4]
                            ,   $data[5]
                            ,   $data[6]
                            ,   $data[7]
                            ,   strtolower($data[8])
                            ,   $aCoords ? $aCoords['longitude'] : ''
                            ,   $aCoords ? $aCoords['latitude'] : ''
                            );
                        } catch( Exception $e ) {
                            continue;
                        }
                        $autoIncrement++;
                    }

                    // Redirect
                    setcookie('BE_PAGE_OFFSET', 0, 0, '/');
                    $this->redirect(str_replace('&key=importStores', '', \Environment::get('request')));
                    return;
                }
            }
        }

        return '
            <div id="tl_buttons">
                <a href="'.ampersand(str_replace('&key=importStores', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
            </div>
            '.\Message::generate().'
            <form action="'.ampersand(\Environment::get('request'), true).'" id="tl_storelocator_import" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data" onsubmit="AjaxRequest.displayBox(\''.$GLOBALS['TL_LANG']['tl_storelocator']['import']['ajax_import_running'].'\');">
                <div class="tl_formbody_edit sl_import">
                    <input type="hidden" name="FORM_SUBMIT" value="tl_storelocator_import">
                    <input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">
                    <input type="hidden" name="MAX_FILE_SIZE" value="'.\Config::get('maxFileSize').'">
                    <div class="tl_tbox widget">
                        <h3 style="margin-bottom: 10px;">'.$GLOBALS['TL_LANG']['tl_storelocator']['import']['head'].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['tl_storelocator']['import']['file'][1]) ? '
                        <p class="tl_help tl_tip" style="height: 30px; margin-top: 10px;">'.$GLOBALS['TL_LANG']['tl_storelocator']['import']['file'][1].'</p>' : '').'
                        <p style="margin-top: 10px;">'.$GLOBALS['TL_LANG']['tl_storelocator']['import']['limit_info'].'</p>
                    </div>
                </div>
                <div class="tl_formbody_submit">
                    <div class="tl_submit_container">
                        <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_storelocator']['import']['start']).'">
                    </div>
                </div>
            </form>';
    }
}
