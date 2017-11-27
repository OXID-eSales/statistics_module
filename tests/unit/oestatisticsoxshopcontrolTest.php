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

class OeStatisticsOxShopControlTest extends OxidTestCase
{
    /**
     * Testing oxShopControl::_log()
     */
    public function testLog()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $this->setSessionParam("actshop", "testshopid");
        $this->setSessionParam("usr", "testusr");

        $this->setRequestParameter("cnid", "testcnid");
        $this->setRequestParameter("aid", "testaid");
        $this->setRequestParameter("tpl", "testtpl.tpl");
        $this->setRequestParameter("searchparam", "testsearchparam");

        $this->assertEquals(0, $oDb->getOne("select count(*) from oestatisticslog"));

        $oControl = oxNew('oxShopControl');
        $oControl->oeStatisticsLog('content', 'testFnc1');
        $oControl->oeStatisticsLog('search', 'testFnc2');

        $this->assertEquals(2, $oDb->getOne("select count(*) from oestatisticslog"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oestatisticslog where oxclass='content' and oxparameter='testtpl'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oestatisticslog where oxclass='search' and oxparameter='testsearchparam'"));
    }
}
