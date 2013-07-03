<?php
/**
 * @package ZT Counter module
 * @author Hiepvu
 * @copyright(C) 2013 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// no direct access
defined('_JEXEC') or die;

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

require_once(dirname(__FILE__) . DS . 'helper.php');

class plgSystemZT_Visitor_Counter extends JPlugin
{

    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }


    public function onAfterInitialise()
    {

        if (JFactory::getApplication()->isAdmin()) return;

        $timeNow = mktime();

        $config = & JFactory::getConfig();
//        $lifeTime = $config->get('lifetime') * 60;
        //$time     = $timeNow + $lifeTime;
        $time = strtotime('+' . $config->get('lifetime') . ' minutes', $timeNow);
        $session = & JFactory::getSession();

        $browserInfo = plgZTVisitorCounterHelper::checkBrowserInfo();

        if ($browserInfo['clientType'] != 'bots') {

            $lastVisit = (int)plgZTVisitorCounterHelper::getLastVisit($browserInfo['ipAddress'], $browserInfo['userAgent'], false);

            if ($timeNow > $lastVisit) {

                plgZTVisitorCounterHelper::addVisits($time, $browserInfo['clientType'], $browserInfo['ipAddress'], $browserInfo['userAgent']);

                return;
            }

            if ($session->isNew()) {
                plgZTVisitorCounterHelper::updateVisits($lastVisit, $browserInfo['clientType'], $browserInfo['ipAddress'], $browserInfo['userAgent']);
                return;
            }

        } else {

            $lastVisit = (int)plgZTVisitorCounterHelper::getLastVisit($browserInfo['ipAddress'], '', true);

            if ($timeNow > $lastVisit) {

                plgZTVisitorCounterHelper::addVisits($time, $browserInfo['clientType'], $browserInfo['ipAddress']);
                return;
            }

            if ($session->isNew()) {
                plgZTVisitorCounterHelper::updateVisits($lastVisit, $browserInfo['clientType'], $browserInfo['ipAddress']);
                return;
            }
        }

    }
}