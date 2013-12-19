CREATE TABLE `glpi_plugin_pdu_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `outlets` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_pdu_connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pdu_id` int(11) NOT NULL,
  `outlet_id` smallint(6) NOT NULL,
  `connected_id` int(11) NOT NULL,
  `connected_itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

