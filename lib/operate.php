<?php
require 'common.php';

if($argc !=3) echo "输入不正确";

$commond = $argv[1];

$stock_id = trim($argv[2]);
$stock_id_file = ROOT. '/run_time/stock_id.txt';

switch($commond) {
	case 'del':
		$ids = file($stock_id_file);
		if(empty($ids)) {
			echo "id不存在\n";
			break;
		}
		foreach($ids as $id) {
			$id = trim($id);
			if($id == "") continue;
			$stock_ids[] = $id;
		}
		foreach($stock_ids as $key=>$id) {
			if($id == $stock_id){
				$del_key = $key;
				break;
			}
		}
		if(!isset($del_key)) {
			echo "id不存在\n";
			break;
		}
		unset($stock_ids[$del_key]);
		file_put_contents($stock_id_file, implode("\n", $stock_ids));
		echo "del成功\n";
		break;
	case 'add':
		$stock_ids = file($stock_id_file);
		if(!empty($stock_ids)) {
			if(in_array($stock_id, $stock_ids)) {
				echo "id已经存在\n";
				break;
			}
			foreach($stock_ids as $id) {
				$id = trim($id);
				if($id == "") continue;
				$new_ids[] = $id;
			}
		}
		if(empty($new_ids)) break;
		$new_ids[] = $stock_id;
		file_put_contents($stock_id_file, implode("\n", $new_ids));
		echo "add 成功\n";
		break;
	default:
		echo '非法的命令';
		break;
}

