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
 * User per group reports class
 */
class OeStatistics_Report_User_Per_Group extends OeStatistics_Report_Base
{
    /** @var string Name of template to render. */
    protected $_sThisTemplate = "oestatistics_report_user_per_group.tpl";

    /**
     * Checks if db contains data for report generation
     *
     * @return bool
     */
    public function drawReport()
    {
        $query = "SELECT 1 FROM oxobject2group, oxuser, oxgroups
           WHERE oxobject2group.oxobjectid = oxuser.oxid AND
           oxobject2group.oxgroupsid = oxgroups.oxid";

        return oxDb::getDb()->getOne($query);
    }

    /**
     * Collects and renders user per group report data
     */
    public function user_per_group()
    {
        $database = oxDb::getDb();

        $aDataX = array();
        $aDataY = array();

        $query = "SELECT oxgroups.oxtitle,
                    count(oxuser.oxid)
             FROM oxobject2group,
                  oxuser,
                  oxgroups
             WHERE oxobject2group.oxobjectid = oxuser.oxid  AND
                   oxobject2group.oxgroupsid = oxgroups.oxid
             GROUP BY oxobject2group.oxgroupsid
             ORDER BY oxobject2group.oxgroupsid";

        $result = $database->execute($query);
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                if ($result->fields[1]) {
                    $aDataX[] = $result->fields[1];
                    $aDataY[] = $result->fields[0];
                }
                $result->moveNext();
            }
        }

        header("Content-type: image/png");

        // New graph with a drop shadow
        if (count($aDataX) > 10) {
            $graph = new PieGraph(800, 830);
        } else {
            $graph = new PieGraph(600, 600);
        }

        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $graph->setBackgroundImage($backgroundImage, BGIMG_FILLFRAME);
        $graph->setShadow();

        // Set title and subtitle
        //$graph->title->set($this->aTitles[$myConfig->getConfigParam( 'iAdminLanguage' ) ]);
        $graph->title->set($this->aTitles[oxRegistry::getLang()->getObjectTplLanguage()]);

        // Use built in font
        $graph->title->setFont(FF_FONT1, FS_BOLD);

        // Create the bar plot
        $bplot = new PiePlot3D($aDataX);

        $bplot->setSize(0.4);
        $bplot->setCenter(0.5, 0.32);

        // explodes all chunks of Pie from center point
        $bplot->explodeAll(10);
        $iUserCount = 0;
        foreach ($aDataX as $iVal) {
            $iUserCount += $iVal;
        }
        for ($iCtr = 0; $iCtr < count($aDataX); $iCtr++) {
            $iSLeng = strlen($aDataY[$iCtr]);
            if ($iSLeng > 20) {
                if ($iSLeng > 23) {
                    $aDataY[$iCtr] = trim(substr($aDataY[$iCtr], 0, 20)) . "...";
                }

            }
            $aDataY[$iCtr] .= " - " . $aDataX[$iCtr] . " Kund.";
        }
        $bplot->setLegends($aDataY);

        if (count($aDataX) > 10) {
            $graph->legend->pos(0.49, 0.66, 'center');
            $graph->legend->setFont(FF_FONT0, FS_NORMAL);
            $graph->legend->setColumns(4);
        } else {
            $graph->legend->pos(0.49, 0.70, 'center');
            $graph->legend->setFont(FF_FONT1, FS_NORMAL);
            $graph->legend->setColumns(2);
        }

        $graph->add($bplot);

        // Finally output the  image
        $graph->stroke();
    }
}
