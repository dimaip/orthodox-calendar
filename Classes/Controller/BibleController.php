<?php
header('Access-Control-Allow-Origin: https://c.psmb.ru');
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Dmitri Pisarev <dimaip@gmail.com>, PSMB
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package orthodox
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
 //require("functions.php");
 
 

	
function arabic($roman){
	$result = 0;
	// Remove subtractive notation.
	$roman = str_replace("CM", "DCCCC", $roman);
	$roman = str_replace("CD", "CCCC", $roman);
	$roman = str_replace("XC", "LXXXX", $roman);
	$roman = str_replace("XL", "XXXX", $roman);
	$roman = str_replace("IX", "VIIII", $roman);
	$roman = str_replace("IV", "IIII", $roman);
	// Calculate for each numeral.
	$result += substr_count($roman, 'M') * 1000;
	$result += substr_count($roman, 'D') * 500;
	$result += substr_count($roman, 'C') * 100;
	$result += substr_count($roman, 'L') * 50;
	$result += substr_count($roman, 'X') * 10;
	$result += substr_count($roman, 'V') * 5;
	$result += substr_count($roman, 'I');
	return $result;
}

function do_reg($text, $regex){
	preg_match_all($regex, $text, $result);
	return($result['0']);
}
 
class Tx_Orthodox_Controller_BibleController extends Tx_Extbase_MVC_Controller_ActionController {
	private $zachala;
	private $active_trans_name = null;
	
	private function prepare_zachala(){
		$filename = $_SERVER['DOCUMENT_ROOT'].'/typo3conf/ext/orthodox/Data/cache_zachala_apostol.csv';
		$gid = 3;
		if(!file_exists($filename)){
			file_put_contents($filename, file_get_contents($google_url.$gid));
		}
		$file = fopen($filename, 'r');
		while (($line = fgetcsv($file)) !== FALSE) {
			$key = $line[0];
			$reading = $line[1];
			$this->zachala[$key] = $reading;
		}
		fclose($file);		
		
		$filename = $_SERVER['DOCUMENT_ROOT'].'/typo3conf/ext/orthodox/Data/cache_zachala_gospel.csv';
		$gid = 2;
		if(!file_exists($filename)){
			file_put_contents($filename, file_get_contents($google_url.$gid));
		}
		$file = fopen($filename, 'r');
		while (($line = fgetcsv($file)) !== FALSE) {
			$key = $line[0];
			$reading = $line[1];
			$this->zachala[$key] = $reading;
		}
		fclose($file);
	}
	
	private function parseReading(){
	
	} 
	private function availTrans($book_key, $active_trans = null){
		$i=0;
		$dir = scandir($_SERVER["DOCUMENT_ROOT"].'/bible');
		asort($dir);
		foreach($dir as $folder){
			if(!(($folder=='.')||($folder=='..'))){
				////
				$settings = file("bible/".$folder."/bibleqt.ini");
				
				foreach($settings as $key=>$setting){
					// $setting = iconv('cp1251','utf-8',$setting);
					$comm = preg_match('{^\s*//}',$setting);
					if(!$comm){		
						$bib = preg_match('{^\s*BibleName\s*=\s*(.+)$}',$setting,$matches);
						if($bib){$bi = trim($matches['1']);}
						
						$reg = '{^\s*ShortName\s*=.*(\s+'.$book_key.'\s+).*$}';
						$short_name = preg_match($reg,$setting);
						if($short_name){
							$avail_trans[$i]['path'] = trim($folder);
							$avail_trans[$i]['name'] = trim($bi);
							if($active_trans == $folder)
								$this->active_trans_name = $avail_trans[$i]['name'];
							$i++;
							break;
						}
					}
				}
			}
			
		}
		return $avail_trans;
	}
	/**
	 * action list
	 *
	 * @param string $zachalo
	 * @param string $trans
	 * @return void
	 */
	public function listAction($zachalo = 'Mt.1',$trans = null) {
		$block = "


function ajaxify(){
	var linkToBible = document.getElementById('top');
	if (linkToBible) {
		linkToBible.addEventListener('click', function (e) {
			e.preventDefault();
			location.hash = '#books';
		});
	}

	jQuery('.books li a,.trans a,.chapter_selector li a').unbind('click');
	
	jQuery('.books li a').click(function(event) {
	  event.preventDefault();
	  jQuery('.books li a.active').removeClass('active');
	  jQuery(this).addClass('active');
	  var href = jQuery(this).attr('href');
	  var book = jQuery('.books li a.active').text().trim();
	  var chap = jQuery('.chapter_selector li a.active').text().trim();
	  var trans = jQuery('.trans a.active').text().trim();
	  var doc_title = book+' '+chap+' / '+trans+' / Онлайн Библия';
	  document.title = doc_title;
	  if(history.pushState){ history.pushState(null, doc_title, href); }
	  jQuery('.wrap_center').load(href+'?type=555', function(){location.hash = '#top'; ajaxify();});
	});
	
	jQuery('.trans a').click(function(event) {
	  event.preventDefault();
	  jQuery('.trans a.active').removeClass('active');
	  jQuery(this).addClass('active');
	  var href = jQuery(this).attr('href');
	  var book = jQuery('.books li a.active').text().trim();
	  var chap = jQuery('.chapter_selector li a.active').text().trim();
	  var trans = jQuery('.trans a.active').text().trim();
	  var doc_title = book+' '+chap+' / '+trans+' / Онлайн Библия';
	  document.title = doc_title;
	  if(history.pushState){ history.pushState(null, doc_title, href); }
	  jQuery('.wrap_center').load(href+'?type=555', function(){ajaxify();});
	});
	
	jQuery('.chapter_selector li a').click(function(event) {
	  event.preventDefault();
	  jQuery('.chapter_selector li a.active').removeClass('active');
	  jQuery(this).addClass('active');
	  var href = jQuery(this).attr('href');
	  var book = jQuery('.books li a.active').text().trim();
	  var chap = jQuery('.chapter_selector li a.active').text().trim();
	  var trans = jQuery('.trans a.active').text().trim();
	  var doc_title = book+' '+chap+' / '+trans+' / Онлайн Библия';
	  document.title = doc_title;
	  if(history.pushState){ history.pushState(null, doc_title, href); }
	  jQuery('#output').load(href+'?type=555 #output', function(){ajaxify();});
	});
	
	jQuery('sup').each(function(i){
		var idstr = 's' + jQuery(this).text().trim();
		jQuery(this).attr('id',idstr);
	});
}
		
jQuery(document).ready(function() {

	ajaxify();
	var hash = window.location.hash;
	jQuery('.output p').has(hash).addClass('highlight');
	

	jQuery('.news_load').ajaxStart(function() {
		jQuery(this).hide();
		jQuery(this).after('<div style=\"text-align:left; margin-left:270px;margin-top:100px;\" id=\"spinner\"><img src=\"/typo3conf/ext/orthodox/Resources/Public/Icons/spinner.gif\"><br/>Загрузка</div>');
	}).ajaxStop(function() {
		jQuery(this).show();
		jQuery('#spinner').remove();
		jQuery('.media a').media();
	});
	
});
		";
		$GLOBALS['TSFE']->additionalHeaderData['100'] = t3lib_div::wrapJS($block);
	
		$css_block = '<style>
		#dialog textarea{
			width:100%;
			height:126px;
		}
		#linkquote{
			width:100%;
		}
		.trans-fixed{position:fixed;}
		</style>';
		$GLOBALS['TSFE']->additionalHeaderData['110'] = $css_block;
	
	
		$versekey = $zachalo;
		unset($zachalo);
		if(!$versekey)
			echo("No verse!");
		if(preg_match('/\./',$versekey)){
			$bookchap = $versekey;
		}else{
			$zachalo = $versekey;
		}
		if($bookchap){
			$bookchapExp = explode('.',$bookchap);
			$chap_key = array_pop($bookchapExp);
			$book_key = implode('.', $bookchapExp);
		}
		
		if($zachalo){
			$this->prepare_zachala();
			$ver = $this->zachala[$zachalo];
			
			
			$orig_ver = $ver;
			$ver = preg_replace('/,.*зач.*?,/','',$ver); //Евр. V, 11 - VI, 8.  Remove zach 
			$ver = preg_replace('/(\.$)/','',$ver); //Евр. V, 11 - VI, 8 remove last dot
			//$ver = preg_replace('#(\d{1,3}),(\s\w{1,4})#u','$1;$2',$ver); //VII, 37-52, VIII,12 TEMPORARY DISABLED
			$ver = preg_replace('#(\d{1,3}),(\s\d{1,3})#u','$1;$2',$ver); //VII, 37-52, 12-15
			$ver = preg_replace('#(\d{1,3}-\d{1,3})\s-(\s\w{1,4})#u','$1;$2',$ver); //VII, 37-52 - VIII,12
			$verse = explode('.',$ver); //Евр | V, 11 - VI, 8 split book from verse
			$v_parts = explode(';',$verse['1']); //V, 11 - VI, 8 split verse on parts(if multipart verse)
			$i=0;
			$print_b=1000;
			$print_e=0;
			foreach($v_parts as $v_part){ //II, 23 - III, 5   
				$part_be = explode('-',$v_part); //II, 23 | III, 5 
				$part_b = explode(',',$part_be['0']); //II| 23 
				if(!$part_b['1']){ //hard to imagine this
					$part_b['1'] = $part_b['0']; 
					if($saved_chap){
						$part_b['0'] = $saved_chap; //Get previous chapter
					}else{
						return ("Этого дня в календаре нет!");
					}
				}else{
					$part_b['0'] = arabic($part_b['0']); //Convert chpter to arabic	//II		
				}
				if($part_b['0']<$print_b){
					$print_b=$part_b['0']; //Begining of reading chap
				}
				if($part_b['0']>$print_e){
					$print_e=$part_b['0']; //Ending of reading chap
				}
				$chtenije[$i]['chap_b'] = trim($part_b['0']);
				$saved_chap = $part_b['0'];
				$chtenije[$i]['stih_b'] = trim($part_b['1']);
				
				if(!$part_be['1']){$part_be['1']=$part_be['0'];} //just a single verse, set the ending to begining
				$part_e = explode(',',$part_be['1']);  //III, 5 FLAW
				if(!$part_e['1']){ //if doesn't span across few chapters
					$part_e['1'] = $part_e['0'];
					$part_e['0'] = $part_b['0'];
				}else{
					$part_e['0'] = arabic($part_e['0']); //Convert chpter to arabic	
				}
				$saved_chap = $part_e['0'];
				if($part_e['0']<$print_b){
					$print_b=$part_e['0'];
				}
				if($part_e['0']>$print_e){
					$print_e=$part_e['0'];
				}
				$chtenije[$i]['chap_e'] = trim($part_e['0']);
				$chtenije[$i]['stih_e'] = trim($part_e['1']);
				$i++;
			}
			
			$book_key = $verse['0'];
			$book_key = str_replace(' ','',$book_key);
		}
		$avail_trans = $this->availTrans($book_key,$trans);
		
		
		$trans = $trans?$trans:$avail_trans['0']['path'];
		$this->active_trans_name = $this->active_trans_name?$this->active_trans_name:$avail_trans['0']['name'];
		
		$settings = file("bible/".$trans."/bibleqt.ini");
		
		// $utf88 = preg_match('{^\s*DefaultEncoding\s*=\s*(.+)$}',$setting,$matches);
		$utf8 = true;

		foreach($settings as $key=>$setting){
			if(!$utf8)
				$setting = iconv('cp1251','utf-8',$setting);
			$comm = preg_match('{^\s*//}',$setting);
			if(!$comm){			
				$chap = preg_match('{^\s*ChapterSign\s*=\s*(.+)$}',$setting,$matches);
				if($chap){
					$token = trim($matches['1']);
				}
				
				$path_name = preg_match('{^\s*PathName\s*=\s*(.+)$}',$setting,$matches);
				if($path_name){
					$pa = $matches['1'];
				}
				
				$fname = preg_match('{^\s*FullName\s*=\s*(.+)$}',$setting,$matches);
				if($fname){$fn = $matches['1'];}
				

				
				$reg = '{^\s*ShortName\s*=.*(\s+'.$book_key.'\s+).*$}';
				$sn = preg_match($reg,$setting);
				if($sn){
					$short_name = $sn;
				}
				
				$chap_cc = preg_match('{^\s*ChapterQty\s*=\s*(.+)$}',$setting,$matches);
				if($chap_cc){
					$chap_c = $matches['1'];
				}
				
				if($short_name&&$chap_cc){
					$path = trim($pa);
					$full_name = trim($fn);
					$chap_count = trim($chap_c);
					unset($short_name);
				}
												

			}
		}
		if($zachalo){
			$GLOBALS['TSFE']->additionalHeaderData['99'] = "<title>".$orig_ver." / Онлайн Библия</title>";
		}else{
			$GLOBALS['TSFE']->additionalHeaderData['99'] = "<title>".$full_name." ".$chap_key." / ".$this->active_trans_name." / Онлайн Библия</title>";
		}
		$filepath = 'bible/'.$trans.'/'.$path;
		$text = file_get_contents($filepath);
		if(!$utf8)
			$text = iconv('cp1251','utf-8',$text);
		$text = preg_replace('/<p>([0-9]{1,2})/','<p><sup>$1</sup>',$text);
		$text = preg_replace('/<a.*?<\/a>/i','',$text);
		
		if(substr($trans,1)=='RBO2011')
			$text = preg_replace('/<sup>/','<p><sup>',$text);
			
		$chapters = explode($token,$text);
		foreach($chapters as $i=>$chapter){
			$chapters[$i] = $token.$chapter;
		}
		foreach($chtenije as $int){
			$chapters[$int['chap_b']] = str_replace('<p><sup>'.$int['stih_b'].'</sup>','</p><span class="quote"><p><sup>'.$int['stih_b'].'</sup>',$chapters[$int['chap_b']]);
			$chapters[$int['chap_e']] = preg_replace('#(<p><sup>'.($int['stih_e']).'</sup>.*)#u','$1</p></span>',$chapters[$int['chap_e']]);
		}
		for($i=$print_b;$i<=$print_e;$i++){
			$outputc .= $chapters[$i];
		}
		
		if($bookchap){
			$outputc .= $chapters[$chap_key];
		}
		
		//
		if(substr($trans,1)=='RST'){
			$outputc = preg_replace('/Глава\s*([0-9]{1,2})/','Глава$1',$outputc);
			$outputc = preg_replace('/\s+[0-9]{1,6}/','',$outputc);
			$outputc = preg_replace('/Глава([0-9]{1,2})/','Глава $1',$outputc);
		}
		//
		if($zachalo){
			preg_match_all('#<span class="quote">.*?</span>#us',$outputc,$matches);
			if(strlen($matches[0][0])<10){
				//preg_match_all('#<span class="quote">.*#us',$outputc,$matches);
			}
			$outputc = implode('(...)<br/>',$matches['0']);
		}
		$output = $output.$outputc;
$thebible = array(
	'Ветхий завет' => array(
		'Пятикнижие Моисея' => array(
			'Бытие' => array('Gn','50'),
			'Исход' => array('Ex','40'),
			'Левит' => array('Lv','27'),
			'Числа' => array('Nm','36'),
			'Второзаконие' => array('Dt','34'),
		),
		'Книги исторические' => array(
			'Иисус Навин' => array('Jos',''),
			'Судей' => array('Jdg',''),
			'Руфь' => array('Rth',''),
			'1 Царств' => array('1Sam',''),
			'2 Царств' => array('2Sam',''),
			'3 Царств' => array('1Kn',''),
			'4 Царств' => array('2Kn',''),
			'1 Паралипоменон' => array('1Chr',''),
			'2 Паралипоменон' => array('2Chr',''),
			'Ездра' => array('Ezr',''),
			'Неемия' => array('Neh',''),
			'Есфирь' => array('Est',''),
			'1-я Маккавейская' => array('1Мак', ''),
			'2-я Маккавейская' => array('2Мак', ''),
			'3-я Маккавейская' => array('3Мак', ''),
			'2-я Ездры' => array('2Ездры', ''),
			'3-я Ездры' => array('3Ездры', ''),
			'Иудифь' => array('Иудифь', '')
		),
		'Книги учительные' => array(
			'Иов' => array('Job',''),
			'Псалтирь' => array('Ps',''),
			'Притчи' => array('Prov',''),
			'Екклесиаст' => array('Eccl',''),
			'Песня Песней' => array('Songs',''),
			'Премудрости Соломона' => array('Премудрости'),
			'Сирах' => array('Сирах', ''),
			'Товит' => array('Товит', '')
		),
		'Книги пророческие' => array(
			'Исаия' => array('Isa',''),
			'Иеремия' => array('Jer',''),
			'Плач Иеремии' => array('Lam',''),
			'Иезекииль' => array('Eze',''),
			'Даниил' => array('Dan',''),
			'Осия' => array('Hos',''),
			'Иоиль' => array('Joe',''),
			'Амос' => array('Amo',''),
			'Авдий' => array('Oba',''),
			'Иона' => array('Jona',''),
			'Михей' => array('Mic',''),
			'Наум' => array('Na',''),
			'Аввакум' => array('Hab',''),
			'Софония' => array('Zep',''),
			'Аггей' => array('Hag',''),
			'Захария' => array('Zec',''),
			'Малахия' => array('Mal',''),
			'Варух' => array('Варух', ''),
			'Послание Иеремии' => array('Посл.Иерем', ''),
		),
	),
	'Новый завет' => array(
		'Евангелия и Деяния' => array(
			'От Матфея' => array('Mt',''),
			'От Марка' => array('Mk',''),
			'От Луки' => array('Lk',''),
			'От Иоанна' => array('Jn',''),
			'Деяния' => array('Acts',''),
		),
		'Соборные Послания' => array(
			'Иакова' => array('Jam',''),
			'1 Петра' => array('1Pe',''),
			'2 Петра' => array('2Pe',''),
			'1 Иоанна' => array('1Jo',''),
			'2 Иоанна' => array('2Jo',''),
			'3 Иоанна' => array('3Jo',''),
			'Иуды' => array('Jud',''),
		),
		'Послания' => array(
			'Римлянам' => array('Rom',''),
			'1 Коринфянам' => array('1Cor',''),
			'2 Коринфянам' => array('2Cor',''),
			'Галатам' => array('Gal',''),
			'Ефесянам' => array('Eph',''),
			'Филиппийцам' => array('Phil',''),
			'Колоссянам' => array('Col',''),
			'1 Фессалоникийцам' => array('1Thes',''),
			'2 Фессалоникийцам' => array('2Thes',''),
			'1 Тимофею' => array('1Tim',''),
			'2 Тимофею' => array('2Tim',''),
			'Титу' => array('Tit',''),
			'Филимону' => array('Phlm',''),
			'Евреям' => array('Heb',''),
		),
		'Апокалипсис' => array(
			'Откровение' => array('Rev',''),
		),
	),
);

		$typenum = intval(t3lib_div::_GET('type'));

		//$this->view->assignMultiple($assignArray);
		$this->view->assign('avail_trans',$avail_trans);
		$this->view->assign('active_trans',$trans);
		$this->view->assign('book_name',$full_name);
		$this->view->assign('versekey',$versekey);
		$this->view->assign('zachalo_title',$orig_ver);
		$this->view->assign('book_key',$book_key);
		$this->view->assign('chap_key',$chap_key);
		$this->view->assign('chap_count',$chap_count);
		$this->view->assign('thebible',$thebible);
		$this->view->assign('output',$output);
		$this->view->assign('typenum',$typenum);
		$this->view->assign('filepath',$filepath);


		
	}

} ?>
