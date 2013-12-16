<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 Racks plugin for GLPI
 Copyright (C) 2003-2011 by the Racks Development Team.

 https://forge.indepnet.net/projects/pdu
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

if (!isset($_POST["id"])) {
	exit();
}

$PluginPduModel = new PluginPduModel();

$PluginPduModel->checkGlobal("r");

if (empty($_POST["id"])) {
	switch($_POST['plugin_pdu_tab']) {
		default :
			break;
	}
} else {
   $target = $CFG_GLPI['root_doc']."/plugins/pdu/front/model.form.php";
	switch($_POST['plugin_pdu_tab']) {
		case "all" :
			$_SESSION['glpi_plugin_pdu_tab']="all";
			$PluginPduModel->showList($target,$_POST["id"],'ComputerModel');
			$PluginPduModel->showList($target,$_POST["id"],'NetworkEquipmentModel');
			$PluginPduModel->showList($target,$_POST["id"],'PeripheralModel');
			break;
		case 'ComputerModel' :
			$_SESSION['glpi_plugin_pdu_tab']='ComputerModel';
			$PluginPduModel->showList($target,$_POST["id"],$_SESSION['glpi_plugin_pdu_tab']);
			break;
		case 'NetworkEquipmentModel' :
			$_SESSION['glpi_plugin_pdu_tab']='NetworkEquipmentModel';
			$PluginPduModel->showList($target,$_POST["id"],$_SESSION['glpi_plugin_pdu_tab']);
			break;
		case 'PeripheralModel' :
			$_SESSION['glpi_plugin_pdu_tab']='PeripheralModel';
			$PluginPduModel->showList($target,$_POST["id"],$_SESSION['glpi_plugin_pdu_tab']);
			break;
		default :
			break;
	}
}

Html::ajaxFooter();

?>
