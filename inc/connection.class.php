<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginPduConnection extends CommonDBTM {

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
                                 'itemtype' => get_class($item),
                                 'target'   => $CFG_GLPI['root_doc']."/plugins/pdu/front/model.form.php"));
   }

   function showForm ($ID, $options=array()) {
      $itemtype = -1;
      if (isset($options['itemtype'])) {
         $itemtype = $options['itemtype'];
      }

      $items_id = -1;
      if (isset($options['items_id'])) {
         $items_id = $options['items_id'];
      }

      // Existing item?
      if($this->getFromDBByModel($itemtype,$items_id))
         $ID = $this->fields["id"];

      if ($ID > 0) {
         $this->check($ID,'r');
      } else {
         // Create item
         $this->check(-1,'w',$input);
      }

      $this->showFormHeader($options);

      if ($ID > 0) {
         echo "<input type='hidden' name='itemtype' value='".$this->fields["itemtype"]."'>";
         echo "<input type='hidden' name='model_id' value='".$this->fields["model_id"]."'>";
      } else {
         echo "<input type='hidden' name='itemtype' value='$itemtype'>";
         echo "<input type='hidden' name='model_id' value='$items_id'>";
      }

      echo "<tr class='tab_bg_1'>";
      echo "<td>" . __('Number of outlets') . "</td>";
      echo "<td>";
      Dropdown::showInteger("outlets_qty", ($ID > 0) ? $this->fields["outlets_qty"] : 1, 1, 50, 1);
      echo "</td>";
      echo "</tr>";
      $this->showFormButtons($options);
   }

   function getFromDBByModel($itemtype,$id) {
      global $DB;

      $query = "SELECT * FROM `".$this->getTable()."`
         WHERE `itemtype` = '$itemtype'
         AND `model_id` = '$id' ";
      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 1) {
            return false;
         }
         $this->fields = $DB->fetch_assoc($result);
         if (is_array($this->fields) && count($this->fields)) {
            return true;
         } else {
            return false;
         }
      }
      return false;
   }
}

?>
