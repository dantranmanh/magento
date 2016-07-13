<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('thuchi')};
CREATE TABLE {$this->getTable('thuchi')} (
  `tcid` int(11) unsigned NOT NULL auto_increment,
  `ngay` varchar(255) NOT NULL default '',
  `noidung` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`tcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 