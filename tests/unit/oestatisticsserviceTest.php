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
 * Tests for Statistic_Service class
 */
class OeStatisticsServiceTest extends OxidTestCase
{

    /**
     * Statistic_Main::Render() test case
     */
    public function testRender()
    {
        $oView = $this->getProxyClass("OeStatistics_Service");

        $this->assertEquals('oestatistics_service.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['iLogCount']));
    }

    /**
     * Statistic_Service::cleanup() test case
     */
    public function testCleanup()
    {
        $iTimeFrame = "62";
        $this->setRequestParameter("timeframe", $iTimeFrame);
        $dNow = time();
        $sInsertFrom = date("Y-m-d H:i:s", mktime(date("H", $dNow), date("i", $dNow), date("s", $dNow), date("m", $dNow), date("d", $dNow) - 186, date("Y", $dNow)));
        $sDeleteFrom = date("Y-m-d H:i:s", mktime(date("H", $dNow), date("i", $dNow), date("s", $dNow), date("m", $dNow), date("d", $dNow) - $iTimeFrame, date("Y", $dNow)));
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oestatisticslog (oxtime) value (" . $oDb->quote($sInsertFrom) . ")");
        $iCnt = $oDb->getOne("select count(*) from oestatisticslog where oxtime < " . $oDb->quote($sDeleteFrom));

        $oView = oxNew('OeStatistics_Service');
        $oView->cleanup();

        $oDb = oxDb::getDb();
        $iCnt = $oDb->getOne("select count(*) from oestatisticslog where oxtime < " . $oDb->quote($sDeleteFrom));
        $this->assertEquals(0, $iCnt);
    }
}
