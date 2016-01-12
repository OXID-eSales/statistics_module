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
 * Class oeStatisticsModule
 * Handles module setup, provides additional tools and module related helpers.
 *
 * @codeCoverageIgnore
 */
class oeStatisticsModule extends oxModule
{
    /**
     * Module activation script.
     *
     * @return bool
     */
    public static function onActivate()
    {
        return self::_dbEvent('install.sql', 'Error activating module: ');
    }

    /**
     * Install/uninstall event.
     * Executes SQL queries form a file.
     *
     * @param string $sSqlFile     SQL file located in module docs folder (usually install.sql or uninstall.sql).
     * @param string $failureError An error message to show on failure.
     *
     * @return bool
     */
    protected static function _dbEvent($sSqlFile, $failureError = 'Operation failed: ')
    {
        try {
            $database  = oxDb::getDb();
            $queries = file_get_contents(dirname(__FILE__) . '/../docs/' . (string) $sSqlFile);
            $queriesSplit = (array) explode(';', $queries);

            foreach ($queriesSplit as $query) {
                if (!empty($query)) {
                    $database->execute($query);
                }
            }
        } catch (Exception $ex) {
            error_log($failureError . $ex->getMessage());
        }

        return true;
    }
}
