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
 * Top viewed products reports class
 */
class OeStatistics_Report_Top_Viewed_Products extends OeStatistics_Report_Base
{
    /** @var string Name of template to render. */
    protected $_sThisTemplate = "oestatistics_report_top_viewed_products.tpl";

    /**
     * Current month top viewed products report
     *
     * @return string
     */
    public function render()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $aDataX = array();
        $aDataY = array();

        $smarty = $this->getSmarty();
        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime($smarty->_tpl_vars['time_from'])));
        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime($smarty->_tpl_vars['time_to'])));

        $query = "select count(*) as nrof, oxarticles.oxtitle from oestatisticslog, oxarticles " .
                "where oestatisticslog.oxclass = 'details' and oestatisticslog.oxanid = oxarticles.oxid  " .
                "and oestatisticslog.oxtime >= $sTimeFrom and oestatisticslog.oxtime <= $sTimeTo group by oestatisticslog.oxanid " .
                "order by nrof desc limit 0, 25";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                if ($resultSet->getFields()[1]) {
                    $aDataX[] = $resultSet->getFields()[0];
                    $aDataY[] = $resultSet->getFields()[1];
                }
                $resultSet->fetchRow();
            }
        }
        $iMax = 0;
        for ($iCtr = 0; $iCtr < count($aDataX); $iCtr++) {
            if ($iMax < $aDataX[$iCtr]) {
                $iMax = $aDataX[$iCtr];
            }
        }

        $aPoints = array();
        $aPoints[0] = 0;
        $aAligns[0] = 'report_searchstrings_scale_aligns_left"';
        $iTenth = strlen($iMax) - 1;
        if ($iTenth < 1) {
            $iScaleMax = $iMax;
            $aPoints[(round(($iMax / 2)))] = $iMax / 2;
            $aAligns[(round(($iMax / 2)))] = 'report_searchstrings_scale_aligns_center" width="' . (720 / 3) . '"';
            $aPoints[$iMax] = $iMax;
            $aAligns[$iMax] = 'report_searchstrings_scale_aligns_right" width="' . (720 / 3) . '"';
        } else {
            $iDeg = bcpow(10, $iTenth);
            //$iScaleMax = $iDeg * (round($iMax/$iDeg));
            $iScaleMax = $iMax;
            $ctr = 0;
            for ($iCtr = 10; $iCtr > 0; $iCtr--) {
                $aPoints[(round(($ctr)))] = $ctr += $iScaleMax / 10;
                $aAligns[(round(($ctr)))] = 'report_searchstrings_scale_aligns_center" width="' . (720 / 10) . '"';
            }
            $aAligns[(round(($ctr)))] = 'report_searchstrings_scale_aligns_right" width="' . (720 / 10) . '"';
        }

        $aAligns[0] .= ' width="' . (720 / count($aAligns)) . '"';

        $aDataVals = array();

        for ($iCtr = 0; $iCtr < count($aDataY); $iCtr++) {
            $aDataVals[$iCtr]['pc'] = round($aDataX[$iCtr] / $iMax * 100);
            $aDataVals[$iCtr]['nm'] = $aDataY[$iCtr];
        }

        if (count($aDataY) > 0) {
            $smarty->assign("drawStat", true);
        } else {
            $smarty->assign("drawStat", false);
        }

        $smarty->assign("classes", array($aAligns));
        $smarty->assign("allCols", count($aAligns));
        $smarty->assign("cols", count($aAligns));
        $smarty->assign("percents", array($aDataVals));
        $smarty->assign("y", $aDataY);

        return parent::render();
    }

    /**
     * Current week top viewed products report
     */
    public function graph1()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $aDataX = array();
        $aDataY = array();

        $sTimeFrom = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_from"))));
        $sTimeTo = $database->quote(date("Y-m-d H:i:s", strtotime(oxRegistry::getConfig()->getRequestParameter("time_to"))));

        $query = "select count(*) as nrof, oxparameter from oestatisticslog where oxclass = 'search' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxparameter order by nrof desc";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query);
        if ($resultSet != false && $resultSet->count() > 0) {
            while (!$resultSet->EOF) {
                if ($resultSet->getFields()[1]) {
                    $aDataX[] = $resultSet->getFields()[0];
                    $aDataY[] = $resultSet->getFields()[1];
                }
                $resultSet->fetchRow();
            }
        }
        header("Content-type: image/png");

        // New graph with a drop shadow
        $graph = new Graph(800, max(640, 20 * count($aDataX)));
        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $graph->setBackgroundImage($backgroundImage, BGIMG_FILLFRAME);

        // Use a "text" X-scale
        $graph->setScale("textlin");

        $top = 60;
        $bottom = 30;
        $left = 80;
        $right = 30;
        $graph->set90AndMargin($left, $right, $top, $bottom);

        // Label align for X-axis
        $graph->xaxis->setLabelAlign('right', 'center', 'right');

        // Label align for Y-axis
        $graph->yaxis->setLabelAlign('center', 'bottom');

        $graph->setShadow();
        // Description
        $graph->xaxis->setTickLabels($aDataY);
        //print_r($aDataY);


        // Set title and subtitle
        $graph->title->set("Suchw�rter");

        // Use built in font
        $graph->title->setFont(FF_FONT1, FS_BOLD);

        // Create the bar plot
        $bplot = new BarPlot($aDataX);
        $bplot->setFillGradient("navy", "lightsteelblue", GRAD_VER);
        $bplot->setLegend("Hits");

        $graph->add($bplot);

        // Finally output the  image
        $graph->stroke();
    }
}
