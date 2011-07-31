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
 * Logs formatter class
 *
 */
class LogsFormatter
{
    /**
     * The whole logs contents
     *
     * @var string
     */
    private $contents = '';

    /**
     * Lines filters
     *
     * @var array
     */
    private $filters = array();

    /**
     * Formaters
     *
     * format => replace
     *
     * @var array
     */
    private $formats = array();

    /**
     * Magic constructor
     *
     * @param string $contents The logs contents
     */
    public function __construct($contents)
    {
        $this->contents = $contents;
    }

    /**
     * Add a filter
     *
     * @param string $filter Regular expression filter
     */
    public function addFilter($filter)
    {
        array_push($this->filters, $filter);
    }

    /**
     * Add a format
     *
     * @param string $search Regular expression search
     * @param string $replace Regular expression replacement
     */
    public function addFormat($search, $replace)
    {
        $this->formats[$search] = $replace;
    }

    /**
     * Gets the formatted logs
     *
     * @return string
     */
    public function getFormated()
    {
        $contents = $this->contents;
        $contentsLines = preg_split("@\n|\r\n@", $contents);
        $hasFilters = count($this->filters) > 0;
        $hasFormats = count($this->formats) > 0;
        if ($hasFilters || $hasFormats) {
            $formattedContentsLines = array();
            foreach ($contentsLines as $n => $contentsLine) {
                $match = false;
                foreach ($this->filters as $filter) {
                    if (preg_match($filter, $contentsLine)) {
                        $match = true;
                    }
                }
                if ($match || !$hasFilters) {
                    foreach ($this->formats as $search => $replace) {
                        $contentsLine = preg_replace($search, $replace, $contentsLine);
                    }
                    $contentsLine = str_replace('[__LINE_NUMBER__]', $n, $contentsLine);
                    array_push($formattedContentsLines, $contentsLine);
                }
            }
            $contentsLines = $formattedContentsLines;
        }
        $contents = implode("\n", $contentsLines);
        return $contents;
    }
}
