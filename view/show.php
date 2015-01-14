<?php
require 'common.php';
require_once(ROOT.'/lib/httpRequest.php');

function load_data()
{
	$url = 'http://stockpage.10jqka.com.cn/spService/stock_id/Header/realHeader';
	$stock_ids = file(ROOT . '/run_time/stock_id.txt');
	$show_info = array();
	foreach($stock_ids as $id) {
		$new_url = str_replace('stock_id', trim($id), $url);
		$max_try = 3;
		while($max_try) {
			$response = httpRequest($new_url);
			if($response) break;
			--$max_try;
		}
		if(!isset($response)) continue;
		$show_info[] = json_decode($response, true);
	}
	return $show_info;
}

function show_data($show_info = array())
{
	$keys = array('stockname'=>'', 'stockcode'=>'', 'xj'=>'', 'zdf'=>'', 'zde'=>'', 'zd'=>'', 'zg'=>'');
	$lines = 1;
	foreach($show_info as $info) {
		if($info['stockcode'] == "1A0001") $info['stockname'] = '上证指数';
		if(empty($info)) continue;
		$lines += 1;
		$info = array_merge($keys, array_intersect_key($info, $keys));
		foreach($info as $key=>$value) {
			if(in_array($key, array('zd','zg'))) {
				echo str_pad(trim($value) . "({$key})", 16);
			} else {
				echo str_pad(trim($value), 15);
				//echo str_pad(trim($value), 20 - mb_strlen(trim($value), 'utf-8'));
			}
		}
		echo "\n";
	}
	echo date('Y-m-d H:i:s') . "\n";
	return $lines;
}

function clear_show($lines=0)
{
	while($lines) {
		echo "\033[1A" ; //先回到上一行
		echo "\033[K";  //清除该行
		--$lines;
	}
}

$data = load_data();
while(true) {
	list($m, $s) = explode(" ", microtime());
	$start = $s+$m;
	$lines = show_data($data);
	$data = load_data();
	list($m, $s) = explode(" ", microtime());
	$take_time = ($m + $s - $start) * 1000000;
	usleep($take_time);
	clear_show($lines);
}
