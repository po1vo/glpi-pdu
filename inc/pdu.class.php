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

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      return __('PDUs', 'pdu');
   }

   static function displayTabContentForItem (CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      print '<table class="tab_cadre_fixe">';
      print '<tr class="tab_bg_2">';
      print '<th colspan=2>Connect to a PDU</th>';
      print '</tr>';
      print '</table>';

      print '<table class="tab_cadre_fixe">';
      print '<tr class="tab_bg_2">';
      print '<th>&nbsp;</th>';
      print '<th>' . __('PDU Name', 'pdu')  .  '</th>';
      print '<th>' . __('PDU Type', 'pdu')  .  '</th>';
      print '<th>' . __('Location', 'pdu')  .  '</th>';
      print '<th>' . __('Outlet', 'pdu')  .  '</th>';
      print '</tr>';
      print '</table>';
   }

   function getSearchOptions() {
      
   }

}

?>
