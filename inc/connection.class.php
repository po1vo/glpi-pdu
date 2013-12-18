<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginPduConnection extends CommonDBTM {

   static function getTypeName($nb = 0) {
      return __('Connections to PDU', 'pdu');
   }

   static function canCreate() {
      return true;
   }

   static function canView() {
      return true;
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      return __('PDU connections', 'pdu');
   }

   static function displayTabContentForItem (CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI, $DB;

      $self=new self();
      $rand = mt_rand();

      echo "<form id='add_connection_form' name='add_connection_form' method='POST' action='" . $self->getFormURL() ."'>";
      echo "<input type='hidden' name='connected_id' value='".$item->getID()."'>";
      echo "<input type='hidden' name='connected_itemtype' value='".$item->getType()."'>";
      echo '<div class="firstbloc">';
      echo  '<table class="tab_cadre_fixe">';
      echo   '<tr class="tab_bg_2">';
      echo    '<td>';
      _e('Connect to a PDU');
      echo "&nbsp;";

      $params = array( 'searchText' => '__VALUE__',
                       'rand'       => $rand, );

      $default = Dropdown::showFromArray(
         "pdu_id", 
         array( '0' => Dropdown::EMPTY_VALUE ), 
         array( 
            'rand' => $rand, 
            'display' => false) 
      );
      Ajax::dropdown($CFG_GLPI["use_ajax"], "/plugins/pdu/ajax/dropdownPdus.php", $params, $default, $rand );

      echo "&nbsp;Outlet&nbsp;";

      echo "<span id='outlet_id".$rand."'>";      
      Dropdown::showFromArray("outlet_id", array('0' => Dropdown::EMPTY_VALUE), array('rand' => $rand) );
      echo "</span>";

      echo    "</td>";
      echo    "<td><input type='submit' name='add' value=\""._sx('button','Add')."\" class='submit'></td>\n";
      echo   '</tr>';
      echo  '</table>';
      echo '</div>';
      Html::closeForm();


      $query = "
         SELECT 
            `glpi_networkequipments`.`id`,
            `glpi_networkequipments`.`name`,
            `glpi_locations`.`completename` AS `location`,
            `glpi_plugin_pdu_connections`.`outlet_id`,
            `glpi_plugin_pdu_connections`.`id` AS `connection_id`
         FROM `glpi_networkequipments`
            LEFT JOIN `glpi_plugin_pdu_connections`
               ON `glpi_plugin_pdu_connections`.`pdu_id`=`glpi_networkequipments`.`id`
            LEFT JOIN `glpi_locations`
               ON `glpi_locations`.`id`=`glpi_networkequipments`.`locations_id`
         WHERE
            `glpi_plugin_pdu_connections`.`connected_id`=".$item->getID()."
            AND `glpi_plugin_pdu_connections`.`connected_itemtype`='".$item->getType()."'";

      echo "<form id='outlets$rand' name='outlets$rand' method='POST' action='" . $self->getFormURL() ."'>";
      echo '<div class="firstbloc">';
      echo '<table class="tab_cadre_fixe">';
      echo '<tr class="tab_bg_2">';
      echo '<th width="10"></th>';
      echo '<th>' . __('PDU Name', 'pdu')  .  '</th>';
      echo '<th>' . __('Location', 'pdu')  .  '</th>';
      echo '<th>' . __('Outlet', 'pdu')  .  '</th>';
      echo '</tr>';

      $result = $DB->query($query);
      while ($data = $DB->fetch_assoc($result)) {
         echo "<tr>";
         echo '<td width="10"><input type="checkbox" name="item['.$data['connection_id'].']" value="1"></td>';
         echo "<td><a href=\"". Toolbox::getItemTypeFormURL("NetworkEquipment")."?id=".$data['id']."\">".$data['name']."</td>";
         echo "<td>".$data['location']."</td>";
         echo "<td>".$data['outlet_id']."</td>";
         echo "</tr>";
      }

      Html::openArrowMassives("outlets$rand",true);
      Html::closeArrowMassives(array('delete' => _sx('button','Disconnect')));
      Html::closeForm();

      echo '</table>';
      echo '</div>';
   }

   function listUsedOutlets($ID) {
      $data = $this->find("`pdu_id`='$ID'");

      $outlets = array();
      foreach($data as $key => $assoc) {
         $outlets[$assoc['outlet_id']] = $assoc['outlet_id'];
      }
      return $outlets;
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
      Dropdown::showInteger("outlets", ($ID > 0) ? $this->fields["outlets"] : 1, 1, 50, 1);
      echo "</td>";
      echo "</tr>";
      $this->showFormButtons($options);
   }
}

?>
