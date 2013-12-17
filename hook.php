<?php

function plugin_pdu_install() {
   global $DB;

   if (
      (!TableExists("glpi_plugin_pdu_models")) &&
      (!TableExists("glpi_plugin_pdu_connections"))
   ) {
      $DB->runFile(dirname(__FILE__) . "/db/mysql.0.0.1.sql") or
      Html::displayErrorAndDie(
         "Error installing PDUs plugin ". $DB->error()
      );
   }

   return true;

}

function plugin_pdu_uninstall() {
   global $DB;

    foreach (array("models", "connections") as $table) {
        if (TableExists("glpi_plugin_pdu_" . $table)) {
            $DB->query("DROP TABLE glpi_plugin_pdu_" . $table) or
                print "Cannot remove database table glpi_plugin_pdu_" .
                    $table;
        }
    }

   return true;
}

/*
function plugin_pdu_addLeftJoin($type, $ref_table, $new_table, $linkfield, & $already_link_tables) {
   switch ($new_table) {
      case "glpi_networkequipments" :
         return " LEFT JOIN `$new_table` ON (`$ref_table`.`model_id` = `$new_table`.`networkequipmentmodels_id` ) ";
   }
   return "";
}

function plugin_pdu_addDefaultJoin($type, $ref_table, &$already_link_tables) {
   if ($type == "PluginPduModel" && $ref_table == "glpi_plugin_pdu_models")
      return " LEFT JOIN `glpi_networkequipments` ON (`glpi_plugin_pdu_models`.`model_id` = `glpi_networkequipments`.`networkequipmentmodels_id` ) ";

   return "";
}

*/

?>
