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

function plugin_item_purge_pdu($item) {
   global $DB;

   $sql = sprintf(
      'DELETE FROM `glpi_plugin_pdu_connections` ' .
      'WHERE `connected_itemtype` = \'%s\' ' .
      'AND `connected_id` = %d',
      $item->getType(),
      $item->getID()
   );
   $DB->query($sql);

   $PluginPduModel = new PluginPduModel;
   if ( $PluginPduModel->isPdu($item->getID()) ) {
      $sql = sprintf(
         'DELETE FROM `glpi_plugin_pdu_connections`
          WHERE `pdu_id` = %d',
         $item->getID()
      );
   }
   $DB->query($sql);

   return true; 
}


?>
