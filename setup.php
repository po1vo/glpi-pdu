<?php

// Init the hooks of the plugins -Needed
function plugin_init_pdu() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   Plugin::registerClass('PluginPduPdu', 
       array( "addtabon" => "NetworkEquipment" )
   );

   Plugin::registerClass('PluginPduModel',
       array( "addtabon" => "NetworkEquipmentModel" )
   );

   $PLUGIN_HOOKS['item_purge']['pdu'] = array( 'NetworkEquipment' => 'plugin_item_purge_pdu' );
   $PLUGIN_HOOKS['submenu_entry']['pdu']['search'] = 'front/pdu.php';

   $PLUGIN_HOOKS['menu_entry']['pdu'] = 'front/pdu.php';
   $PLUGIN_HOOKS['submenu_entry']['pdu']["<img  src='".
      $CFG_GLPI["root_doc"]."/pics/menu_showall.png' title=\"".__('PDU models', 'racks').
      "\" alt=\"".__('pdu', 'pdu')."\">"] = 'front/model.php';


   // Massive Action definition
   $PLUGIN_HOOKS['use_massive_action']['pdu'] = 1;

   // CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm();
   $PLUGIN_HOOKS['csrf_compliant']['pdu'] = true;
}


// Get the name and the version of the plugin - Needed
function plugin_version_pdu() {

   return array('name'           => 'PDU',
                'version'        => '0.0.1',
                'author'         => 'Vadim Pisarev',
                'license'        => 'BSD',
                'minGlpiVersion' => '0.84');// For compatibility / no install in version < 0.80
}


function plugin_pdu_check_prerequisites() {
   return true;
}

function plugin_pdu_check_config($verbose=false) {
   return true;
}

?>
