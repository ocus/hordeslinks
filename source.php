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
 * Which file to highlight
 */
if (!isset($_REQUEST['file'])) {
    die('Nah.');
}

/**
 * Avoid parent directory listing ;)
 */
$pwd = getcwd();
$file = $_REQUEST['file'];
$fullPath = realpath(sprintf('%s/%s', $pwd, $file));
if (strpos($fullPath, $pwd) !== 0) {
    die('Nah nah.');
}
$relativePath = str_replace($pwd, '', $fullPath);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <title>Source de <?php echo $relativePath ?></title>
        <style type="text/css">
            html { font-size: 88%; background: #fff; color: #000; }
            * { font-family: monospace; font-size: 1em; vertical-align: middle; }
            h1 { background: #ff8866; color: #5c2b20; border: 1px solid #f0d79e; font-size: 1.2em; padding: 0.1em 1em; }
            .footer { text-align: center; }
        </style>
    </head>
    <body>
        <h1>Source de <?php echo $relativePath ?></h1>
        <hr/>
<?php
/**
 * DO IT !
 */
highlight_file($fullPath);
?>
        <hr/>
        <p class="footer">
            <a href="tree.php">Retour &agrave; la liste des fichiers</a> | <a href="index.php">Retour &agrave; la recherche</a>
        </p>
<?php
require 'analytics.php';
?>
    </body>
</html>