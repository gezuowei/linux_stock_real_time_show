<?php
require 'common.php';
require_once(ROOT.'/lib/httpRequest.php');

$load_funds = true;
function load_funds($stock_id)
{
    $url = 'http://stockpage.10jqka.com.cn/spService/' .$stock_id . '/Funds/realFunds';
    $max_try = 3;
    while($max_try) {
        $response = httpRequest($url);
        if($response) break;
        --$max_try;
    }
    $res = array("zlc"=>'--', "zlr"=>'--', "je"=> '--');
    if(isset($response) && ($json = json_decode($response, true))) {
        return $json['title'];
    }
    return $res;
}

function multi_load_funds($stock_ids)
{
	$url_tag = 'http://stockpage.10jqka.com.cn/spService/stock_id/Funds/realFunds';
	foreach($stock_ids as $id) {
		$id = trim($id);
		$urls[$id] = str_replace('stock_id', $id, $url_tag);
	}
	$response = curlMulti($urls);
	$all_funds = array();
	$funds = array("zlc"=>'--', "zlr"=>'--', "je"=> '--');
	foreach($response as $id=>$value) {
		if($json = json_decode($value, true)) {
			$all_funds[$id] = $json['title'];
		} else {
			$all_funds[$id] = $funds;
		}
	}
	return $all_funds;
}

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
        $res = json_decode($response, true);
        if(empty($res)) continue;
        $funds = load_funds(trim($id));
		$show_info[] = array_merge($res, $funds);
	}
	return $show_info;
}

function multi_load_data()
{
	$url_tag = 'http://stockpage.10jqka.com.cn/spService/stock_id/Header/realHeader';
	$stock_ids = file(ROOT . '/run_time/stock_id.txt');
	$show_info = array();
	if(empty($stock_ids)) return array();
	foreach($stock_ids as $id) {
		$id = trim($id);
		$urls[$id] = str_replace('stock_id', $id, $url_tag);
	}
	$all_infos = curlMulti($urls);
	$all_funds = multi_load_funds($stock_ids);
	foreach($all_infos as $id=>$info){
		if($res = json_decode($info, true)) {
			$show_info[] = array_merge($res, $all_funds[$id]);
		}
	}
	return $show_info;
}


function show_data($show_info = array())
{
    global $load_funds;
	$keys = array('stockname'=>'', 'stockcode'=>'', 'xj'=>'', 'zdf'=>'', 'zde'=>'', 'zd'=>'', 'zg'=>'');
    if($load_funds) {
        $keys = array_merge($keys, array("zlc"=>'--', "zlr"=>'--', "je"=> '--'));
    }
	$lines = 1;
	foreach($show_info as $info) {
		if($info['stockcode'] == "1A0001") $info['stockname'] = '上证指数';
		if(empty($info)) continue;
		$lines += 1;
		$info = array_merge($keys, array_intersect_key($info, $keys));
		foreach($info as $key=>$value) {
			if(in_array($key, array('zd','zg', 'zlc', 'zlr', 'je'))) {
				echo str_pad(trim($value) . "({$key})", 15);
			} else if($key == 'stockname') {
                echo str_pad(trim($value), 16);
            }else {
				echo str_pad(trim($value), 10);
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

$data = multi_load_data();
while(true) {
	list($m, $s) = explode(" ", microtime());
	$start = $s+$m;
	$lines = show_data($data);
	$data = multi_load_data();
	list($m, $s) = explode(" ", microtime());
	$take_time = ($m + $s - $start) * 1000000;
    if($take_time <= 2000000) {
        // 正常情况下保障秒级别的更新
        usleep(2000000 - $take_time);
	}
	clear_show($lines);
}

// load_funds('000936');
