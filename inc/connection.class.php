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

      $PluginPduModel = new PluginPduModel;

      if ( $PluginPduModel->isPdu($item) ){
         self::displayPduForm($item, $PluginPduModel, $tabnum, $withtemplate);
      } else {
         self::displayAssetForm($item, $tabnum, $withtemplate);
      }
   }


   static function displayPduForm(CommonGLPI $item, $objModel, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI, $DB;

      $self=new self();
      $rand = mt_rand();

      echo "<form id='add_connection_form' name='add_connection_form' method='POST' action='" . $self->getFormURL() ."'>";
      echo "<input type='hidden' name='pdu_id' value='".$item->getID()."'>";
      echo '<div>';
      echo  '<table class="tab_cadre_fixe">';
      echo   '<tr class="tab_bg_2">';
      echo    '<td>';
      _e('Connect outlet');
      echo "&nbsp;";

      $outlets = array( '0' => Dropdown::EMPTY_VALUE );
      $outlets += array_combine( range(1,$objModel->getField('outlets')), range(1,$objModel->getField('outlets')) );

      Dropdown::showFromArray(
         "outlet_id", 
         $outlets,
         array( 'rand' => $rand,
                'used' => $self->listUsedOutlets($item->getID()),
         )
      );

      echo "&nbsp;";
      _e('to');
      echo "&nbsp;";

      $connected_types = array( '0' => Dropdown::EMPTY_VALUE );
      foreach(self::getAssetClasses() as $class_name) {
         $connected_types[$class_name] = $class_name;
      }

      $params = array(
         'idtable' => '__VALUE__',
         'rand'    => $rand,
         'myname'  => 'connected_id'
      );

      Dropdown::showFromArray(
         "connected_itemtype", 
         $connected_types,
         array( 'rand' => $rand )
      );

      Ajax::updateItemOnSelectEvent(
         "dropdown_connected_itemtype$rand", "show_connected_id$rand",
         $CFG_GLPI["root_doc"]."/plugins/pdu/ajax/dropdownAllItems.php",
         $params
      );

      echo "<span id='show_connected_id$rand'>&nbsp;</span>\n";
      echo    "</td>";
      echo    "<td><input type='submit' name='add' value=\""._sx('button','Add')."\" class='submit'></td>\n";
      echo   '</tr>';
      echo  '</table>';
      echo '</div>';
      Html::closeForm();



      echo "<form id='pdu_connections$rand' name='pdu_connections$rand' method='POST' action='" . $self->getFormURL() ."'>";
      echo '<div>';
      echo '<table class="tab_cadre_fixe">';
      echo '<tr class="tab_bg_2">';
      echo '<th width="10"></th>';
      echo '<th width="1%">' . __('Outlet', 'pdu') . '</th>';
      echo '<th>' . __('Connected to', 'pdu') . '</th>';
      echo '<th>' . __('Serial', 'pdu') . '</th>';
      echo '<th>' . __('Type', 'pdu') . '</th>';
      echo '<th>' . __('Model', 'pdu') . '</th>';
      echo '<th>' . __('Location', 'pdu') . '</th>';
      echo '</tr>';

      $data = $self->find("`pdu_id`='".$item->getID()."'");
      $outlets = array();
      foreach($data as $key => $assoc) {
         $outlets[$assoc['outlet_id']] = $assoc;
      }

      $n = 1;
      for($outlet_id = 1; $outlet_id <= $objModel->getField('outlets'); $outlet_id++) {

         if (array_key_exists($outlet_id, $outlets)) {
            $asset_id = $outlets[$outlet_id]['connected_id'];
            $asset_class = $outlets[$outlet_id]['connected_itemtype'];
            $asset_url = Toolbox::getItemTypeFormURL($asset_class);
            $asset_model_table = getTableForItemType($asset_class."Model");
            $asset_model_field = getForeignKeyFieldForTable($asset_model_table);
            $class = new $asset_class;
            $class->getFromDB($asset_id);

            echo '<tr style="background-color: #c5e693">';
            echo '<td><input type="checkbox" name="item['.$outlets[$outlet_id]['id'].']" value="1"></td>';
            echo '<td class="center">'.$outlet_id.'</td>';
            echo '<td class="center"><a href="'.$asset_url.'?id='.$asset_id.'">'.
            Dropdown::getDropdownName(
               getTableForItemType($asset_class), 
               $asset_id
            );
            echo '<td class="center">'.$class->getField('serial').'</td>';
            echo '</a></td>';
            echo '<td class="center">'.$class::getTypeName(2).'</td>';
            echo '<td class="center">'.Dropdown::getDropdownName($asset_model_table,$class->getField($asset_model_field)).'</td>';
            echo '<td class="center">'.Dropdown::getDropdownName("glpi_locations",$class->getField("locations_id")).'</td>';
         } else {
            echo '<tr class="tab_bg_'.(($n%2==0)?"2":"1").'">';
            echo '<td></td>';
            echo '<td class="center">'.$outlet_id.'</td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td></td>';
         }

         echo '</tr>';
         $n++;
      }
      echo '</table>';
      echo '</div>';

      Html::openArrowMassives("pdu_connections$rand",true);
      Html::closeArrowMassives(array('delete' => _sx('button','Disconnect')));
      Html::closeForm();
   }


   static function displayAssetForm(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI, $DB;

      $self=new self();
      $rand = mt_rand();

      echo "<form id='add_connection_form' name='add_connection_form' method='POST' action='" . $self->getFormURL() ."'>";
      echo "<input type='hidden' name='connected_id' value='".$item->getID()."'>";
      echo "<input type='hidden' name='connected_itemtype' value='".$item->getType()."'>";
      echo '<div>';
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
            `glpi_plugin_pdu_connections`.`id` AS `connection_id`,
            `glpi_networkequipmentmodels`.`name` AS `model_name`
         FROM `glpi_networkequipments`
            LEFT JOIN `glpi_plugin_pdu_connections`
               ON `glpi_plugin_pdu_connections`.`pdu_id`=`glpi_networkequipments`.`id`
            LEFT JOIN `glpi_locations`
               ON `glpi_locations`.`id`=`glpi_networkequipments`.`locations_id`
            LEFT JOIN `glpi_networkequipmentmodels`
               ON `glpi_networkequipmentmodels`.`id`=`glpi_networkequipments`.`networkequipmentmodels_id`
         WHERE
            `glpi_plugin_pdu_connections`.`connected_id`=".$item->getID()."
            AND `glpi_plugin_pdu_connections`.`connected_itemtype`='".$item->getType()."'";

      echo "<form id='outlets$rand' name='outlets$rand' method='POST' action='" . $self->getFormURL() ."'>";
      echo '<div>';
      echo '<table class="tab_cadre_fixe">';
      echo '<tr class="tab_bg_2">';
      echo '<th width="10"></th>';
      echo '<th>' . __('PDU Name', 'pdu')  .  '</th>';
      echo '<th>' . __('Model', 'pdu')  .  '</th>';
      echo '<th>' . __('Location', 'pdu')  .  '</th>';
      echo '<th>' . __('Outlet', 'pdu')  .  '</th>';
      echo '</tr>';

      $result = $DB->query($query);
      while ($data = $DB->fetch_assoc($result)) {
         echo "<tr>";
         echo '<td width="10"><input type="checkbox" name="item['.$data['connection_id'].']" value="1"></td>';
         echo '<td class="center"><a href="'. Toolbox::getItemTypeFormURL(PluginPduModel::ITEM_MODEL)."?id=".$data['id']."\">".$data['name']."</td>";
         echo '<td class="center">'.$data['model_name'].'</td>';
         echo '<td class="center">'.$data['location']."</td>";
         echo '<td class="center">'.$data['outlet_id']."</td>";
         echo "</tr>";
      }

      Html::openArrowMassives("outlets$rand",true);
      Html::closeArrowMassives(array('delete' => _sx('button','Disconnect')));
      Html::closeForm();

      echo '</table>';
      echo '</div>';
   }

   static function getAssetClasses () {
      static $types = array( 'Computer','NetworkEquipment','Peripheral' );
      return $types;
   }

   function listUsedOutlets($ID) {
      $data = $this->find("`pdu_id`='$ID'");

      $outlets = array();
      foreach($data as $key => $assoc) {
         $outlets[$assoc['outlet_id']] = $assoc['outlet_id'];
      }
      return $outlets;
   }


   function prepareInputForAdd($input) {
      return $this->prepareInput($input);
   }


   function prepareInputForUpdate($input) {
      return $this->prepareInput($input);
   }


   function prepareInput($input) {
      global $DB;

      $table_fields = $DB->list_fields($this->getTable());

      foreach($table_fields as $field => $val) {
         if (empty($input[$field]))
            return false;
      }

      return $input;
   }
}

?>
