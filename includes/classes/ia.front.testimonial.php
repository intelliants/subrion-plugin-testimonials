<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaTestimonial extends abstractModuleFront
{
    protected static $_table = 'testimonials';

    protected $_itemName = 'testimonials';

    private $_foundRows;

    public function get($where, $start = null, $limit = null, $order = null)
    {


        $sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS *
FROM :table_testimonials
WHERE :where :status
ORDER BY :order 
LIMIT :start, :limit
SQL;
        $sql = iaDB::printf($sql,[
            'table_testimonials' => self::getTable(true),
            'where' => $where ? $where . ' AND' : '',
            'order' => $order ? $order : '`date` DESC',
            'start' => $start ? $start : 0,
            'limit' => $limit ? $limit : 1,
            'status' => "`status` = 'active'"
        ]);

        $rows = $this->iaDb->getAll($sql);
        $this->_foundRows = $this->iaDb->foundRows();

        $this->_processValues($rows);

        return $rows;
    }

    public function getFoundRows()
    {
        return $this->_foundRows;
    }
}


