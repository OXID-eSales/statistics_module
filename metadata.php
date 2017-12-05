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
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'oestatistics',
    'title'       => array(
        'de' => 'OE Statistiken',
        'en' => 'OE Statistics',
    ),
    'description' => array(
        'de' => 'Ein Modul für das Loggen und die Ausgabe von Statistiken in OXID eShop Community und Professional Edition',
        'en' => 'A module for logging and displaying statistics for OXID eShop Community and Professional Edition',
    ),
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '2.0.1',
    'author'      => 'OXID eSales AG',
    'url'         => 'http://www.oxid-esales.com/',
    'extend'      => array(
        'oxShopControl' => 'oe/statistics/core/oestatisticsoxshopcontrol'
    ),
    'files'       => array(
        'OeStatistics' => 'oe/statistics/controllers/admin/oestatistics.php',
        'oeStatisticsManager' => 'oe/statistics/models/oestatisticsmanager.php',
        'OeStatistics_List' => 'oe/statistics/controllers/admin/oestatistics_list.php',
        'OeStatistics_Main' => 'oe/statistics/controllers/admin/oestatistics_main.php',
        'OeStatistics_Main_Ajax' => 'oe/statistics/controllers/admin/oestatistics_main_ajax.php',
        'OeStatistics_Service' => 'oe/statistics/controllers/admin/oestatistics_service.php',
        'OeStatistics_Report_Base' => 'oe/statistics/controllers/admin/Report/oestatistics_report_base.php',
        'OeStatistics_Report_Canceled_Orders' => 'oe/statistics/controllers/admin/Report/oestatistics_report_canceled_orders.php',
        'OeStatistics_Report_Conversion_Rate' => 'oe/statistics/controllers/admin/Report/oestatistics_report_conversion_rate.php',
        'OeStatistics_Report_SearchStrings' => 'oe/statistics/controllers/admin/Report/oestatistics_report_searchstrings.php',
        'OeStatistics_Report_Top_Clicked_Categories' => 'oe/statistics/controllers/admin/Report/oestatistics_report_top_clicked_categories.php',
        'OeStatistics_Report_Top_Viewed_Products' => 'oe/statistics/controllers/admin/Report/oestatistics_report_top_viewed_products.php',
        'OeStatistics_Report_User_Per_Group' => 'oe/statistics/controllers/admin/Report/oestatistics_report_user_per_group.php',
        'OeStatistics_Report_Visitor_Absolute' => 'oe/statistics/controllers/admin/Report/oestatistics_report_visitor_absolute.php',
        'oestatisticsmodule' => 'oe/statistics/core/oestatisticsmodule.php',
    ),
    'templates'   => array(
        'oestatistics.tpl' => 'oe/statistics/views/admin/tpl/oestatistics.tpl',
        'oestatistics_list.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_list.tpl',
        'oestatistics_main.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_main.tpl',
        'oestatistics_service.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_service.tpl',
        'oestatistics_report_bottomitem.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_bottomitem.tpl',
        'oestatistics_report_canceled_orders.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_canceled_orders.tpl',
        'oestatistics_report_conversion_rate.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_conversion_rate.tpl',
        'oestatistics_report_pagehead.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_pagehead.tpl',
        'oestatistics_report_searchstrings.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_searchstrings.tpl',
        'oestatistics_report_top_clicked_categories.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_top_clicked_categories.tpl',
        'oestatistics_report_top_viewed_products.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_top_viewed_products.tpl',
        'oestatistics_report_user_per_group.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_user_per_group.tpl',
        'oestatistics_report_visitor_absolute.tpl' => 'oe/statistics/views/admin/tpl/oestatistics_report_visitor_absolute.tpl',
        'popups/oestatistics_main.tpl' => 'oe/statistics/views/admin/tpl/popups/oestatistics_main.tpl',
    ),
    'blocks'      => array(
        array('template' => 'bottomnaviitem.tpl', 'block'=>'admin_bottomnavicustom', 'file'=>'/views/blocks/bottomnavicustom.tpl'),
    ),
    'settings'    => array(
        array('group' => 'oestatistics', 'name' => 'blOEStatisticsLogging', 'type' => 'bool', 'value' => 'false'),
    ),
    'events'      => array(
        'onActivate'   => 'oeStatisticsModule::onActivate',
        'onDeactivate' => 'oeStatisticsModule::onDeactivate',
    ),
);
