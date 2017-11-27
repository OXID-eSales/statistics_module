<?php
/**
 * This file is part of OXID eSales Statistics module.
 *
 * OXID eSales Statistics module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Statistics module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Statistics module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category      module
 * @package       oestatistics
 * @author        OXID eSales AG
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Canceled orders reports class
 */
class OeStatistics_Report_Canceled_Orders extends OeStatistics_Report_Base
{
    /** @var string Name of template to render. */
    protected $_sThisTemplate = "oestatistics_report_canceled_orders.tpl";

    /**
     * Checks if db contains data for report generation
     *
     * @return bool
     */
    public function drawReport()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $smarty = $this->getSmarty();
        $timeFrom = $database->quote(date("Y-m-d H:i:s", strtotime($smarty->_tpl_vars['time_from'])));
        $timeTo = $database->quote(date("Y-m-d H:i:s", strtotime($smarty->_tpl_vars['time_to'])));

        // collects sessions what executed 'order' function
        $query = "select 1 from `oestatisticslog` where oxclass = 'order' and
                   oxfnc = 'execute' and oxtime >= $timeFrom and oxtime <= $timeTo";
        if ($database->getOne($query)) {
            return true;
        }

        // collects sessions what executed order class
        $query = "select 1 from `oestatisticslog` where oxclass = 'order' and oxtime >= $timeFrom and oxtime <= $timeTo";
        if ($database->getOne($query)) {
            return true;
        }

        // collects sessions what executed payment class
        $query = "select 1 from `oestatisticslog` where oxclass = 'payment' and oxtime >= $timeFrom and oxtime <= $timeTo";
        if ($database->getOne($query)) {
            return true;
        }

        // collects sessions what executed 'user' class
        $query = "select 1 from `oestatisticslog` where oxclass = 'user' and oxtime >= $timeFrom and oxtime <= $timeTo";
        if ($database->getOne($query)) {
            return true;
        }

        // collects sessions what executed 'tobasket' function
        $query = "select 1 from `oestatisticslog` where oxclass = 'basket' and oxtime >= $timeFrom and oxtime <= $timeTo";
        if ($database->getOne($query)) {
            return true;
        }

        // orders made
        $query = "select 1 from oxorder where oxorderdate >= $timeFrom and oxorderdate <= $timeTo";
        if ($database->getOne($query)) {
            return true;
        }
    }

    /**
     * Collects sessions what executed 'order' function
     *
     * @param string $query data query
     *
     * @return array
     */
    protected function _collectSessions($query)
    {
        $order = array();
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $order[$resultSet->getFields()[1]] = $resultSet->getFields()[0];
                $resultSet->fetchRow();
            }
        }

        return $order;
    }

    /**
     * Collects sessions what executed order class
     *
     * @param string $query  Data query
     * @param array  $orders Orders
     * @param array  $data   Data to fill (X6)
     * @param bool   $month  If TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectOrderSessions($query, $orders, &$data, $month = true)
    {
        $orderSessions = array();
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam('iFirstWeekDay');
            while (!$resultSet->EOF) {
                if (!isset($orders[$resultSet->getFields()[1]])) {
                    $orderSessions[$resultSet->getFields()[1]] = 1;
                    $key = strtotime($resultSet->getFields()[0]);
                    $utilsData = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
                    $key = $month ? date("m/Y", $key) : $utilsData->getWeekNumber($iFirstWeekDay, $key);
                    if (isset($data[$key])) {
                        $data[$key]++;
                    }
                }
                $resultSet->fetchRow();
            }
        }

        return $orderSessions;
    }

    /**
     * Collects sessions what executed payment class
     *
     * @param string $query         Data query
     * @param array  $orders        Orders
     * @param array  $orderSessions Finished orders
     * @param array  $dataToFill    Data to fill
     * @param bool   $month         If TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectPaymentSessions($query, $orders, $orderSessions, &$dataToFill, $month = true)
    {
        $paymentSessions = array();
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam('iFirstWeekDay');
            while (!$resultSet->EOF) {
                if (!isset($orders[$resultSet->getFields()[1]]) && !isset($orderSessions[$resultSet->getFields()[1]])) {
                    $paymentSessions[$resultSet->getFields()[1]] = 1;
                    $key = strtotime($resultSet->getFields()[0]);
                    $key = $month ? date("m/Y", $key) : \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($iFirstWeekDay, $key);
                    if (isset($dataToFill[$key])) {
                        $dataToFill[$key]++;
                    }
                }
                $resultSet->fetchRow();
            }
        }

        return $paymentSessions;
    }

    /**
     * Collects sessions what executed 'user' class
     *
     * @param string $query           data query
     * @param array  $orders          orders
     * @param array  $orderSessions   finished orders
     * @param array  $paymentSessions payment sessions
     * @param array  $dataToFill      data to fill
     * @param bool   $month           if TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectUserSessionsForVisitorMonth($query, $orders, $orderSessions, $paymentSessions, &$dataToFill, $month = true)
    {
        $userSessions = array();
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam('iFirstWeekDay');
            while (!$resultSet->EOF) {
                if (!isset($orders[$resultSet->getFields()[1]]) && !isset($paymentSessions[$resultSet->getFields()[1]]) && !isset($orderSessions[$resultSet->getFields()[1]])) {
                    $userSessions[$resultSet->getFields()[1]] = 1;
                    $sKey = strtotime($resultSet->getFields()[0]);
                    $sKey = $month ? date("m/Y", $sKey) : \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($iFirstWeekDay, $sKey);
                    if (isset($dataToFill[$sKey])) {
                        $dataToFill[$sKey]++;
                    }
                }
                $resultSet->fetchRow();
            }
        }

        return $userSessions;
    }

    /**
     * Collects sessions what executed 'tobasket' function
     *
     * @param string $query           data query
     * @param array  $orders          orders
     * @param array  $ordersSessions  finished orders
     * @param array  $paymentSessions payment sessions
     * @param array  $userSessions    user sessions
     * @param array  $dataToFill      data to fill
     * @param bool   $month           if TRUE - for month, if FALSE - for week [true]
     */
    protected function _collectToBasketSessions($query, $orders, $ordersSessions, $paymentSessions, $userSessions, &$dataToFill, $month = true)
    {
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam('iFirstWeekDay');
            while (!$resultSet->EOF) {
                if (!$orders[$resultSet->getFields()[1]] && !isset($paymentSessions[$resultSet->getFields()[1]]) && !isset($userSessions[$resultSet->getFields()[1]]) && !isset($ordersSessions[$resultSet->getFields()[1]])) {
                    $sKey = strtotime($resultSet->getFields()[0]);
                    $sKey = $month ? date("m/Y", $sKey) : \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($iFirstWeekDay, $sKey);
                    if (isset($dataToFill[$sKey])) {
                        $dataToFill[$sKey]++;
                    }
                }
                $resultSet->fetchRow();
            }
        }
    }

    /**
     * Collects made orders
     *
     * @param string $query      data query
     * @param array  $dataToFill data to fill
     * @param bool   $month      if TRUE - for month, if FALSE - for week [true]
     */
    protected function _collectOrdersMade($query, &$dataToFill, $month = true)
    {
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam('iFirstWeekDay');
            while (!$resultSet->EOF) {
                $sKey = strtotime($resultSet->getFields()[0]);
                $sKey = $month ? date("m/Y", $sKey) : \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($iFirstWeekDay, $sKey);
                if (isset($dataToFill[$sKey])) {
                    $dataToFill[$sKey]++;
                }
                $resultSet->fetchRow();
            }
        }
    }

    /**
     * Collects made orders
     *
     * @param string $query      data query
     * @param array  $dataToFill data to fill
     */
    protected function _collectOrdersMadeForVisitorWeek($query, &$dataToFill)
    {
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $sKey = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber(oxConfig::getConfigParam('iFirstWeekDay'), strtotime($resultSet->getFields()[0]));
                if (isset($dataToFill[$sKey])) {
                    $dataToFill[$sKey]++;
                }
                $resultSet->fetchRow();
            }
        }
    }

    /**
     * Collects and renders visitor/month report data
     */
    public function visitor_month()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $dTimeTo = strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"));
        $sTimeTo = $database->quote(date("Y-m-d H:i:s", $dTimeTo));
        $dTimeFrom = mktime(23, 59, 59, date("m", $dTimeTo) - 12, date("d", $dTimeTo), date("Y", $dTimeTo));
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", $dTimeFrom));

        $query = "select oxtime, count(*) as nrof from oestatisticslog where oxtime >= {$sTimeFrom} and oxtime <= {$sTimeTo} group by oxsessid";

        $temp = array();
        for ($i = 1; $i <= 12; $i++) {
            $temp[date("m/Y", mktime(23, 59, 59, date("m", $dTimeFrom) + $i, date("d", $dTimeFrom), date("Y", $dTimeFrom)))] = 0;
        }

        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $temp[date("m/Y", strtotime($resultSet->getFields()[0]))]++;
                $resultSet->fetchRow();
            }
        }

        $aDataY = array_keys($temp);
        $aDataX2 = $aDataX3 = $aDataX4 = $aDataX5 = $aDataX6 = array_fill_keys($aDataY, 0);

        // collects sessions what executed 'order' function
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'order' and oxfnc = 'execute' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempOrder = $this->_collectSessions($query);

        // collects sessions what executed order class
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'order' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempExecOrdersSessions = $this->_collectOrderSessions($query, $aTempOrder, $aDataX6);

        // collects sessions what executed payment class
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'payment' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempPaymentSessions = $this->_collectPaymentSessions($query, $aTempOrder, $aTempExecOrdersSessions, $aDataX2);

        // collects sessions what executed 'user' class
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'user' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempUserSessions = $this->_collectUserSessionsForVisitorMonth($query, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aDataX2);

        // collects sessions what executed 'tobasket' function
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'basket' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $this->_collectToBasketSessions($query, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aTempUserSessions, $aDataX4);

        // orders made
        $query = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $this->_collectOrdersMade($query, $aDataX5);

        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = $this->getGraph(800, 600);

        // Description
        $graph->xaxis->setTickLabels($aDataY);

        // Set title and subtitle
        $graph->title->set("Monat");

        // Create the bar plot
        $bplot2 = new BarPlot(array_values($aDataX2));
        $bplot2->setFillColor("#9966cc");
        $bplot2->setLegend("Best.Abbr. in Bezahlmethoden");

        // Create the bar plot
        $bplot3 = new BarPlot(array_values($aDataX3));
        $bplot3->setFillColor("#ffcc00");
        $bplot3->setLegend("Best.Abbr. in Benutzer");

        // Create the bar plot
        $bplot4 = new BarPlot(array_values($aDataX4));
        $bplot4->setFillColor("#6699ff");
        $bplot4->setLegend("Best.Abbr. in Warenkorb");

        // Create the bar plot
        $bplot6 = new BarPlot(array_values($aDataX6));
        $bplot6->setFillColor("#ff0099");
        $bplot6->setLegend("Best.Abbr. in Bestellbestaetigung");

        // Create the bar plot
        $bplot5 = new BarPlot(array_values($aDataX5));
        $bplot5->setFillColor("silver");
        $bplot5->setLegend("Bestellungen");

        // Create the grouped bar plot
        $gbplot = new groupBarPlot(array($bplot4, $bplot3, $bplot2, $bplot6, $bplot5));
        $graph->add($gbplot);

        // Finally output the  image
        $graph->stroke();
    }

    /**
     * Collects and renders visitor/week report data
     */
    public function visitor_week()
    {
        $config = $this->getConfig();
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $aDataX = array();
        $aDataX2 = array();
        $aDataX3 = array();
        $aDataX4 = array();
        $aDataX5 = array();
        $aDataX6 = array();
        $aDataY = array();

        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"))));
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_from"))));

        $query = "select oxtime, count(*) as nrof from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid order by oxtime";

        $aTemp = array();
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);

        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $aTemp[\OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($config->getConfigParam('iFirstWeekDay'), strtotime($resultSet->getFields()[0]))]++;
                $resultSet->fetchRow();
            }
        }

        // initializing
        list($iFrom, $iTo) = $this->getWeekRange();
        for ($i = $iFrom; $i < $iTo; $i++) {
            $aDataX[$i] = 0;
            $aDataX2[$i] = 0;
            $aDataX3[$i] = 0;
            $aDataX4[$i] = 0;
            $aDataX5[$i] = 0;
            $aDataX6[$i] = 0;
            $aDataY[] = "KW " . $i;
        }

        foreach ($aTemp as $key => $value) {
            $aDataX[$key] = $value;
            $aDataX2[$key] = 0;
            $aDataX3[$key] = 0;
            $aDataX4[$key] = 0;
            $aDataX5[$key] = 0;
            $aDataX6[$key] = 0;
            $aDataY[] = "KW " . $key;
        }

        // collects sessions what executed 'order' function
        $query = "select oxtime, oxsessid FROM `oestatisticslog` where oxclass = 'order' and oxfnc = 'execute' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempOrder = $this->_collectSessions($query);

        // collects sessions what executed order class
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'order' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempExecOrdersSessions = $this->_collectOrderSessions($query, $aTempOrder, $aDataX6, false);

        // collects sessions what executed payment class
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'payment' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempPaymentSessions = $this->_collectPaymentSessions($query, $aTempOrder, $aTempExecOrdersSessions, $aDataX2, false);

        // collects sessions what executed 'user' class
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'user' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempUserSessions = $this->_collectUserSessionsForVisitorMonth($query, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aDataX2, false);

        // collects sessions what executed 'tobasket' function
        $query = "select oxtime, oxsessid from `oestatisticslog` where oxclass = 'basket' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $this->_collectToBasketSessions($query, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aTempUserSessions, $aDataX4, false);

        // orders made
        $query = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $this->_collectOrdersMade($query, $aDataX5, false);

        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = $this->getGraph(max(800, count($aDataX) * 80), 600);

        // Description
        $graph->xaxis->setTickLabels($aDataY);

        // Set title and subtitle
        $graph->title->set("Woche");

        // Create the bar plot
        $bplot2 = new BarPlot(array_values($aDataX2));
        $bplot2->setFillColor("#9966cc");
        $bplot2->setLegend("Best.Abbr. in Bezahlmethoden");

        // Create the bar plot
        $bplot3 = new BarPlot(array_values($aDataX3));
        $bplot3->setFillColor("#ffcc00");
        $bplot3->setLegend("Best.Abbr. in Benutzer");

        // Create the bar plot
        $bplot4 = new BarPlot(array_values($aDataX4));
        $bplot4->setFillColor("#6699ff");
        $bplot4->setLegend("Best.Abbr. in Warenkorb");

        // Create the bar plot
        $bplot6 = new BarPlot(array_values($aDataX6));
        $bplot6->setFillColor("#ff0099");
        $bplot6->setLegend("Best.Abbr. in Bestellbestaetigung");

        // Create the bar plot
        $bplot5 = new BarPlot(array_values($aDataX5));
        $bplot5->setFillColor("silver");
        $bplot5->setLegend("Bestellungen");

        // Create the grouped bar plot
        $gbplot = new groupBarPlot(array($bplot4, $bplot3, $bplot2, $bplot6, $bplot5));
        $graph->add($gbplot);

        // Finally output the  image
        $graph->stroke();
    }
}
