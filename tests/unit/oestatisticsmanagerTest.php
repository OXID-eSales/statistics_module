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

class OeStatisticsManagerTest extends OxidTestCase
{
    /**
     * Testing if deletion removes all db records
     */
    public function testSetGetReports()
    {
        $oStatistic = oxNew("oeStatisticsManager");
        $oStatistic->setReports("aaaa");
        $this->assertEquals("aaaa", $oStatistic->getReports());
    }

    /**
     * Testing oxvalue field setter
     */
    public function testSetFieldData()
    {
        $sValue = '"\"&\'';

        $oStatistic = oxNew("oeStatisticsManager");
        $oStatistic->UNITsetFieldData('oxvalue', $sValue);
        $oStatistic->UNITsetFieldData('oxsomefield', $sValue);

        $this->assertEquals($sValue, $oStatistic->oestatistics__oxvalue->value);
        $this->assertEquals(htmlentities($sValue, ENT_QUOTES), $oStatistic->oestatistics__oxsomefield->value);
    }

    public function testOeStatisticsSaveAndLoad()
    {
        $value = 'agent??????für';
        $fields = array('oestatistics__oxtitle', 'oestatistics__oxvalue');

        $statistics = oxNew('oeStatisticsManager');
        $statistics->setId('_testStat');
        foreach ($fields as $fieldName) {
            $statistics->{$fieldName} = new oxField($value);
        }
        $statistics->save();

        $statistics = oxNew('oeStatisticsManager');
        $statistics->load('_testStat');

        foreach ($fields as $fieldName) {
            $this->assertTrue(strcmp($statistics->{$fieldName}->value, $value) === 0, "$fieldName (" . $statistics->{$fieldName}->value . ")");
        }
    }
}
