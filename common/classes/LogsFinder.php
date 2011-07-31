<?php
/**
 * HordesLinks
 * Copyright (C) 2010  Matthieu Honel (L`OcuS)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Logs finder class
 *
 * This is used to fetch logs files in a folder with name matching mask
 *
 */
class LogsFinder
{
    /**
     * Base path to files
     *
     * @var string
     */
    private $path = null;

    /**
     * Match mask
     *
     * @var string
     */
    private $mask = null;

    /**
     * List of found files
     *
     * @var array
     */
    private $files = array();

    /**
     * Magic constructor
     *
     * @param string $path Path to files
     * @param string $mask Match mask
     */
    public function __construct($path, $mask)
    {
        $this->path = $path;
        $this->mask = $mask;
    }

    /**
     * Fetch the files
     *
     * @return array List of found files
     */
    public function fetchFilesPath()
    {
        if (!count($this->files)) {
            $files = glob(sprintf('%s%s', $this->path, $this->mask));
            foreach ($files as $fileName) {
                $this->files[str_replace($this->path, '', $fileName)] = $fileName;
            }
        }
        return $this->files;
    }

}
