<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.send.new,comments.delete
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$cache && $cache->clear_realm('comlist', COT_CACHE_TYPE_DB);

?>