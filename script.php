<?php
/**
 * @package ZT Visitor Counter Package
 * @author Hiepvu
 * @copyright(C) 2013 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// no direct access
defined('_JEXEC') or die;

class pkg_ZT_Visitor_CounterInstallerScript
{

    public function __constructor(JAdapterInstance $adapter)
    {

    }

    public function preflight($route, JAdapterInstance $adapter)
    {

    }

    public function postflight($route, JAdapterInstance $adapter)
    {

    }

    public function install(JAdapterInstance $adapter)
    {

        $db =& JFactory::getDBO();

        $query = "CREATE TABLE IF NOT EXISTS `#__zt_visitor_counter` (
					  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
					  `timestamp` int(10) unsigned NOT NULL,
					  `visits` int(8) unsigned NOT NULL DEFAULT '0',
					  `guests` int(8) unsigned NOT NULL DEFAULT '0',
					  `bots` int(8) unsigned NOT NULL DEFAULT '0',
					  `members` int(8) unsigned NOT NULL DEFAULT '0',
					  `ipaddress` varchar(150) DEFAULT NULL,
					  `useragent` varchar(120) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `timestamp` (`timestamp`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					";

        $db->setQuery($query);
        $db->query();

        // Enabled ZT Visitor Counter plugin
        $query = " UPDATE `#__extensions` SET `enabled`=1 WHERE `type`='plugin' AND `element`='zt_visitor_counter' LIMIT 1;";

        $db->setQuery($query);
        $db->query();

    }

    public function update(JAdapterInstance $adapter)
    {

    }

    public function uninstall(JAdapterInstance $adapter)
    {

        $query = " DROP TABLE IF EXISTS `#__zt_visitor_counter`;";
        $db =& JFactory::getDBO();
        $db->setQuery($query);
        $db->query();
    }
}
