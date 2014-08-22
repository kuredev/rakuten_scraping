<?php

init();
$fridayArr = array(
		strtotime('+1 Friday'),
		strtotime('+2 Friday'),
		strtotime('+3 Friday'),
		strtotime('+4 Friday')
		);

foreach($fridayArr as $friday){
	$y = date("Y", $friday);
	$m = date("m", $friday);
	$d = date("d", $friday);

	$url = 'http://bus.travel.rakuten.co.jp/bus/ReSearchOpenSeatAction.do?f_rsf=&f_chd=0&f_cthr=true&f_cfou=false&f_fst=&f_sky=price&f_tst=&f_dsctcd=ALL&f_prcsq=0&f_isb=false&f_adt=1&f_cwom=false&f_ctwo=false&f_spy=&f_dscpcd=osaka&f_crel=false&f_cnig=true&f_stm='.$m.'&f_ctts=false&f_sk=0&f_spm=&f_sty='.$y.'&f_dpcpcd=tokyo&f_spd=&f_cday=false&f_spc=&f_dpctcd=ALL&f_gyt=&f_cres=false&f_rno=&f_std='.$d;

	$arr = get_bus_info($url);
	$arr += array('syutoku_date'=>'now');
	$arr += array('syuppatu_date'=>date("Y-m-d", $friday));
	$week = Week::create($arr);
}


function init(){
	require_once dirname(__FILE__).'/mysql.php';
	require_once dirname(__FILE__).'/lib/php-activerecord/ActiveRecord.php';
	require_once dirname(__FILE__).'/lib/simple_html_dom.php';

#php activerecord
	ActiveRecord\Config::initialize(function($cfg) use ($db)
			{
			$cfg->set_model_directory(dirname(__FILE__).'/models/'); 
			$cfg->set_connections(array(
					'development' => 'mysql://'.$db['user'].':'.$db['pass'].'@'.$db['host'].'/'.$db['db'],
					));
			});
}

function get_bus_info($url){
	$html = file_get_html($url);

	$kensu = $html->find(".paging_str", 0)->plaintext;
	$course_name = str_replace(array("\r\n","\r","\n"), '', trim($html->find(".result_route",0)->plaintext));
	$price = parsePrice($html->find(".result_price", 0)->plaintext);

	return array(
		"kensu" => $kensu, 
		"price" => $price, 
		"course_name"=>$course_name
		);
}

function parsePrice($price){
	return preg_replace("/[^0-9]+/", "", $price);
}


