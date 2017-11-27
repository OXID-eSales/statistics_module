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
 * Base reports class
 */
class OeStatistics_Report_Base extends oxAdminView
{
    /**
     * Include JPGraph classes.
     */
    public function __construct()
    {
        parent::__construct();

        define('USE_CACHE', false);
        define('CACHE_DIR', $this->getConfig()->getConfigParam('sCompileDir'));

        $modulePath = $this->getViewConfig()->getModulePath('oestatistics');
        require_once "$modulePath/core/jpgraph/jpgraph.php";
        require_once "$modulePath/core/jpgraph/jpgraph_bar.php";
        require_once "$modulePath/core/jpgraph/jpgraph_line.php";
        require_once "$modulePath/core/jpgraph/jpgraph_pie.php";
        require_once "$modulePath/core/jpgraph/jpgraph_pie3d.php";
    }

    /**
     * Smarty object
     *
     * @return
     */
    protected $smarty = null;

    /**
     * Returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        return $this->_sThisTemplate;
    }

    /**
     * Smarty object setter
     *
     * @param smarty $oSmarty smarty object
     */
    public function setSmarty($oSmarty)
    {
        $this->smarty = $oSmarty;
    }

    /**
     * Returns Smarty object
     *
     * @return smarty
     */
    public function getSmarty()
    {
        return $this->smarty;
    }

    /**
     * Returns array with week range points
     *
     * @return array
     */
    public function getWeekRange()
    {
        $config = $this->getConfig();

        // initializing one week before current..
        $from = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($config->getConfigParam('iFirstWeekDay'), strtotime(oxRegistry::getConfig()->getRequestParameter("time_from")));
        $to = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber($config->getConfigParam('iFirstWeekDay'), strtotime(oxRegistry::getConfig()->getRequestParameter("time_to")));

        return array($from - 1, $to + 1);
    }

    /**
     * Returns predefined graph object
     *
     * @param int    $xSize         graph image x size
     * @param int    $ySize         graph image y size
     * @param string $backgroundImg background filler image (full path)
     * @param string $scaleType     graph scale type ["textlin"]
     *
     * @return Graph
     */
    public function getGraph($xSize, $ySize, $backgroundImg = null, $scaleType = "textlin")
    {
        $backgroundImage = $this->getViewConfig()->getModulePath('oestatistics', 'out/pictures/reportbgrnd.jpg');
        $backgroundImg = $backgroundImg ? $backgroundImg : $backgroundImage;

        // New graph with a drop shadow
        $jpGraph = new Graph($xSize, $ySize);

        $jpGraph->setBackgroundImage($backgroundImg, BGIMG_FILLFRAME);

        // Use a "text" X-scale
        $jpGraph->setScale($scaleType);

        // Label align for X-axis
        $jpGraph->xaxis->setLabelAlign('center', 'top', 'right');

        // Label align for Y-axis
        $jpGraph->yaxis->setLabelAlign('right', 'bottom');

        // shadow
        $jpGraph->setShadow();

        // Use built in font
        $jpGraph->title->setFont(FF_FONT1, FS_BOLD);

        return $jpGraph;
    }
}
