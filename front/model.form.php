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

if(!isset($_GET["id"])) $_GET["id"] = "";

$PluginPduModel = new PluginPduModel();

if (isset ($_POST["add"])) {

   if ($PluginPduModel->canCreate())
      $PluginPduModel->add($_POST);
   Html::back();
   
} else if (isset ($_POST["update"])) {

   if ($PluginPduModel->canCreate())
      $PluginPduModel->UpdateModel($_POST);
   Html::back();
   
} else if (isset ($_POST["delete"])) {

   if ($PluginPduModel->canCreate())
      $PluginPduModel->Delete($_POST);
   Html::back();
   
} else if (isset ($_POST["deleteSpec"])) {
   foreach ($_POST["item"] as $key => $val) {
      $input = array('id' => $key);
      if ($val==1) {
         $PluginPduModel->delete($input);
      }
   }
   Html::back();
} else {
   Html::header(PluginRacksRack::getTypeName(2),'',"plugins","pdu");

   $PluginPduModel->showForm($_GET["id"], array('target' => $CFG_GLPI['root_doc']."/plugins/pdu/front/model.form.php"));
   
   Html::footer();
}

?>
