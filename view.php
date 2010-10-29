<?php
/**
 * View module
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
define('COT_VIEW', true);
$env['ext'] = 'view';
$env['location'] = 'views';

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

$v = cot_import('v', 'G', 'TXT');

if (mb_strpos($v, '.') !== false || mb_strpos($v, '/') !== false)
{
	die('Wrong URL.');
}

$incl_html = "datas/html/".$v.".html";
$incl_htm = "datas/html/".$v.".htm";
$incl_txt = "datas/html/".$v.".txt";

if (file_exists($incl_txt))
{
	$fd = @fopen($incl_txt, 'r') or die("Couldn't find a file : ".$incl_txt); // TODO: Need translate
	$vd = fread($fd, filesize($incl_txt));
	fclose($fd);
}
elseif (file_exists($incl_htm))
{
	$fd = @fopen($incl_htm, 'r') or die("Couldn't find a file : ".$incl_htm); // TODO: Need translate
	$vd = fread($fd, filesize($incl_htm));
	fclose($fd);
}
elseif (file_exists($incl_html))
{
	$fd = @fopen($incl_html, 'r') or die("Couldn't find a file : ".$incl_html); // TODO: Need translate
	$vd = fread($fd, filesize($incl_html));
	fclose($fd);
}
else
{
	cot_die();
}

if (preg_match('@<head>(.*?)</head>@si', $vd, $ext_head) == 1)
{
	$ext_head = $ext_head[1];
}

if (preg_match('@<body[^>]*?>(.*?)</body>@si', $vd, $ext_body) == 1)
{
	$ext_body = $ext_body[1];
}

$vt = '&nbsp;';

if (mb_stripos($ext_head, '<meta name="cot_title"') !== false)
{
	$vt = mb_stristr($ext_head, '<meta name="cot_title"');
	$vt = mb_stristr($vt, 'content="');
	$vt = mb_substr($vt, 9);
	$tag_title_end = mb_strpos($vt, '">');
	$vt = mb_substr($vt, 0, $tag_title_end);
}
elseif (preg_match('@<title>(.*?)</title>@si', $ext_head, $vt)==1)
{
	$vt = $vt[1];
}

if (preg_match_all('@<script[^>]*?>(.*?)</script>@si',$ext_head,$ext_js) > 0)
{
	foreach ($ext_js[1] as $js)
	{
		$js = preg_replace(array('@<!--(.*?)\n@si','@\/\/(.*?)-->\n@si'),array('',''),$js);
		$morejavascript .= $js;
	}
}

if (preg_match_all('@<link[^>](.*?)>@si', $ext_head, $ext_links) > 0)
{
	foreach ($ext_links[0] as $link)
	{
		$moremetas .= $link;
	}
}

require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate(cot_skinfile('plugin'));

$t->assign(array(
	"PLUGIN_TITLE" => $vt,
	"PLUGIN_BODY" => $ext_body
));

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'].'/footer.php';

?>