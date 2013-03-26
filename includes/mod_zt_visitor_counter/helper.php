<?php
/**
 * @package ZT Counter module
 * @author Hiepvu
 * @copyright(C) 2013 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// no direct access
defined('_JEXEC') or die;

class modZTCounterHelper
{

    private $db;

    private $params;

    public function __construct($params)
    {
        $this->db = JFactory::getDbo();
        $this->params = $params;
    }

    public function getTodayVisitors()
    {
        $query = $this->db->getQuery(true);
        ;
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN UNIX_TIMESTAMP(DATE(NOW())) AND UNIX_TIMESTAMP(DATE(NOW()) + INTERVAL 1 DAY)");
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function getYesterdayVisitors()
    {
        $query = $this->db->getQuery(true);
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN UNIX_TIMESTAMP(DATE(NOW() - INTERVAL 1 DAY)) AND UNIX_TIMESTAMP(DATE(NOW()))");
        $this->db->setQuery($query);

        $result = $this->db->loadResult();
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function getForecastToday()
    {
        $config = JFactory::getConfig();
        $offset = $config->get('offset');
        $date = JFactory::getDate('now', $offset);
        $timeStart = mktime(0, 0, 0, $date->format('m', true), $date->format('d', true), $date->format('Y', true));
        $timeEnd = $date->toUnix();
        $hours = $date->format('H', true);

        $query = $this->db->getQuery(true);
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN " . $timeStart . " AND " . $timeEnd . "");
        $this->db->setQuery($query);
        $visitors = $this->db->loadResult();

        if ($visitors > 0) {

            $avgVisitors = round($visitors / $hours);
            $foreCastToday = $avgVisitors * 24;
            return $foreCastToday;

        } else {
            return $visitors;
        }
    }

    /*
     * The method to get all visit of this week now
     */
    public function getWeekVisitors()
    {
        $weeks = array();

        switch ($this->params->get('startDay', 0)) {
            case 0:
                $weeks = $this->getWeekStartEnd(time(), 'SUNDAY');
                break;
            case 1;
                $weeks = $this->getWeekStartEnd(time(), 'MONDAY');
        }
        $query = $this->db->getQuery(true);
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN " . strtotime($weeks['start']) . " AND " . strtotime('+1 day' . $weeks['end']) . "");
        $this->db->setQuery($query);

        $result = $this->db->loadResult();
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function getLastWeekVisitors()
    {
        $weeks = array();
        $time = strtotime('-6 days', time());

        switch ($this->params->get('startDay', 0)) {
            case 0:
                $weeks = $this->getWeekStartEnd($time, 'SUNDAY');
                break;
            case 1;
                $weeks = $this->getWeekStartEnd($time, 'MONDAY');
        }

        $query = $this->db->getQuery(true);
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN " . strtotime($weeks['start']) . " AND " . strtotime('+1 day' . $weeks['end']) . "");
        $this->db->setQuery($query);

        $result = $this->db->loadResult();

        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    /*
     * The method to get all visit of this month now
     */
    public function getMonthVisitors()
    {
        $query = $this->db->getQuery(true);
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-01')) AND UNIX_TIMESTAMP(DATE_FORMAT(NOW(),'%Y-%m-01') + INTERVAL 1 MONTH)");
        $this->db->setQuery($query);

        $result = $this->db->loadResult();
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    /*
     * The method to get all visit of last month now
     */
    public function getLastMonthVisitors()
    {
        $query = $this->db->getQuery(true);
        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $query->where("timestamp BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-01') - INTERVAL 1 MONTH) AND UNIX_TIMESTAMP(DATE_FORMAT(NOW(),'%Y-%m-01'))");
        $this->db->setQuery($query);

        $result = $this->db->loadResult();
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    /*
     * the method to get all visitors on base
     */
    public function getTotalVisitors()
    {
        $query = $this->db->getQuery(true);

        $query->select("SUM(visits)");
        $query->from("#__zt_visitor_counter");
        $this->db->setQuery($query);
        $total = $this->db->loadResult();


        if ($total == null) {
            $total = $this->params->get('initialValue');
        } else {
            $total += $this->params->get('initialValue');
        }

        return $total;
    }

    /*
     * Get time start and time end of the week for current time
     */
    private function getWeekStartEnd($time = '', $startDayOfWeek = 'Sunday')
    {
        if (empty($time)) {
            $time = time();
        }

        $startDay = date('l', $time);

        $startWeek = $time;

        if ($startDay != $startDayOfWeek) {
            $startWeek = strtotime('-1 ' . $startDayOfWeek, $time);
        }

        $WeekS = date('d.m.Y', $startWeek);
        $WeekE = date('d.m.Y', strtotime('+6 days', $startWeek));

        return array('start' => $WeekS, 'end' => $WeekE);
    }

    /*
     * get a number digits that was limited by admin (min digits)
     */
    public function getDigits($number, $length = 0)
    {
        $number = ($length > strlen($number)) ? substr('000000000' . $number, -$length) : $number;

        return $number;
    }

    /*
     * render a digits html with $digits
     */
    public function renderDigitsCounter($digits)
    {
        $ret = "";
        $digits = str_split($digits);
        foreach ($digits as $digit) {
            $ret .= "<span class=\"digit-$digit\">$digit</span>";
        }

        return $ret;
    }

    public function renderIcons($config)
    {
        $content = "";
        if ($config['today']) {
            $content .= "<div class=\"ztvc-row ztvc-icon-today\"></div>";
        }
        if ($config["yesterday"]) $content .= "<div class=\"ztvc-row ztvc-icon-yesterday\"></div>";
        if ($config["thisWeek"]) $content .= "<div class=\"ztvc-row ztvc-icon-week\"></div>";
        if ($config["lastWeek"]) $content .= "<div class=\"ztvc-row ztvc-icon-week\"></div>";
        if ($config["thisMonth"]) $content .= "<div class=\"ztvc-row ztvc-icon-month\"></div>";
        if ($config["lastMonth"]) $content .= "<div class=\"ztvc-row ztvc-icon-month\"></div>";
        if ($config["all"]) $content .= "<div class=\"ztvc-row ztvc-icon-all\"></div>";

        return $content;
    }

    public function renderTitles($config)
    {
        $content = "";
        if ($config["today"]) $content .= "<div class=\"ztvc-row\" title=" . date('d/m/Y', time()) . ">" . $config["today"] . "</div>";
        if ($config["yesterday"]) $content .= "<div class=\"ztvc-row\" title=" . date('d/m/Y', strtotime('-1 day', time())) . ">" . $config["yesterday"] . "</div>";
        if ($config["thisWeek"]) $content .= "<div class=\"ztvc-row\">" . $config["thisWeek"] . "</div>";
        if ($config["lastWeek"]) $content .= "<div class=\"ztvc-row\">" . $config["lastWeek"] . "</div>";
        if ($config["thisMonth"]) $content .= "<div class=\"ztvc-row\">" . $config["thisMonth"] . "</div>";
        if ($config["lastMonth"]) $content .= "<div class=\"ztvc-row\">" . $config["lastMonth"] . "</div>";
        if ($config["all"]) $content .= "<div class=\"ztvc-row\">" . $config["all"] . "</div>";

        return $content;
    }

    public function renderTotalVisit($config, $totals)
    {
        $content = "";

        if ($config["today"]) $content .= "<div class=\"ztvc-row\">" . $totals['today'] . "</div>";
        if ($config["yesterday"]) $content .= "<div class=\"ztvc-row\">" . $totals['yesterday'] . "</div>";
        if ($config["thisWeek"]) $content .= "<div class=\"ztvc-row\">" . $totals['thisWeek'] . "</div>";
        if ($config["lastWeek"]) $content .= "<div class=\"ztvc-row\">" . $totals['lastWeek'] . "</div>";
        if ($config["thisMonth"]) $content .= "<div class=\"ztvc-row\">" . $totals['thisMonth'] . "</div>";
        if ($config["lastMonth"]) $content .= "<div class=\"ztvc-row\">" . $totals['lastMonth'] . "</div>";
        if ($config["all"]) $content .= "<div class=\"ztvc-row\">" . $totals['all'] . "</div>";

        return $content;
    }

    // show online count in duration (minutes)

    public function getOnline($duration = 7)
    {

        $db = JFactory::getDbo();
        $lifeTime = $duration * 60;
        $time = time();
        $result = array();
        $count_member = 0;
        $count_guest = 0;
        $query = $db->getQuery(true);

        $query->select('guest, client_id');
        $query->from('#__session');
        $query->where('client_id = 0 AND ' . $db->quoteName('time') . ' BETWEEN ' . ($time - $lifeTime) . ' AND ' . $time);
        $db->setQuery($query);

        $sessions = (array)$db->loadObjectList();

        if (count($sessions)) {
            foreach ($sessions as $session) {

                if ($session->guest == 1) {
                    $count_guest++;
                }

                if ($session->guest == 0) {
                    $count_member++;
                }
            }
        }

        $result['member'] = $count_member;
        $result['guest'] = $count_guest;
        $result['total_online'] = $count_member + $count_guest;

        return $result;
    }


    public function getVisitorsAgent()
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('SUM(visits) AS count, useragent');
        $query->from('#__zt_visitor_counter');
        $query->group('useragent');
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function renderIconsUserAgents()
    {
        // count visitors by agent
        $visitors = $this->getVisitorsAgent();
        $content = '';
        $totalVisitors = 0;
        $userAgents = array();

        foreach ($visitors as $row) {
            $totalVisitors += $row->count;
            $userAgents[$row->useragent] = $row->count;
        }
        foreach ($userAgents as $key => $value) {
            if ($key == 'chrome' || $key == 'firefox' || $key == 'msie' || $key == 'safari' || $key == 'opera') {
                $content .= "<div class=\"ztvc-column center ztvc-icon-$key\">" . number_format(($value / $totalVisitors) * 100, 2, '.', '.') . "%</div>";
            } else {
                $content .= "<div class=\"ztvc-column center ztvc-icon-other\">" . number_format((($value / $totalVisitors) * 100), 2, '.', '.') . "%</div>";
            }
        }

        return $content;
    }
}