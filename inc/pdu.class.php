<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginPduPdu extends CommonDBTM {
   static $itemtype = 'NetworkEquipment';

   static function getTypeName($nb = 0) {
      return _n('PDUs', 'PDUs', $nb, 'pdu');
   }

   static function canCreate() {
      return true;
   }

   static function canView() {
      return true;
   }

   static function getAvailPdus($name) {
      global $DB;

      $ret = array();

      $query = "
         SELECT
            `glpi_networkequipments`.`id`,
            `glpi_networkequipments`.`name`,
            `glpi_plugin_pdu_models`.`model_id`,
            `glpi_plugin_pdu_models`.`outlets`,
            `conn`.`used`
         FROM `glpi_networkequipments`
         JOIN `glpi_networkequipmentmodels`
            ON `glpi_networkequipments`.`networkequipmentmodels_id`=`glpi_networkequipmentmodels`.`id`
         JOIN `glpi_plugin_pdu_models`
            ON `glpi_networkequipmentmodels`.`id`=`glpi_plugin_pdu_models`.`model_id`
         LEFT JOIN
            (SELECT `pdu_id`, COUNT(*) AS `used` FROM `glpi_plugin_pdu_connections`) AS `conn`
            ON `glpi_networkequipments`.`id`=`conn`.`pdu_id`
         WHERE IFNULL(`conn`.`used`,0) < `glpi_plugin_pdu_models`.`outlets` ";

      if ($name)
         $query .= " AND `glpi_networkequipments`.`name` LIKE '%".$name."%'";
     
      $query .= " ORDER BY `glpi_networkequipments`.`name`";

      $result = $DB->query($query);
      while ($data = $DB->fetch_assoc($result)) {
         $ret[$data['id']] = $data;
      }

      return $ret;
   } 
}

?>
