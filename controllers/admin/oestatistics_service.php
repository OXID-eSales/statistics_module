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
 * Admin statistics service setting manager.
 * Collects statistics service settings, updates it on user submit, etc.
 * Admin Menu: Statistics -> Show -> Clear Log.
 */
class OeStatistics_Service extends oxAdminDetails
{
    /**
     * Executes parent method parent::render() and returns name of template
     * file "statistic_service.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $query = "select count(*) from oestatisticslog where oxshopid = '" . $this->getConfig()->getShopId() . "'";
        $this->_aViewData['iLogCount'] = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($query, false, false);

        return "oestatistics_service.tpl";
    }

    /**
     * Performs cleanup of statistic data for selected period.
     */
    public function cleanup()
    {
        $timeFrame = oxRegistry::getConfig()->getRequestParameter("timeframe");
        $currentTime = time();
        $iTimestamp = mktime(
            date("H", $currentTime),
            date("i", $currentTime),
            date("s", $currentTime),
            date("m", $currentTime),
            date("d", $currentTime) - $timeFrame,
            date("Y", $currentTime)
        );
        $deleteFrom = date("Y-m-d H:i:s", $iTimestamp);

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->Execute("delete from oestatisticslog where oxtime < " . $database->quote($deleteFrom));
    }
}
