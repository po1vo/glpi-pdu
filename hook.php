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

?>
