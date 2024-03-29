<?php

// Init the hooks of the plugins -Needed
function plugin_init_pdu() {
   global $PLUGIN_HOOKS,$CFG_GLPI;
   static $types = array( 'Computer','NetworkEquipment','Peripheral' );

   Plugin::registerClass('PluginPduConnection', 
       array( "addtabon" => $types )
   );

   Plugin::registerClass('PluginPduModel',
       array( "addtabon" => "NetworkEquipmentModel" )
   );

   $PLUGIN_HOOKS['menu_entry']['pdu'] = 'front/model.php';
   foreach ($types as $type) {
      $PLUGIN_HOOKS['item_purge']['pdu'][$type] = 'plugin_item_purge_pdu';
   }

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
                'license'        => 'GPLv2+',
                'minGlpiVersion' => '0.84');// For compatibility / no install in version < 0.80
}


function plugin_pdu_check_prerequisites() {
   return true;
}

function plugin_pdu_check_config($verbose=false) {
   return true;
}

?>
