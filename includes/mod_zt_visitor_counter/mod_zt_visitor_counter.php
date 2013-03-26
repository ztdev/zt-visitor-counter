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

// Require the base helper class only once
require_once dirname(__FILE__) . DS . 'helper.php';

$enabled = JPluginHelper::isEnabled('system', 'zt_visitor_counter');

if (!$enabled) {
    return JError::raiseWarning(0, JText::_("MOD_ZT_VISITOR_COUNTER_ERROR_PLUGIN"));
}
$moduleclass_sfx = $params->get('moduleclass_sfx', '');

$content = '';
$totals = array();

$show = array('today' => $params->get('today'), 'yesterday' => $params->get('yesterday'), 'thisWeek' => $params->get('thisWeek', 'This Week'), 'lastWeek' => $params->get('lastWeek', 'Last Week'), 'thisMonth' => $params->get('thisMonth', 'This Month'), 'lastMonth' => $params->get('lastMonth', 'Last Month'), 'all' => $params->get('all'), 'foreCast' => $params->get('foreCast', 'Forecast Today'));
$widths = $params->get('width');
$showDigit = $params->get("showDigit", 1) ? true : false;
$minDigits = $params->get('minDigits', 8);
$showIcons = $params->get('showIcons', 1) ? true : false;
$showTitles = $params->get('showTitles', 1) ? true : false;
$showTotals = $params->get('showTotals', 1) ? true : false;
$firstDay = $params->get('firstDay', 0);
$digit_style = $params->get('digit_style', 'default');
$module_style = $params->get('module_style', 'default');
// for count online
$showOnline = $params->get('showOnline', 1) ? true : false;
$duration = $params->get('duration', 15);
$showGuestOnline = $params->get('showGuestOnline', 1) ? true : false;
$showMemberOnline = $params->get('showMemberOnline', 1) ? true : false;
$showAllOnline = $params->get('showAllOnline', 1) ? true : false;
$showAgent = $params->get('showAgent', 1) ? true : false;
$showForeCast = $params->get('foreCast', 1) ? true : false;
$ip = ($params->get('showIp') != '0') ? $params->get('showIp') . $_SERVER['REMOTE_ADDR'] : '';

$help = new modZTCounterHelper($params);

$cache_time = (int)$params->get('cache_time', 900);
$cache_enabled = $params->get('cache', 0) ? true : false;
$cache = & JFactory::getCache('mod_zt_visitor_counter');
$cache->setCaching($cache_enabled);
$cache->setLifeTime($cache_time);

if ($show['today']) {

    $totals["today"] = $help->getTodayVisitors();

}
if ($show['yesterday']) {

    if ($cache_enabled) {
        $totals["yesterday"] = $cache->call(array($help, 'getYesterdayVisitors'));
    } else {
        $totals["yesterday"] = $help->getYesterdayVisitors();
    }

}
if ($show['thisWeek']) {

    if ($cache_enabled) {
        $totals["thisWeek"] = $cache->call(array($help, 'getWeekVisitors'));
    } else {
        $totals["thisWeek"] = $help->getWeekVisitors();
    }

}

if ($show['lastWeek']) {

    if ($cache_enabled) {
        $totals["lastWeek"] = $cache->call(array($help, 'getLastWeekVisitors'));
    } else {
        $totals["lastWeek"] = $help->getLastWeekVisitors();
    }

}

if ($show['thisMonth']) {

    if ($cache_enabled) {
        $totals["thisMonth"] = $cache->call(array($help, 'getMonthVisitors'));
    } else {
        $totals["thisMonth"] = $help->getMonthVisitors();
    }

}
if ($show['lastMonth']) {

    if ($cache_enabled) {
        $totals["lastMonth"] = $cache->call(array($help, 'getLastMonthVisitors'));
    } else {
        $totals["lastMonth"] = $help->getLastMonthVisitors();
    }

}
if ($show['all']) {

    if ($cache_enabled) {
        $totals["all"] = $cache->call(array($help, 'getTotalVisitors'));
    } else {
        $totals["all"] = $help->getTotalVisitors();
    }
}
if ($show['foreCast']) {

    if ($cache_enabled) {
        $totals["foreCast"] = $cache->call(array($help, 'getForecastToday'));
    } else {
        $totals["foreCast"] = $help->getForecastToday();
    }
}

if ($showDigit) {
    $digits = modZTCounterHelper::getDigits($totals["all"], $minDigits);
    $digits = modZTCounterHelper::renderDigitsCounter($digits);
}
// count visitors by agent
if ($showAgent) {

    if ($cache_enabled) {
        $userAgents = $cache->call(array($help, 'renderIconsUserAgents'));
    } else {
        $userAgents = $help->renderIconsUserAgents();
    }
}
// count online
$count = $help->getOnline($duration);

// add css
$document = JFactory::getDocument();

$document->addStyleSheet(JURI::base() . 'modules/mod_zt_visitor_counter/assets/css/zt_visitor_counter.css');
$document->addStyleSheet(JURI::base() . 'modules/mod_zt_visitor_counter/assets/digit/' . $digit_style . '.css');
$document->addStyleSheet(JURI::base() . 'modules/mod_zt_visitor_counter/assets/style/' . $module_style . '.css');

$document->addCustomTag("
                    <style>
                            .ztvc-visitor-counter {
                                width: " . $widths[0] . $widths[1] . ";
                            }
                    </style>
           ");

require JModuleHelper::getLayoutPath('mod_zt_visitor_counter', 'default');