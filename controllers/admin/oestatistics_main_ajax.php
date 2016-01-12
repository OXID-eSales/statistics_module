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
 * Class manages statistics configuration
 */
class OeStatistics_Main_Ajax extends ajaxListComponent
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        // field , table, visible, multilanguage, ident
        'container1' => array(
            array('oxtitle', 'oxstat', 1, 0, 0),
            array('oxid', 'oxstat', 0, 0, 1)
        ),
         'container2' => array(
             array('oxtitle', 'oxstat', 1, 0, 0),
             array('oxid', 'oxstat', 0, 0, 1)
         )
    );

    /**
     * Formats and returns statiistics configuration related data array for ajax response
     *
     * @param string $sCountQ this param currently is not used as this method overrides default function behaviour
     * @param string $sQ      this param currently is not used as this method overrides default function behaviour
     *
     * @return array
     */
    protected function _getData($sCountQ, $sQ)
    {
        $response['startIndex'] = $this->_getStartIndex();
        $response['sort'] = '_' . $this->_getSortCol();
        $response['dir'] = $this->_getSortDir();

        // all possible reports
        $reports = oxRegistry::getSession()->getVariable("allstat_reports");
        $syncId = oxRegistry::getConfig()->getRequestParameter("synchoxid");
        $oxId = oxRegistry::getConfig()->getRequestParameter("oxid");

        $statisticId = $syncId ? $syncId : $oxId;
        $manager = oxNew('oeStatisticsManager');
        $manager->load($statisticId);
        $statisticData = unserialize($manager->oestatistics__oxvalue->value);

        $data = array();
        $count = 0;
        $str = getStr();

        // filter data
        $filters = oxRegistry::getConfig()->getRequestParameter("aFilter");
        $filter = (is_array($filters) && isset($filters['_0'])) ? $str->preg_replace('/^\*/', '%', $filters['_0']) : null;

        foreach ($reports as $report) {
            if ($syncId) {
                if (is_array($statisticData) && in_array($report->filename, $statisticData)) {
                    continue;
                }
            } else {
                if (!is_array($statisticData) || !in_array($report->filename, $statisticData)) {
                    continue;
                }
            }

            // checking filter
            if ($filter && !$str->preg_match("/^" . preg_quote($filter) . "/i", $report->name)) {
                continue;
            }

            $data[$count]['_0'] = $report->name;
            $data[$count]['_1'] = $report->filename;
            $count++;
        }

        if (oxRegistry::getConfig()->getRequestParameter("dir")) {
            if ('asc' == oxRegistry::getConfig()->getRequestParameter("dir")) {
                usort($data, array($this, "sortAsc"));
            } else {
                usort($data, array($this, "sortDesc"));
            }
        } else {
            usort($data, array($this, "sortAsc"));
        }

        $response['records'] = $data;
        $response['totalRecords'] = count($reports);

        return $response;
    }

    /**
     * Callback function used to apply ASC sorting
     *
     * @param array $first  first item to check sorting
     * @param array $second second item to check sorting
     *
     * @return int
     */
    public function sortAsc($first, $second)
    {
        if ($first['_0'] == $second['_0']) {
            return 0;
        }

        return ($first['_0'] < $second['_0']) ? -1 : 1;
    }

    /**
     * Callback function used to apply ASC sorting
     *
     * @param array $first  first item to check sorting
     * @param array $second second item to check sorting
     *
     * @return int
     *
     */
    public function sortDesc($first, $second)
    {
        if ($first['_0'] == $second['_0']) {
            return 0;
        }

        return ($first['_0'] > $second['_0']) ? -1 : 1;
    }


    /**
     * Removes selected report(s) from generating list.
     */
    public function removeReportFromList()
    {
        $reports = oxRegistry::getSession()->getVariable("allstat_reports");
        $statisticId = oxRegistry::getConfig()->getRequestParameter('oxid');

        // assigning all items
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $statistics = array();
            foreach ($reports as $report) {
                $statistics[] = $report->filename;
            }
        } else {
            $statistics = $this->_getActionIds('oxstat.oxid');
        }

        $manager = oxNew('oeStatisticsManager');
        if (is_array($statistics) && $manager->load($statisticId)) {
            $statisticsData = $manager->getReports();

            // additional check
            foreach ($reports as $report) {
                if (in_array($report->filename, $statistics) && ($iPos = array_search($report->filename, $statisticsData)) !== false) {
                    unset($statisticsData[$iPos]);
                }
            }

            $manager->setReports($statisticsData);
            $manager->save();
        }
    }

    /**
     * Adds selected report(s) to generating list.
     */
    public function addReportToList()
    {
        $reports = oxRegistry::getSession()->getVariable("allstat_reports");
        $statisticId = oxRegistry::getConfig()->getRequestParameter('synchoxid');

        // assigning all items
        if (oxRegistry::getConfig()->getRequestParameter('all')) {
            $statistics = array();
            foreach ($reports as $report) {
                $statistics[] = $report->filename;
            }
        } else {
            $statistics = $this->_getActionIds('oxstat.oxid');
        }

        $manager = oxNew('oeStatisticsManager');
        if ($manager->load($statisticId)) {
            $statisticsData = (array) $manager->getReports();


            // additional check
            foreach ($reports as $report) {
                if (in_array($report->filename, $statistics) && !in_array($report->filename, $statisticsData)) {
                    $statisticsData[] = $report->filename;
                }
            }

            $manager->setReports($statisticsData);
            $manager->save();
        }
    }
}
