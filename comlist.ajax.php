<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('comments', 'plug');

$tpl = cot_import('tpl','G','TXT');
$items = cot_import('items','G','INT');
$period = cot_import('period','G','INT');
$order = cot_import('order','G','TXT');
$condition = cot_import('condition','G','TXT');
$area = cot_import('area','G','TXT');
$cachetime = cot_import('cachetime','G','INT');
$pagination = cot_import('pagination','G','TXT');
$ajax_block = cot_import('ajax_block','G','TXT');

ob_clean();
echo comlist($tpl, $items, $period, $order, $condition, $area, $cachetime, $pagination, $ajax_block);
ob_flush();
exit;

