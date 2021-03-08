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


namespace numero2\StoreLocator\DCAHelper;

use Contao\Backend;
use Contao\DataContainer;
use Contao\StringUtil;
use \numero2\StoreLocator\CategoriesModel;


class Categories extends Backend {


    /**
     * Auto-generate an category alias if it has not been set yet
     *
     * @param mixed         $varValue
     * @param Contao\DataContainer $dc
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateAlias( $varValue, DataContainer $dc ) {

        $autoAlias = false;

        // Generate an alias if there is none
        if( $varValue == '' ) {
            $autoAlias = true;
            $varValue = StringUtil::generateAlias($dc->activeRecord->title);
        }

        $oAlias = NULL;
        $oAlias = CategoriesModel::findBy(['id=? OR alias=?'], [$dc->activeRecord->id, $varValue] );

        // Check whether the alias exists
        if( $oAlias && $oAlias->count() > 1 ) {

            if( !$autoAlias ) {
                throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
            }

            $varValue .= '-' . $dc->id;
        }

        return $varValue;
    }
}
