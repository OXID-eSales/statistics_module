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
 * Shop visitor reports class
 */
class OeStatistics_Report_Visitor_Absolute extends OeStatistics_Report_Base
{
    /** @var string Name of template to render. */
    protected $_sThisTemplate = "oestatistics_report_visitor_absolute.tpl";

    /**
     * Checks if db contains data for report generation
     *
     * @return bool
     */
    public function drawReport()
    {
        $database = oxDb::getDb();

        $oSmarty = $this->getSmarty();
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime($oSmarty->_tpl_vars['time_from'])));
        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime($oSmarty->_tpl_vars['time_to'])));

        if ($database->getOne("select 1 from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo")) {
            return true;
        }

        // buyer
        if ($database->getOne("select 1 from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo")) {
            return true;
        }

        // newcustomer
        if ($database->getOne("select 1 from oxuser where oxcreate >= $sTimeFrom and oxcreate <= $sTimeTo")) {
            return true;
        }
    }

    /**
     * Collects and renders visitor/month report data
     */
    public function visitor_month()
    {
        $database = oxDb::getDb();

        $aDataX = array();
        $aDataY = array();

        $dTimeTo = strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"));
        $dTimeFrom = mktime(23, 59, 59, date("m", $dTimeTo) - 12, date("d", $dTimeTo), date("Y", $dTimeTo));

        $sTimeTo = $database->quote(date("Y-m-d H:i:s", $dTimeTo));
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", $dTimeFrom));

        $query = "select oxtime, count(*) as nrof from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTemp = array();
        for ($i = 1; $i <= 12; $i++) {
            $aTemp[date("m/Y", mktime(23, 59, 59, date("m", $dTimeFrom) + $i, date("d", $dTimeFrom), date("Y", $dTimeFrom)))] = 0;
        }

        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $aTemp[date("m/Y", strtotime($result->fields[0]))]++;
                $result->moveNext();
            }
        }

        foreach ($aTemp as $key => $value) {
            $aDataX[$key] = $value;
            $aDataX2[$key] = 0;
            $aDataX3[$key] = 0;
            $aDataY[] = $key;
        }

        // buyer
        $query = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $aTemp = array();
        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $aTemp[date("m/Y", strtotime($result->fields[0]))]++;
                $result->moveNext();
            }
        }

        foreach ($aTemp as $key => $value) {
            $aDataX2[$key] = $value;
        }

        // newcustomer
        $query = "select oxcreate from oxuser where oxcreate >= $sTimeFrom and oxcreate <= $sTimeTo order by oxcreate";
        $aTemp = array();
        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $aTemp[date("m/Y", strtotime($result->fields[0]))]++;
                $result->moveNext();
            }
        }

        foreach ($aTemp as $key => $value) {
            $aDataX3[$key] = $value;
        }


        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = new Graph(800, 600);

        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $graph->setBackgroundImage($backgroundImage, BGIMG_FILLFRAME);

        // Use a "text" X-scale
        $graph->setScale("textlin");

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
        $bplot = new BarPlot($aDataFinalX);
        $bplot->setFillGradient("navy", "lightsteelblue", GRAD_VER);
        $bplot->setLegend("Besucher");

        $aDataFinalX2 = array();
        foreach ($aDataX2 as $dData) {
            $aDataFinalX2[] = $dData;
        }
        // Create the bar plot
        $bplot2 = new BarPlot($aDataFinalX2);
        $bplot2->setFillColor("orange");
        $bplot2->setLegend("Kaeufer");

        $aDataFinalX3 = array();
        foreach ($aDataX3 as $dData) {
            $aDataFinalX3[] = $dData;
        }
        // Create the bar plot
        $bplot3 = new BarPlot($aDataFinalX3);
        $bplot3->setFillColor("silver");
        $bplot3->setLegend("Neukunden");

        // Create the grouped bar plot
        $gbplot = new groupBarPlot(array($bplot, $bplot2, $bplot3));
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
        $database = oxDb::getDb();

        $aDataX = array();
        $aDataY = array();

        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"))));
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_from"))));

        $query = "select oxtime, count(*) as nrof from oestatisticslog where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid order by oxtime";
        $aTemp = array();
        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $firstWeekDay = $config->getConfigParam('iFirstWeekDay');
                $aTemp[oxRegistry::get("oxUtilsDate")->getWeekNumber($firstWeekDay, strtotime($result->fields[0]))]++;
                $result->moveNext();
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
        $aTemp = array();
        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $firstWeekDay = $config->getConfigParam('iFirstWeekDay');
                $aTemp[oxRegistry::get("oxUtilsDate")->getWeekNumber($firstWeekDay, strtotime($result->fields[0]))]++;
                $result->moveNext();
            }
        }

        foreach ($aTemp as $key => $value) {
            $aDataX2[$key] = $value;
        }

        // newcustomer
        $query = "select oxcreate from oxuser where oxcreate >= $sTimeFrom and oxcreate <= $sTimeTo order by oxcreate";
        $aTemp = array();
        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $firstWeekDay = $config->getConfigParam('iFirstWeekDay');
                $aTemp[oxRegistry::get("oxUtilsDate")->getWeekNumber($firstWeekDay, strtotime($result->fields[0]))]++;
                $result->moveNext();
            }
        }

        foreach ($aTemp as $key => $value) {
            $aDataX3[$key] = $value;
        }

        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = new Graph(max(800, count($aDataX) * 80), 600);

        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $graph->setBackgroundImage($backgroundImage, BGIMG_FILLFRAME);

        // Use a "text" X-scale
        $graph->setScale("textlin");

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
        $bplot = new BarPlot($aDataFinalX);
        $bplot->setFillGradient("navy", "lightsteelblue", GRAD_VER);
        $bplot->setLegend("Besucher");

        $aDataFinalX2 = array();
        foreach ($aDataX2 as $dData) {
            $aDataFinalX2[] = $dData;
        }
        // Create the bar plot
        $bplot2 = new BarPlot($aDataFinalX2);
        $bplot2->setFillColor("orange");
        $bplot2->setLegend("Kaeufer");

        $aDataFinalX3 = array();
        foreach ($aDataX3 as $dData) {
            $aDataFinalX3[] = $dData;
        }
        // Create the bar plot
        $bplot3 = new BarPlot($aDataFinalX3);
        $bplot3->setFillColor("silver");
        $bplot3->setLegend("Neukunden");

        // Create the grouped bar plot
        $gbplot = new groupBarPlot(array($bplot, $bplot2, $bplot3));
        $graph->add($gbplot);

        // Finally output the  image
        $graph->stroke();
    }
}
