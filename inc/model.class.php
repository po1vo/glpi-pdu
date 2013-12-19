<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginPduModel extends CommonDBTM {

   const ITEM_TYPE   = 'NetworkEquipment';
   const ITEM_MODEL  = 'NetworkEquipmentModel';


   static function getTypeName($nb = 0) {
      return __('PDU models specifications', 'pdu');
   }


   static function canCreate() {
      return true;
   }


   static function canView() {
      return true;
   }


   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      return __('PDU details', 'pdu');
   }


   static function displayTabContentForItem (CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      $self=new self();
      $self->showForm("", array( 'items_id' => $item->getField('id') ,
                                 'target'   => $CFG_GLPI['root_doc']."/plugins/pdu/front/model.form.php"));
   }


   function showForm ($ID, $options=array()) {

      $items_id = -1;
      if (isset($options['items_id'])) {
         $items_id = $options['items_id'];
      }

      // Existing item?
      if ($this->getFromDBByQuery("WHERE `" . $this->getTable() . "`.`model_id` = '" . Toolbox::cleanInteger($items_id) . "' LIMIT 1"))
         $ID = $this->fields["id"];

      if ($ID > 0) {
         $this->check($ID,'r');
      } else {
         // Create item
         $this->check(-1,'w',$input);
      }

      $this->showFormHeader($options);

      if ($ID > 0) {
         echo "<input type='hidden' name='model_id' value='".$this->fields["model_id"]."'>";
      } else {
         echo "<input type='hidden' name='model_id' value='$items_id'>";
      }

      echo "<tr class='tab_bg_1'>";
      echo "<td>" . __('Number of outlets') . "</td>";
      echo "<td>";
      Dropdown::showInteger("outlets", ($ID > 0) ? $this->fields["outlets"] : 1, 1, 50, 1);
      echo "</td>";
      echo "</tr>";
      $this->showFormButtons($options);
   }


   function UpdateModel($input) {
      global $DB;

      $query = "SELECT `outlet_id` AS `outlets_max`
         FROM `glpi_plugin_pdu_connections` 
         LEFT JOIN `glpi_networkequipments` 
            ON `glpi_networkequipments`.`id`=`glpi_plugin_pdu_connections`.`pdu_id` 
         WHERE `glpi_networkequipments`.`networkequipmentmodels_id`='".$input['model_id']."'
         ORDER BY `outlet_id` 
         DESC LIMIT 1;";

      $result = $DB->query($query);
      $data = $DB->fetch_assoc($result);

      // if new number of outlets is less than the maximum used
      if ($input['outlets'] < $data['outlets_max']) {
         Session::addMessageAfterRedirect(__('Can\'t set less outlets than the number in use'), false, ERROR);
      } else { 
         $this->update($input);
      }
   }


   function DeleteModel($input) {
      global $DB;

      $query = "SELECT 1
         FROM `glpi_plugin_pdu_connections` 
         LEFT JOIN `glpi_networkequipments` 
            ON `glpi_networkequipments`.`id`=`glpi_plugin_pdu_connections`.`pdu_id` 
         LEFT JOIN `glpi_plugin_pdu_models`
            ON `glpi_networkequipments`.`networkequipmentmodels_id`=`glpi_plugin_pdu_models`.`model_id`
         WHERE `glpi_plugin_pdu_models`.`id`='".$input['id']."'";

      $result = $DB->query($query);

      if ($DB->numrows($result) > 0) {
         Session::addMessageAfterRedirect(__('Can\'t delete: there are PDUs in use'), false, ERROR);
      } else {
         $this->delete($input);
      }
   }   


   function showList($withtemplate='') {

      $rand=mt_rand();
      $this->showModels($rand);

      $massiveactionparams['ontop'] = false;
      Html::showMassiveActions(__CLASS__, $massiveactionparams);
      Html::closeForm();
   }


   function showModels() {
      global $DB;

      $link  = Toolbox::getItemTypeFormURL(self::ITEM_MODEL);
      $table = getTableForItemType(self::ITEM_MODEL);

      echo "<table class='tab_cadre_fixe' cellpadding='5'>";
      echo "<tr class='tab_bg_1'>";
      echo "<th>&nbsp;</th>";
      echo "<th>" . __('Model', 'pdu') . "</th>";
      echo "<th>" . __('Outlets Total', 'pdu') . "</th>";
      echo "</tr>";

      $modelid=-1;

      $result = $DB->query("SELECT * FROM `".$this->getTable()."`");
      while ($data = $DB->fetch_assoc($result)) {
         $modelid = $data['model_id'];
         $id = $data['id'];

         echo "<tr class='tab_bg_1'>";
         echo "<td class='center'>";
         echo "<input type='checkbox' name='item[$id]' value='1'>";
         echo "</td>";

         echo "<td>";
         echo "<a href=\"".$link."?id=".$modelid."\">";
         echo Dropdown::getDropdownName($table,$modelid);
         echo "</a></td>";
         echo "<td>" . $data['outlets'] . "</td>";
         echo "</tr>";
      }
      echo "</table>";
   }


   function isPdu(CommonGLPI $item) {
      if ($item->getType() != self::ITEM_TYPE)
         return false;

      $modelfield = getForeignKeyFieldForTable(getTableForItemType(self::ITEM_MODEL));
      $model_id = $item->fields[$modelfield];

      return $this->getFromDBByQuery(
         "WHERE `" . $this->getTable() . "`.`model_id` = '" . Toolbox::cleanInteger($model_id) . "' LIMIT 1");
   }
}

?>
