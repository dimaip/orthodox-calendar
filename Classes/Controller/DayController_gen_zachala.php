		/* GRAP SAINTS
		set_time_limit(3600);
		
		$filename = $_SERVER['DOCUMENT_ROOT'].'/typo3conf/ext/orthodox/Data/static_saints.csv';
		$fp = fopen($filename, 'w');
		fputcsv($fp, $fields);
		$dd = strtotime('01-01-2012');
		$ed = strtotime('01-01-2013');
		while ($dd < $ed){
			$dd_n = strtotime("+13 days", $dd);
			$day = date("d", $dd_n);
			$month = date("m", $dd_n);
			sleep(2);
			$ht = file_get_contents('http://www.holytrinityorthodox.com/ru/calendar/calendar.php?month='.$month.'&today='.$day.'&year=2012&dt=0&header=0&lives=5&trp=0&scripture=0');
			$ht = iconv('cp1251','utf-8',$ht);
			$ht = str_replace('http://www.holytrinityorthodox.com/ru/calendar/jcal_img/','/typo3conf/ext/orthodox/Resources/Public/Icons/',$ht);
			$ht = str_replace('onClick="return popup(this, \'los\')"','',$ht);
			$ht = str_replace('border="0"','',$ht);
			$arr = array(date("d/m", $dd), $ht);
			fputcsv($fp, $arr);
			$dd = strtotime("+1 days", $dd);
		}
		fclose($fp);
		*/
		
		/*
	private function generate_zachala(){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_biblequote_days');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$str .= $row['reading'];
		}
		preg_match_all("/(\w{2,4})\.,\s(\d{1,3})\sзач\.,[^\.]*\./u",$str,$out);
		foreach($out['0'] as $i => $o){
			$book = $out['1'][$i];
			$zachalo = $out['2'][$i];
			
			if($book == 'Ин'||$book == 'Лк'||$book == 'Мк'||$book == 'Мф'){
				if(!$arra[$book][$zachalo]){
					switch ($book){
						case 'Мф':
						$book = 'Mt';
						break;						
						case 'Мк':
						$book = 'Mk';
						break;					
						case 'Лк':
						$book = 'Lk';
						break;				
						case 'Ин':
						$book = 'Jh';
						break;
					}
					$flag = false;
					foreach($arra['Gs'][$book.$zachalo] as $vv){
						if($vv == $o)
							$flag = true;
					}
					if(!$flag)
						$arra['Gs'][$book.$zachalo][] = $o;
				}
			}else{
				$flag = false;
				foreach($arra['Ap'][$zachalo] as $vv){
					if($vv == $o)
						$flag = true;
				}
				if(!$flag)
					$arra['Ap'][$zachalo][] = $o;
			}
		}
		return $arra;
	}
	*/
	
			/*for($i = 2005; $i < 2020; $i++){
		$week = datediff('ww', easter($i), easter($i+1), true);
		if ($week > 52)
			$debug .= "вне";
		else
			$debug .= "внутрь";
		$debug .= $i.": ".$week."<br/>";
		}*/