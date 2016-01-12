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
 * Admin article main statistic manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Statistics -> Show -> Main.
 */
class OeStatistics_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oeStatisticsManager object,
     * passes it's data to Smarty engine and returns name of template file
     * "oestatistics_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $language = oxRegistry::getLang();
        parent::render();

        $statisticId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        $reports = array();
        if (isset($statisticId) && $statisticId != "-1") {
            // load object
            $manager = oxNew("oeStatisticsManager");
            $manager->load($statisticId);

            $reports = $manager->getReports();
            $this->_aViewData["edit"] = $manager;
        }

        // setting all reports data: check for reports and load them
        $languageId = (int) oxRegistry::getConfig()->getRequestParameter("editlanguage");
        $allReports = array();

        $availableReports = glob(__DIR__ . "/Report/*.php");
        foreach ($availableReports as $filePath) {
            if (is_file($filePath) && !is_dir($filePath)) {
                $sConst = strtoupper(str_replace('.php', '', basename($filePath)));

                // skipping base report class
                if ($sConst == 'OESTATISTICS_REPORT_BASE') {
                    continue;
                }

                include_once $filePath;

                $item = new stdClass();
                $item->filename = basename($filePath);
                $item->name = $language->translateString($sConst, $languageId);
                $allReports[] = $item;
            }
        }

        // setting reports data
        oxRegistry::getSession()->setVariable("allstat_reports", $allReports);
        oxRegistry::getSession()->setVariable("stat_reports_$statisticId", $reports);

        // passing assigned reports count
        if (is_array($reports)) {
            $this->_aViewData['ireports'] = count($reports);
        }

        if (oxRegistry::getConfig()->getRequestParameter("aoc")) {
            $statisticMainAjax = oxNew('oeStatistics_Main_Ajax');
            $this->_aViewData['oxajax'] = $statisticMainAjax->getColumns();

            return "popups/oestatistics_main.tpl";
        }

        return "oestatistics_main.tpl";
    }

    /**
     * Saves statistic parameters changes.
     */
    public function save()
    {
        $statisticId = $this->getEditObjectId();
        $parameters = oxRegistry::getConfig()->getRequestParameter("editval");

        $shopID = oxRegistry::getSession()->getVariable("actshop");
        $manager = oxNew("oeStatisticsManager");
        if ($statisticId != "-1") {
            $manager->load($statisticId);
        } else {
            $parameters['oestatistics__oxid'] = null;
        }

        $parameters['oestatistics__oxshopid'] = $shopID;
        $manager->assign($parameters);
        $manager->save();

        // set oxid if inserted
        $this->setEditObjectId($manager->getId());
    }

    /**
     * Performs report generation function (outputs Smarty generated HTML report).
     */
    public function generate()
    {
        $config = $this->getConfig();

        $statisticId = $this->getEditObjectId();

        // load object
        $manager = oxNew("oeStatisticsManager");
        $manager->load($statisticId);

        $allReports = $manager->getReports();

        $shop = oxNew("oxShop");
        $shop->load($config->getShopId());
        $this->addGlobalParams($shop);

        $timeFrom = oxRegistry::getConfig()->getRequestParameter("time_from");
        $timeTo = oxRegistry::getConfig()->getRequestParameter("time_to");
        if ($timeFrom && $timeTo) {
            $timeFrom = oxRegistry::get("oxUtilsDate")->formatDBDate($timeFrom, true);
            $timeFrom = date("Y-m-d", strtotime($timeFrom));
            $timeTo = oxRegistry::get("oxUtilsDate")->formatDBDate($timeTo, true);
            $timeTo = date("Y-m-d", strtotime($timeTo));
        } else {
            $days = oxRegistry::getConfig()->getRequestParameter("timeframe");
            $now = time();
            $timeFrom = date("Y-m-d", mktime(0, 0, 0, date("m", $now), date("d", $now) - $days, date("Y", $now)));
            $timeTo = date("Y-m-d", time());
        }

        $smarty = oxRegistry::get("oxUtilsView")->getSmarty();
        $smarty->assign("time_from", $timeFrom . " 23:59:59");
        $smarty->assign("time_to", $timeTo . " 23:59:59");
        $smarty->assign("oViewConf", $this->_aViewData["oViewConf"]);

        echo($smarty->fetch("oestatistics_report_pagehead.tpl"));
        foreach ($allReports as $file) {
            if (($file = trim($file))) {
                $className = str_replace(".php", "", strtolower($file));

                $report = oxNew($className);
                $report->setSmarty($smarty);

                $smarty->assign("oView", $report);
                echo($smarty->fetch($report->render()));
            }
        }

        oxRegistry::getUtils()->showMessageAndExit($smarty->fetch("oestatistics_report_bottomitem.tpl"));
    }
}
