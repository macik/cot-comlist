<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

/**
 * Generates page list widget
 * @param  string  $tpl        Template code
 * @param  integer $items      Number of items to show. 0 - all items
 * @param  integer $period     Limit of days in past, 0 - no limits
 * @param  string  $order      Sorting order (SQL)
 * @param  string  $condition  Custom selection filter (SQL)
 * @param  string  $area       Custom areas list semicolon separated
 * @param  integer $cachtime   Caching time in ms, 0 for none
 * @param  string  $pagination Pagination parameter name for the URL, e.g. 'pcm'. Make sure it does not conflict with other paginations. Leave it empty to turn off pagination
 * @param  string  $ajax_block DOM block ID for ajax pagination
 * @return string              Parsed HTML
 */

function comlist($tpl = 'comlis', $items = 0, $period = 120, $order = '', $condition = '', $area = 'page;polls', $cachetime = 3600, $pagination = '', $ajax_block = '')
{
	global $db, $lang, $id;

	$db_com = cot::$db->com;
	$db_polls = cot::$db->polls;
	$db_pages = cot::$db->pages;
	if (!empty($area))
	{
		$where_condition = " WHERE com_area IN ('".str_replace(";","','",$area)."') ";
	}

	// Get pagination number if necessary
	if (!empty($pagination))
	{
		list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
	}
	else
	{
		$d = 0;
	}

	$time_limit = ($period>0) ? " WHERE com_date > ". (cot::$sys['now'] - $period * 86400) : '';

	// Display the items
	$t = new XTemplate(cot_tplfile($tpl, 'plug'));
	$join_columns = "";
	$join_tables = "";

	if(!empty($where_condition) && !empty($condition))
	{
		$where_condition .= "AND $condition";
	}
	elseif(empty($where_condition) && !empty($condition))
	{
		$where_condition .= "WHERE $condition";
	}

	/* === Hook === */
	foreach (cot_getextplugins('comlist.comments.query') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql_order = empty($order) ? '' : "ORDER BY $order";
	$sql_limit = ($items > 0) ? "LIMIT $d, $items" : '';

	$cachvar = $tpl.'_'.cot::$env['location'].'_'.$id.'_'.$lang.'_'.$pg.'_'.cot::$usr['level'];

	if( $cachetime>0 && cot::$cache && cot::$cache->db->exists($cachvar, 'comlist'))
	{
		$html_comlist = cot::$cache->db->get($cachvar, 'comlist');
	}
	else
	{
		if(!empty($pagination))
		{
			$total = cot::$db->query("
				SELECT * from $db_com c INNER JOIN
					(SELECT MAX(com_id) AS com_id_max, com_area, com_code FROM $db_com $join_tables $time_limit
					GROUP BY com_area, com_code) c_ ON c.com_id=c_.com_id_max
				$where_condition
			");

			$totalitems = $total->rowCount();
		}
		else
		{
			$totalitems = "";
		}

		$res = $db->query("
			SELECT * from $db_com c
			INNER JOIN
				(SELECT MAX(com_id) AS com_id_max, COUNT(*) AS com_count, com_area, com_code FROM $db_com $join_tables $time_limit
				GROUP BY com_area, com_code) c_ ON c.com_id=c_.com_id_max
			LEFT JOIN $db_pages As p ON c.com_code=p.page_id AND c.com_area = 'page' AND p.page_state='0'
			LEFT JOIN $db_polls As pl ON c.com_code=pl.poll_id AND c.com_area = 'polls'
			$where_condition
			$sql_order
			$sql_limit
			");

		$jj = 1;

		while ($row = $res->fetch())
		{
			$t->assign(cot_generate_pagetags($row, 'COM_ROW_PAGE_'));

			$t->assign(array(
				'COM_ROW_NUM' => $jj,
				'COM_ROW_ODDEVEN' => cot_build_oddeven($jj),
				'COM_ROW_RAW' => $row,
				'COM_ROW_COUNT' => $row['com_count'],
				'COM_ROW_COM_AREA' => $row['com_area'],
				'COM_ROW_DATE' => cot_date('datetime_medium', $row['com_date']),
				'COM_ROW_DATE_STAMP' => $row['com_date'],
				'COM_ROW_POLL_TITLE' => htmlspecialchars($row['poll_text']),
				'COM_ROW_POLL_ID' => $row['poll_id'],
				'COM_ROW_TEXT' => strip_tags(preg_replace('/(<blockquote>.+?<\/blockquote>)/s','',$row['com_text'])), //�������� ����������� � ����
			));

			$row['user_id'] = $row['com_authorid'];
			$row['user_name'] = $row['com_author'];
			$t->assign(cot_generate_usertags($row, "COM_ROW_OWNER_", htmlspecialchars($row['com_author']), false,false));

			if (((cot::$usr['id']>0 && $row['com_authorid']!=cot::$usr['id']) || cot::$usr['id']==0) && cot::$usr['lastvisit']<$row['com_date'])
			{
				$t->assign('COM_ROW_NEW',cot::$L['New']);
				$jn++;
			}
			else
			{
				$t->assign('COM_ROW_NEW','');
			}

			/* === Hook === */
			foreach (cot_getextplugins('comlist.comments.loop') as $pl)
			{
				include $pl;
			}
			/* ===== */

			$t->parse("MAIN.COM_ROW");

			$jj++;
		}

		$t->assign('COMMENT_TOP_NEWCOUNT', $jn);

		if(!empty($pagination))
		{
			// Render pagination
			$url_area = defined('COT_PLUG') ? 'plug' : cot::$env['ext'];

			if (defined('COT_LIST'))
			{
				global $list_url_path;
				$url_params = $list_url_path;
			}
			elseif (defined('COT_PAGES'))
			{
				global $al, $id, $pag;
				$url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
			}
			elseif(defined('COT_USERS'))
			{
				global $m;
				$url_params = empty($m) ? array() :  array('m' => $m);
			}
			else
			{
				$url_params = array();
			}

			$url_params[$pagination] = $durl;

			if(!empty($ajax_block)){
				$ajax = true;
				$ajax_plug = 'plug';
				$ajax_plug_params = "r=comlist&tpl=$tpl&items=$items&period=$period&order=$order&condition=$condition&area=$area&pagination=$pagination&cachetime=$cachetime&ajax_block=$ajax_block";
			}
			else{
				$ajax = false;
				$ajax_plug = '';
				$ajax_plug_params = "";
			}
			$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination, '', $ajax, $ajax_block, $ajax_plug, $ajax_plug_params);

				$t->assign(array(
				'PAGE_TOP_PAGINATION'  => $pagenav['main'],
				'PAGE_TOP_PAGEPREV'    => $pagenav['prev'],
				'PAGE_TOP_PAGENEXT'    => $pagenav['next'],
				'PAGE_TOP_FIRST'       => $pagenav['first'],
				'PAGE_TOP_LAST'        => $pagenav['last'],
				'PAGE_TOP_CURRENTPAGE' => $pagenav['current'],
				'PAGE_TOP_TOTALLINES'  => $totalitems,
				'PAGE_TOP_MAXPERPAGE'  => $items,
				'PAGE_TOP_TOTALPAGES'  => $pagenav['total']
			));
		}

		/* === Hook === */
		foreach (cot_getextplugins('comlist.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if($jj==1)
		{
			$t->parse("MAIN.NONE");
		}

		$t->parse();
		$html_comlist = $t->text();

		if(cot::$cache && $cachetime>0)
		{
			cot::$cache->db->store($cachvar, $html_comlist, 'comlist', $cachetime);
		}
	}

	return $html_comlist;
}
