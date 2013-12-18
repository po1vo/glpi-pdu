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
      global $CFG_GLPI;

      $self=new self();
      $rand = mt_rand();

      echo "<form method='get' action='" . $self->getFormURL() ."'>";
      echo "<input type='hidden' name='connected_id' value='".$item->getID()."'>";
      echo "<input type='hidden' name='connected_itemtype' value='".$item->getType()."'>";
      echo '<div class="firstbloc">';
      echo  '<table class="tab_cadre_fixe">';
      echo   '<tr class="tab_bg_2">';
      echo    '<td>';
      _e('Connect to a PDU');
      echo "&nbsp;";

      $params = array( 'searchText' => '__VALUE__',
                       'rand'       => $rand, 
                );

      $default = Dropdown::showFromArray("pdu_id", array('0' => Dropdown::EMPTY_VALUE, '1' => '1'), array('rand' => $rand, 'display' => false) );

      Ajax::dropdown($CFG_GLPI["use_ajax"], "/plugins/pdu/ajax/dropdownPdus.php", $params, $default, $rand );
      Ajax::updateItemOnSelectEvent("dropdown_pdu_id".$rand, "outlet_id".$rand, "/plugins/pdu/ajax/dropdownOutlets.php", $params);

      echo "&nbsp;Outlet&nbsp;";

      echo "<span id='outlet_id".$rand."'>";      
      Dropdown::showFromArray("outlet_id", array('0' => Dropdown::EMPTY_VALUE), array('rand' => $rand) );
      echo "</span>";

      echo    "</td>";
      echo    "<td><input type='submit' name='create' value=\""._sx('button','Add')."\" class='submit'></td>\n";
      echo   '</tr>';
      echo  '</table>';
      echo '</div>';

      print '<table class="tab_cadre_fixe">';
      print '<tr class="tab_bg_2">';
      print '<th>&nbsp;</th>';
      print '<th>' . __('PDU Name', 'pdu')  .  '</th>';
      print '<th>' . __('Location', 'pdu')  .  '</th>';
      print '<th>' . __('Outlet', 'pdu')  .  '</th>';
      print '</tr>';
      print '</table>';
      Html::closeForm();
   }

   function listUsedOutlets($ID) {
      $data = $this->find("`pdu_id`=`$ID`");

      $outlets = array();
      foreach($data as $key => $assoc) {
         $outlets[] = $assoc['pdu_outlet'];
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
