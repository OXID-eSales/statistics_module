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
 * Statistics manager.
 */
class oeStatisticsManager extends oxBase
{
    /**
     * @var string Name of current class
     */
    protected $_sClassName = 'oeStatisticsManager';

    /**
     * Class constructor, initiates paren constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oestatistics');
    }

    /**
     * Sets reports array to current statistics object
     *
     * @param array $values array of reports to set in current statistics object
     */
    public function setReports($values)
    {
        $this->oestatistics__oxvalue = new oxField(serialize($values), oxField::T_RAW);
    }

    /**
     * Returns array of reports assigned to current statistics object
     *
     * @return array
     */
    public function getReports()
    {
        return unserialize($this->oestatistics__oxvalue->value);
    }

    /**
     * Sets data field value
     *
     * @param string $fieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $value     value of data field
     * @param int    $dataType  field type
     *
     * @return null
     */
    protected function _setFieldData($fieldName, $value, $dataType = oxField::T_TEXT)
    {
        if ('oxvalue' === $fieldName) {
            $dataType = oxField::T_RAW;
        }

        return parent::_setFieldData($fieldName, $value, $dataType);
    }
}
