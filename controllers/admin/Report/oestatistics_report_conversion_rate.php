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
 * Conversion rate reports class
 */
class OeStatistics_Report_Conversion_Rate extends OeStatistics_Report_Base
{
    /** @var string Name of template to render. */
    protected $_sThisTemplate = "oestatistics_report_conversion_rate.tpl";

    /**
     * Checks if db contains data for report generation
     *
     * @return bool
     */
    public function drawReport()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $smarty = $this->getSmarty();
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime($smarty->_tpl_vars['time_from'])));
        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime($smarty->_tpl_vars['time_to'])));

        // orders
        if ($database->getOne("select * from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo")) {
            return true;
        }

        // orders
        if ($database->getOne("select 1 from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo")) {
            return true;
        }
    }

    /**
     * Collects and renders visitor/month report data
     */
    public function visitor_month()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $aDataX = array();
        $aDataY = array();

        $dTimeTo = strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"));
        $dTimeFrom = mktime(23, 59, 59, date("m", $dTimeTo) - 12, date("d", $dTimeTo), date("Y", $dTimeTo));

        $sTimeTo = $database->quote(date("Y-m-d H:i:s", $dTimeTo));
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", $dTimeFrom));

        // orders
        $query = "select oxtime, count(*) as nrof from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTemp = array();
        for ($i = 1; $i <= 12; $i++) {
            $aTemp[date("m/Y", mktime(23, 59, 59, date("m", $dTimeFrom) + $i, date("d", $dTimeFrom), date("Y", $dTimeFrom)))] = 0;
        }

        $resultSet = $database->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $aTemp[date("m/Y", strtotime($resultSet->getFields()[0]))]++;
                $resultSet->fetchRow();
            }
        }

        $aDataX2 = array();
        $aDataX3 = array();

        foreach ($aTemp as $key => $value) {
            $aDataX[$key] = $value;
            $aDataX2[$key] = 0;
            $aDataX3[$key] = 0;
            $aDataY[] = $key;
        }

        // orders
        $query = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $resultSet = $database->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $sKey = date("m/Y", strtotime($resultSet->getFields()[0]));
                if (isset($aDataX2[$sKey])) {
                    $aDataX2[$sKey]++;
                }
                $resultSet->fetchRow();
            }
        }

        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = new Graph(800, 600, "auto");

        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $graph->setBackgroundImage($backgroundImage, BGIMG_FILLFRAME);

        // Use a "text" X-scale
        $graph->setScale("textlin");
        $graph->setY2Scale("lin");
        $graph->y2axis->setColor("red");

        // Label align for X-axis
        $graph->xaxis->setLabelAlign('center', 'top', 'right');

        // Label align for Y-axis
        $graph->yaxis->setLabelAlign('right', 'bottom');

        $graph->setShadow();
        // Description
        $graph->xaxis->setTickLabels($aDataY);


        // Set title and subtitle
        $graph->title->set("Monat");

        // Use built in font
        $graph->title->setFont(FF_FONT1, FS_BOLD);

        $aDataFinalX = array();
        foreach ($aDataX as $dData) {
            $aDataFinalX[] = $dData;
        }

        // Create the bar plot
        $l2plot = new LinePlot($aDataFinalX);
        $l2plot->setColor("navy");
        $l2plot->setWeight(2);
        $l2plot->setLegend("Besucher");
        //$l1plot->SetBarCenter();
        $l2plot->value->setColor("navy");
        $l2plot->value->setFormat('% d');
        $l2plot->value->hideZero();
        $l2plot->value->show();

        $aDataFinalX2 = array();
        foreach ($aDataX2 as $dData) {
            $aDataFinalX2[] = $dData;
        }

        // Create the bar plot
        $l3plot = new LinePlot($aDataFinalX2);
        $l3plot->setColor("orange");
        $l3plot->setWeight(2);
        $l3plot->setLegend("Bestellungen");
        //$l1plot->SetBarCenter();
        $l3plot->value->setColor('orange');
        $l3plot->value->setFormat('% d');
        $l3plot->value->hideZero();
        $l3plot->value->show();

        //conversion rate graph
        $l1datay = array();
        for ($iCtr = 0; $iCtr < count($aDataFinalX); $iCtr++) {
            if ($aDataFinalX[$iCtr] != 0 && $aDataFinalX2[$iCtr] != 0) {
                $l1datay[] = 100 / ($aDataFinalX[$iCtr] / $aDataFinalX2[$iCtr]);
            } else {
                $l1datay[] = 0;
            }
        }

        $l1plot = new LinePlot($l1datay);
        $l1plot->setColor("red");
        $l1plot->setWeight(2);
        $l1plot->setLegend("Conversion rate (%)");
        $l1plot->value->setColor('red');
        $l1plot->value->setFormat('% 0.2f%%');
        $l1plot->value->hideZero();
        $l1plot->value->show();

        // Create the grouped bar plot1
        $graph->addY2($l1plot);
        $graph->add($l2plot);
        $graph->add($l3plot);

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
        $aDataY = array();

        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"))));
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_from"))));

        $query = "select oxtime, count(*) as nrof from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid order by oxtime";
        $aTemp = array();
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                //$aTemp[date( "W", strtotime( $rs->fields[0]))]++;
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
            $aDataY[] = "KW " . $i;
        }

        foreach ($aTemp as $key => $value) {
            $aDataX[$key] = $value;
            $aDataX2[$key] = 0;
            $aDataX3[$key] = 0;
            $aDataY[] = "KW " . $key;
        }

        // buyer
        $query = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $sKey = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($config->getConfigParam('iFirstWeekDay'), strtotime($resultSet->getFields()[0]));
                if (isset($aDataX2[$sKey])) {
                    $aDataX2[$sKey]++;
                }
                $resultSet->fetchRow();
            }
        }

        // newcustomer
        $query = "select oxtime, oxsessid from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid order by oxtime";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                $sKey = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($config->getConfigParam('iFirstWeekDay'), strtotime($resultSet->getFields()[0]));
                if (isset($aDataX3[$sKey])) {
                    $aDataX3[$sKey]++;
                }
                $resultSet->fetchRow();
            }
        }
        
        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = new Graph(max(800, count($aDataX) * 80), 600);

        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $graph->setBackgroundImage($backgroundImage, BGIMG_FILLFRAME);

        // Use a "text" X-scale
        $graph->setScale("textlin");
        $graph->setY2Scale("lin");
        $graph->y2axis->setColor("red");

        // Label align for X-axis
        $graph->xaxis->setLabelAlign('center', 'top', 'right');

        // Label align for Y-axis
        $graph->yaxis->setLabelAlign('right', 'bottom');

        $graph->setShadow();
        // Description
        $graph->xaxis->setTickLabels($aDataY);


        // Set title and subtitle
        $graph->title->set("Woche");

        // Use built in font
        $graph->title->setFont(FF_FONT1, FS_BOLD);

        $aDataFinalX = array();
        foreach ($aDataX as $dData) {
            $aDataFinalX[] = $dData;
        }

        // Create the bar plot
        $l2plot = new LinePlot($aDataFinalX);
        $l2plot->setColor("navy");
        $l2plot->setWeight(2);
        $l2plot->setLegend("Besucher");
        $l2plot->value->setColor("navy");
        $l2plot->value->setFormat('% d');
        $l2plot->value->hideZero();
        $l2plot->value->show();

        $aDataFinalX2 = array();
        foreach ($aDataX2 as $dData) {
            $aDataFinalX2[] = $dData;
        }

        // Create the bar plot
        $l3plot = new LinePlot($aDataFinalX2);
        $l3plot->setColor("orange");
        $l3plot->setWeight(2);
        $l3plot->setLegend("Bestellungen");
        //$l1plot->SetBarCenter();
        $l3plot->value->setColor("orange");
        $l3plot->value->setFormat('% d');
        $l3plot->value->hideZero();
        $l3plot->value->show();

        //conversion rate graph
        $l1datay = array();
        for ($iCtr = 0; $iCtr < count($aDataFinalX); $iCtr++) {
            if ($aDataFinalX[$iCtr] != 0 && $aDataFinalX2[$iCtr] != 0) {
                $l1datay[] = 100 / ($aDataFinalX[$iCtr] / $aDataFinalX2[$iCtr]);
            } else {
                $l1datay[] = 0;
            }
        }
        $l1plot = new LinePlot($l1datay);
        $l1plot->setColor("red");
        $l1plot->setWeight(2);
        $l1plot->setLegend("Conversion rate (%)");
        $l1plot->value->setColor('red');
        $l1plot->value->setFormat('% 0.4f%%');
        $l1plot->value->hideZero();
        $l1plot->value->show();

        // Create the grouped bar plot
        $graph->addY2($l1plot);
        $graph->add($l2plot);
        $graph->add($l3plot);

        // Finally output the  image
        $graph->stroke();
    }
}
