CREATE TABLE `glpi_plugin_pdu_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT '0',
  `outlets_qty` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_pdu_connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pdu_id` int(11) NOT NULL DEFAULT '0',
  `pdu_outlet` smallint(6) NOT NULL DEFAULT '0',
  `connected_id` int(11) NOT NULL DEFAULT '0',
  `connected_itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

