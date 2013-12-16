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

Html::header(PluginPduPdu::getTypeName(2),'',"plugins","pdu");

$PluginPduPdu=new PluginPduPdu();
if (!$PluginPduPdu->canView() || !Session::haveRight("config","w")) {
   Html::displayRightError();
}

$tr_class_num = 2;
echo '<div class="center">';
echo "<table class='tab_cadrehov'>";
echo "<tr>";
echo "<th>" . __('Name') . "</th>";
echo "<th>" . __('Type') . "</th>";
echo "<th>" . __('Model') . "</th>";
echo "<th>" . __('Outlets used') . "</th>";
echo "<th>" . __('Outlets total') . "</th>";
echo "</tr>";

$class_table = getTableForItemType('PluginPduModel');

foreach (PluginPduPdu::$types as $type) {
   $type_table = getTableForItemType($type);
   $model_table = getTableForItemType($type."Model");
   $type_field = getForeignKeyFieldForTable($type_table);
   $model_field = getForeignKeyFieldForTable($model_table);

   $query = "SELECT ".$type_table.".* 
      FROM `".$type_table."`
      JOIN `".$model_table."`
         ON `".$type_table."`.`".$model_field."`=`".$model_table."`.`id`
      JOIN `".$class_table."`
         ON `".$model_table."`.`id`=`".$class_table."`.`model_id`
            AND `".$class_table."`.`itemtype`='".$type."Model';";

   $result = $DB->query($query);
   $number = $DB->numrows($result);

   if ($number = 0)
      continue;

   while ($data = $DB->fetch_assoc($result)) {
      echo "<tr>";
      echo '<td valign="top">';
      echo '<a id="'.$type.'_'.$data['id'].'">'.$data['name'].'</a>';
      echo "</td>";
      echo "</tr>";
   }
}

echo "</table>";
echo '</div>';


Html::footer();

?>
