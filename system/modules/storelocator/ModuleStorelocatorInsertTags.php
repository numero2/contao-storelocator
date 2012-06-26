<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


class ModuleStorelocatorInsertTags extends Controller {

	
	protected function replaceInsertTags($strBuffer, $blnCache=false) {

		$this->import('Database');
	
        $aParams = explode('::', $strBuffer);
 
        switch( $aParams[0] ) {
        
            case 'store' :

				// find store
				$objStore = $this->Database->prepare("SELECT * FROM `tl_storelocator_stores` WHERE `id` = ? ")->execute( $aParams[1] );
				
				if( !$objStore )
					return false;
				
				$str = '<div class="mod_storelocator_details>'
						. '<div class="name">'.$objStore->name.'</div>'
						. '<div class="address">'
						. $objStore->street.'<br />'
						. $objStore->postal.' '.$objStore->city.'<br />'
						. $GLOBALS['TL_LANG']['tl_storelocator']['countries'][ $objStore->country ]
						. '</div>';
						
				if( $objStore->phone )
					$str.= '<div class="phone">Tel.: '.$objStore->phone.'</div>';
					
				if( $objStore->fax )
					$str.= '<div class="fax">Fax: '.$objStore->fax.'</div>';
					
				if( $objStore->email )
					$str.= '<div class="email">E-Mail: '.$objStore->email.'</div>';
					
				if( $objStore->url )
					$str.= '<div class="url">WWW: '.$objStore->url.'</div>';
					
				$objStore->opening_times = unserialize($objStore->opening_times);
					
				if( !empty($objStore->opening_times[0]['from']) ) {
				
					$str.= '<ul class="times">';
					
					foreach( $objStore->opening_times as $i => $v ) {
					
						$str.= '<li>';
							$str.= $GLOBALS['TL_LANG']['tl_storelocator']['weekdays'][ $v['weekday'] ].' '.$v['from'].' - '.$v['to'];
						$str.= '</li>';
					}
					
					$str.= '</ul>';
				}
						
				$str.= '</div>';
				
				return $str;

            break;
            
            // not our insert tag?
            default :
                return false;
            break;
        }

        return false;
    
    }
}

?>