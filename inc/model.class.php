<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginPduModel extends CommonDBTM {

   static $itemmodel = 'NetworkEquipmentModel';


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

      $PluginPduModel = new PluginPduModel;
      $PluginPduModel->getFromDB($input['id']);


      $this->update($input);
   }


   static function getModelClasses () {
      static $types = array( 'ComputerModel','NetworkEquipmentModel','PeripheralModel','PluginRacksOtherModel' );
      return $types;
   }


   function showList($withtemplate='') {

      $rand=mt_rand();
      Html::openMassiveActionsForm('mass'.__CLASS__.$rand);
      Html::showMassiveActions(__CLASS__);

      $this->showModels($rand);

      $massiveactionparams['ontop'] = false;
      Html::showMassiveActions(__CLASS__, $massiveactionparams);
      Html::closeForm();
   }


   function showModels() {
      global $DB;

      $link  = Toolbox::getItemTypeFormURL(self::$itemmodel);
      $table = getTableForItemType(self::$itemmodel);

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


   function getModelFromPduId($ID) {
      $this->getFromDBByQuery(
         "LEFT JOIN `glpi_networkequipmentmodels` 
            ON `glpi_networkequipmentmodels`.`id`=`glpi_plugin_pdu_models`.`model_id`  
         LEFT JOIN `glpi_networkequipments` 
            ON `glpi_networkequipments`.`networkequipmentmodels_id`=`glpi_networkequipmentmodels`.`id` 
         WHERE `glpi_networkequipmentmodels`.`id`=`".$ID."`"
      );
   }
}

?>
