<?php
/**
 * @package ZT Counter plugin
 * @author Hiepvu
 * @copyright(C) 2013 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
defined('_JEXEC') or die;

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
require_once(dirname(__FILE__) . DS . 'browser.php');

class plgZTVisitorCounterHelper
{

    /*
     * Add first counter
     */
    public static function addVisits($timeStamp = 0, $clientType = 'guests', $ipAddress, $userAgent = null)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->insert($db->quoteName('#__zt_visitor_counter'), false);

        $columns = array($db->quoteName('id'), $db->quoteName('timestamp'), $db->quoteName('visits'), $db->quoteName($clientType), $db->quoteName('ipaddress'), $db->quoteName('useragent'),);
        $query->columns($columns);
        $query->values('null, ' . $db->Quote($timeStamp) . ', 1 , 1 , ' . $db->Quote($ipAddress) . ', ' . $db->Quote($userAgent));
        $db->setQuery($query);
        $db->query();

    }

    /*
     * Update count visit
     */
    public static function updateVisits($timeStamp = 0, $clientType = 'guests', $ipAddress, $userAgent = null)
    {
        $db =& JFactory::getDbo();
        $query = " UPDATE #__zt_visitor_counter SET visits=visits + 1, " . $clientType . "=" . $clientType . " + 1, useragent = " . $db->quote($userAgent) . "" . " WHERE timestamp = " . $timeStamp . " AND " . $db->quoteName('ipaddress') . " = " . $db->quote($ipAddress) . ";";
        $db->setQuery($query);
        $db->query();

    }

    /*
     * Getting last time who visit site by ip and browser
     */
    public static function getLastVisit($ipAddress = '', $userAgent = '', $bot = false)
    {
        $db =& JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('timestamp');
        $query->from('#__zt_visitor_counter');
        $query->where('ipaddress = ' . $db->quote($ipAddress));
        if (!$bot) {
            $query->where('useragent LIKE "%' . $userAgent . '%"');
        }
        $query->order('timestamp DESC');

        $db->setQuery($query, 0, 1);

        return $db->loadResult();

    }

    /*
     *
     */
    public static function checkBrowserInfo()
    {

        $session = & JFactory::getSession();
        $browserInfo = strtolower($session->get('session.client.browser'));
        $browser = new Browser($browserInfo);
        $user = JFactory::getUser();
        $userAgent = '';
        $clientType = '';

        switch ($browser->getBrowser()) {

            case 'Opera':
                $userAgent = 'opera';
                break;
            case 'Internet Explorer':
                $userAgent = 'msie';
                break;
            case 'Chrome':
                $userAgent = 'chrome';
                break;
            case 'Firefox':
                $userAgent = 'firefox';
                break;
            default:
                $userAgent = 'other';
                break;
        }

        if ($user->get('guest')) {

            if ($browser->isRobot()) {
                $clientType = 'bots';
            } else {
                $clientType = 'guests';
            }
        } else {
            $clientType = 'members';
        }

        return array('userAgent' => $userAgent, 'clientType' => $clientType, 'ipAddress' => JRequest::getVar('REMOTE_ADDR', '', 'SERVER'));
    }

}