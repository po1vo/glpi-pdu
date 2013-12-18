<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 Racks plugin for GLPI
 Copyright (C) 2003-2011 by the Racks Development Team.

 https://forge.indepnet.net/projects/racks
 -------------------------------------------------------------------------

 LICENSE
		
 This file is part of Racks.

 Racks is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Racks is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Racks. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

include ('../../../inc/includes.php');

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Plugin::load('pdu',true);

$rand = $_POST["rand"];

$on_change = Ajax::updateItemJsCode(
   "outlet_id".$rand,                        // id of the item to update
   "/plugins/pdu/ajax/dropdownOutlets.php",  // Url to get datas to update the item
   array( 'pdu' => '__VALUE__',              // parameters to send to ajax URL
          'rand'   => $rand ),
   'dropdown_pdu_id'.$rand,                  // id of another item used to get value in case of __VALUE__ used
   false                                     // display or get string (default true)
);
$on_change = str_replace("'", "&#39;", $on_change);

$avail_pdus = PluginPduPdu::getAvailPdus($_POST["searchText"]);

$list = array('0' => Dropdown::EMPTY_VALUE);
foreach($avail_pdus as $key => $assoc) {
   $list[$key."_".$assoc['model_id']] = $assoc['name'];
}

Dropdown::showFromArray(
   'pdu_id', 
   $list, 
   array( 'rand'      => $rand,
          'on_change' => $on_change,
   ) 
);

?>
