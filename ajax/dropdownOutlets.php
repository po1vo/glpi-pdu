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

list($pdu_id, $model_id) = explode("_", $_POST["pdu"]);

$PluginPduConnection = new PluginPduConnection;
$used_outlets = $PluginPduConnection->listUsedOutlets($pdu_id);

$PluginPduModel = new PluginPduModel;
$PluginPduModel->getFromDBByQuery("WHERE `" . $PluginPduModel->getTable() . "`.`model_id` = '" . $model_id . "' LIMIT 1");

Dropdown::showFromArray(
   'outlet_id',
   array_combine( range(1, $PluginPduModel->fields["outlets"]), range(1, $PluginPduModel->fields["outlets"]) ),
   array(
      'rand' => $_POST["rand"],
      'used' => $used_outlets,
   )
); 


?>
