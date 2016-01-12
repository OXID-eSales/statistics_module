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
 * Adds logging to oxShopControl.
 */
class oeStatisticsOxShopControl extends oeStatisticsOxShopControl_parent
{
    /**
     * Initiates logging on every page.
     *
     * @param string $class      Name of class
     * @param string $function   Name of function
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     */
    protected function _process($class, $function, $parameters = null, $viewsChain = null)
    {
        $utils = oxRegistry::getUtils();

        if (!$utils->isSearchEngine() &&
            !($this->isAdmin() || !$this->getConfig()->getConfigParam('blOEStatisticsLogging'))
        ) {
            $this->oeStatisticsLog($class, $function);
        }

        parent::_process($class, $function, $parameters, $viewsChain);
    }

    /**
     * Logs user performed actions to DB. Skips action logging if
     * it's search engine.
     *
     * @param string $class    Name of class
     * @param string $function Name of executed class method
     */
    protected function oeStatisticsLog($class, $function)
    {
        $database = oxDb::getDb();
        $config = $this->getConfig();
        $session = $this->getSession();

        $shopId = $session->getVariable('actshop');
        $time = date('Y-m-d H:i:s');
        $sidQuoted = $database->quote($session->getId());
        $userIDQuoted = $database->quote($session->getVariable('usr'));

        $categoryId = $config->getRequestParameter('cnid');
        $articleId = $config->getRequestParameter('aid') ? $config->getRequestParameter('aid') : $config->getRequestParameter('anid');

        $parameter = '';

        if ($class == 'content') {
            $parameter = str_replace('.tpl', '', $config->getRequestParameter('tpl'));
        } elseif ($class == 'search') {
            $parameter = $config->getRequestParameter('searchparam');
        }

        $functionQuoted = $database->quote($function);
        $classQuoted = $database->quote($class);
        $parameterQuoted = $database->quote($parameter);

        $query = "insert into oestatisticslog (oxtime, oxshopid, oxuserid, oxsessid, oxclass, oxfnc, oxcnid, oxanid, oxparameter) " .
            "values( '$time', '$shopId', $userIDQuoted, $sidQuoted, $classQuoted, $functionQuoted, " . $database->quote($categoryId) . ", " . $database->quote($articleId) . ", $parameterQuoted )";

        $database->execute($query);
    }
}
