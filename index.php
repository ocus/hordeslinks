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
 * Do this first in case of errors or warnings
 */
header('Content-Type: text/html; charset=UTF-8');

/**
 * Get the common
 */
require_once 'common/common.php';

/**
 * Get the logs files list
 */
$LFi = new LogsFinder(LOGS_PATH, '#hordes.fr.*.log.gz');
$files = $LFi->fetchFilesPath();

if (!count($files)) {
    die('No logs for now...');
}

krsort($files);

/**
 * Lines are ordered by date descendig by default
 */
$reversed = true;
if (isset($_REQUEST['r']) && trim($_REQUEST['r']) == '0') {
    $reversed = false;
}

/**
 * Lines are filtered by default (match links only)
 */
$all = false;
if (isset($_REQUEST['a']) && in_array(trim($_REQUEST['a']), array('0', '1'))) {
    $all = $_REQUEST['a'] ? true : false;
}

/**
 * A single log file is shown by default
 */
$contentsArray = array();
$hardcore = isset($_REQUEST['h']) && trim($_REQUEST['h']) == '1';
// Do it on ALL logs files
if ($hardcore) {
    $currentFile = 'L&agrave;, ya tout depuis que Renard a cr&eacute;&eacute; Internet avec Al Gore';
    foreach ($files as $filePath) {
        $contentsArray = array_merge($contentsArray, gzfile($filePath));
    }
}
else {
    $currentFile = key($files);
    if (isset($_REQUEST['f']) && isset($files[$_REQUEST['f']])) {
        $currentFile = $_REQUEST['f'];
    }
    $contentsArray = gzfile($files[$currentFile]);
}

if ($reversed) {
    $contentsArray = array_reverse($contentsArray);
}

/**
 * Create the formatter
 */
$LFo = new LogsFormatter(htmlentities(utf8_decode(implode('', $contentsArray))));
// Filter on links
if (!$all) {
    $LFo->addFilter('`(https?://[^\s]+)`');
}
// Formats for nicks
$LFo->addFormat('`^(\d{2}:\d{2})\s+&lt;([^&]+)&gt;`', '[<span class="time">\1</span>] &lt;<span class="nick">\2</span>&gt;');
$LFo->addFormat('`^(\d{2}:\d{2})\s+-!-\s+([^\s]+)\s+`', '[<span class="time">\1</span>] -!- <span class="nick">\2</span> ');
$LFo->addFormat('`^(\d{2}:\d{2})\s+\*\s+([^\s]+)\s+`', '[<span class="time">\1</span>] * <span class="nick">\2</span> ');
// Format for links
$LFo->addFormat('`(https?://[^\s]+)`', '<a href="\1" class="link" target="_blank">\1</a>');
// Format for all lines
$LFo->addFormat('`(.+)`', '<a href="#" title="Je veux retourner vers haut ! Vers l\'ELITE quoi !!!" class="top">&uarr;</a> <a href="#[__LINE_NUMBER__]" name="[__LINE_NUMBER__]" title="Ca, c\'est environ la ligne [__LINE_NUMBER__]" class="link">#</a> \1');

// Special formats for the ELITE
$nicks = array(
    'celestia' => 'LA PLUS CULTIVATRICE DE MELONS !',
    'melle' => 'LA PLUS BELLEUH !',
    'dge' => 'LE PLUS HOT !',
    'l`ocus' => 'LE PLUS CON...',
    'bartov' => '...',
    'renard' => 'Le plus vieux...',
    '\[zen\]' => 'Le plus vieux...',
    'tutu' => 'Le plus... heu...',
    'thefranchise' => '3PA !!!',
);
foreach ($nicks as $nick => $title) {
    $LFo->addFormat(sprintf('@>(.?%s[^<]*)<@i', $nick), sprintf('><span title="%s">\1</span><', htmlentities($title)));
}
$formattedContents = $LFo->getFormated();

// Logs files dropdown
$selectBoxOptions = array();
foreach ($files as $fileName => $filePath) {
    array_push(
        $selectBoxOptions,
        sprintf(
            '<option value="%1$s"%3$s>%2$s</option>',
            $fileName,
            $fileName,
            ($fileName == $currentFile ? ' selected="selected"' : '')
        )
    );
}
$selectBox = sprintf('<select id="file" name="f">%s</select>', implode('', $selectBoxOptions));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
        <title>L'ELITE dans le Internet !</title>
        <style type="text/css">
            html { font-size: 88%; background: #fff; color: #000; }
            * { font-family: monospace; font-size: 1em; vertical-align: middle; }
            h1 { background: #ff8866; color: #5c2b20; border: 1px solid #f0d79e; font-size: 1.2em; padding: 0.1em 1em; }
            form { margin: 0; padding: 0.1em 1em; border: solid 1px #f0d78a; background: #693e29; color: #f0d79e; }
            form ul { list-style: none; margin: 0 1em; padding: 0; }
            fieldset { padding: 0.1em; margin-top: 0.2em; }
            legend { font-weight: bold; }
            .explain { text-align: right; font-style: italic; }
            .logs { white-space: pre; padding: 0.1em 1em; background: #5c2b20; border: solid 1px #f0d78a; color: #fff; }
            .logs .time, .logs.nick { color: #ff8866; }
            .logs .nick { font-weight: bold; }
            a.link, a.link:link, a.top, a.top:link, a.top:visited { text-decoration: none; color: #ddab76; border-bottom: solid 1px #ddab76; }
            a.top, a.top, a.top:link, a.top:visited { border: none; }
            a.link:hover, a.top.hover { color: #fff; border-bottom-color: #fff; }
            a.link:visited { border-bottom: none; text-decoration: line-through; }
            .footer { text-align: center; }
            em { border-bottom: dotted 1px #000; }
            .lescigales { margin: 1em; text-align: center; }
            .lescigales a, .lescigales a:link, .lescigales a:visited { color: #8346c4; }
        </style>
    </head>
    <body>
        <p class="explain">Toi aussi, tu as le droit de matter les m&ecirc;mes trucs <strong>TROP BIENS</strong> que l'<strong>ELITE</strong> matte dans le Internet.</p>
        <form method="get" action="">
            <fieldset>
            <legend>Recherche g0g0le:</legend>
            <ul>
                <li><label for="h0"><input type="radio" id="h0" name="h" value="0"<?php echo !$hardcore ? ' checked="checked"' : '' ?> /> Je veux farfouiller dans</label> <?php echo $selectBox ?></li>
                <li><label for="h1"><input type="radio" id="h1" name="h" value="1"<?php echo  $hardcore ? ' checked="checked"' : '' ?> disabled="disabled"/> <strike>Je suis Renard, je ne cherche pas : <em>l'information vient &agrave; moi !</em></strike> Renard a un m&eacute;nisque en carton, reviens plus tard.</label></li>
            </ul>
            </fieldset>
            <fieldset>
            <legend>Les trucs sp&eacute;ciaux que je veux (oupa):</legend>
            <ul>
                <li><label for="all"><input id="all" type="checkbox" name="a" value="1"<?php echo $all ? ' checked="checked"' : '' ?> />Montre-moi TOUT ! \o/</label></li>
                <li>Sens de lecture:
                    <ul>
                        <li><label for="reverse_on"><input id="reverse_on" type="radio" name="r" value="1"<?php echo  $reversed ? ' checked="checked"' : '' ?> />Tu crois vraiment que je vais scroller jusqu'en bas pour voir les nouveaux trucs ? oO</label></li>
                        <li><label for="reverse_of"><input id="reverse_of" type="radio" name="r" value="0"<?php echo !$reversed ? ' checked="checked"' : '' ?> />C'est tout &agrave; l'envers, je comprend que dalle !</label></li>
                    </ul>
                </li>
            </ul>
            </fieldset>
            <p><input type="hidden" name="_dc" value="<?php echo time() ?>" /><button type="submit">&rarr; Tu t'd&eacute;p&ecirc;ches de m'filer c'que j'cherche ?</button></p>
        </form>
        <h1><?php echo $currentFile ?></h1>
        <p class="logs"><?php echo $formattedContents ?></p>
        <p class="explain">Ouais, alors, faut savoir que c'est mis &agrave; jour <strong>toutes les minutes</strong> (je peux pas faire mieux)<br/> et <strong>uniquement</strong> si je suis connect&eacute;.<br/>Donc <strong>TU FAIS PAS CHIER</strong>.</p>
        <p class="footer">Copyright: <em title="Ben ouais, ya pas grand chose &agrave; voler ici...">Y en n'a pas</em> | Auteur: <em title="Et ta soeur ?">OcuS</em> | Contact: <em title="C'est l'adresse à mettre dans ton putain de mIRC, sale windowsien !">irc.quakenet.org:6667</em> Canal: <em title="Si tu sais pas comment faire, tu te d&eacute;merdes. Et surtout, je vois pas ce que tu viens foutre ici...">#hordes.fr</em> | <a href="tree.php" class="link">La source</a></p>
        <p class="lescigales"><a href="http://www.lescigales.org" title="lesCigales.org : H&eacute;bergement gratuit sans publicit&eacute;">H&eacute;bergement gratuit et sans publicit&eacute; assur&eacute; par lesCigales.org<br/><img src="images/lescigales.png" alt="Logo lesCigales.org" title="lesCigales.org : H&eacute;bergement gratuit sans publicit&e&eacute;" /></a></p>
<?php
require 'analytics.php';
?>
    </body>
</html>
