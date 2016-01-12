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
 * Tests for Statistic_List class
 */
class OeStatisticsListTest extends OxidTestCase
{

    /**
     * Statistic_List::Render() test case
     */
    public function testRender()
    {
        $oView = $this->getProxyClass("OeStatistics_List");
        $this->assertEquals(false, $oView->getNonPublicVar("_blDesc"));
        $this->assertEquals("oeStatisticsManager", $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals('oestatistics_list.tpl', $oView->render());
    }
}
