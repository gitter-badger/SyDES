<?php
/**
* SyDES :: helpful functions
* @version 1.8✓
* @copyright 2011-2013, ArtyGrand <artygrand.ru>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

function redirect($link, $m = '', $s = 'error'){
	setcookie('messText', $m, time()+5);
	setcookie('messStatus', $s, time()+5);
	if(Admin::$mode == 'ajax'){
		die(json_encode(array('redirect' => $link)));
	} else {
		$host = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/';
		header("Location: http://$host$link");
		die;
	}
}
	
function getip(){
	if (!empty($_SERVER['HTTP_X_REAL_IP'])){
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function rus_date(){
	$translate = array(
	'am' => 'дп', 'pm' => 'пп', 'AM' => 'ДП', 'PM' => 'ПП',
	'Monday' => 'Понедельник', 'Mon' => 'Пн', 'Tuesday' => 'Вторник', 'Tue' => 'Вт',
	'Wednesday' => 'Среда', 'Wed' => 'Ср', 'Thursday' => 'Четверг', 'Thu' => 'Чт',
	'Friday' => 'Пятница', 'Fri' => 'Пт', 'Saturday' => 'Суббота', 'Sat' => 'Сб',
	'Sunday' => 'Воскресенье', 'Sun' => 'Вс', 'January' => 'Января', 'Jan' => 'Янв',
	'February' => 'Февраля', 'Feb' => 'Фев', 'March' => 'Марта', 'Mar' => 'Мар',
	'April' => 'Апреля', 'Apr' => 'Апр', 'May' => 'Мая', 'May' => 'Мая',
	'June' => 'Июня', 'Jun' => 'Июн', 'July' => 'Июля', 'Jul' => 'Июл',
	'August' => 'Августа', 'Aug' => 'Авг', 'September' => 'Сентября', 'Sep' => 'Сен',
	'October' => 'Октября', 'Oct' => 'Окт', 'November' => 'Ноября', 'Nov' => 'Ноя',
	'December' => 'Декабря', 'Dec' => 'Дек', 'st' => 'ое', 'nd' => 'ое',
	'rd' => 'е', 'th' => 'ое'
	);

	if (func_num_args() > 1){
		$timestamp = func_get_arg(1);
		return strtr(date(func_get_arg(0), $timestamp), $translate);
	} else {
		return strtr(date(func_get_arg(0)), $translate);
	}
}

function globRecursive($dir, $mask, $recursive = false, $del = ''){
	$pages = array();
	foreach(glob($dir.'/*') as $filename){
		if (is_array($mask)){
			if (in_array(pathinfo($filename, PATHINFO_EXTENSION), $mask)){
				static $file = 1;			
				$pages[$file]['dir'] = pathinfo($filename, PATHINFO_DIRNAME);
				$pages[$file]['title'] = pathinfo($filename, PATHINFO_BASENAME);
				$pages[$file]['cyr_name'] = iconv('cp1251','utf-8//TRANSLIT', pathinfo($filename, PATHINFO_FILENAME));
				$pages[$file]['ext'] = pathinfo($filename, PATHINFO_EXTENSION);
				$file++;
			}
		} elseif (is_dir($filename)){
			$del = !$del ? $dir . '/' : $del;
			$alias = str_replace($del, '', $filename);
			$pages[$alias] = array(
				'fullpath' => $filename,
				'title' => str_replace($dir . '/', '', $filename)
			);
		} 
		if($recursive == true and is_dir($filename)){
			if (!is_array($mask)){
				$pages[$alias]['childs'] = globRecursive($filename, $mask, true, $del);
				if (!$pages[$alias]['childs']){
					unset($pages[$alias]['childs']);
				}
			} else {
				$temp = globRecursive($filename, $mask, true);
				$pages = array_merge($pages, $temp);
			}
		}
	}
	return $pages;
}

function natorder($a,$b, $what){
	return strnatcmp($a[$what], $b[$what]); 
}

function lang($text, $dl = array()){
	static $l = array();
	if ($dl) $l = array_merge($l, $dl);
	return isset($l[$text]) ? $l[$text] : $text;
}

function token($length){
    $chars = array(
        'A','B','C','D','E','F','G','H','J','K','L','M',
        'N','P','Q','R','S','T','U','V','W','X','Y','Z',
        'a','b','c','d','e','f','g','h','i','j','k','m',
        'n','o','p','q','r','s','t','u','v','w','x','y','z',
        '1','2','3','4','5','6','7','8','9');
    if ($length < 0 or $length > 58) return null;
    shuffle($chars);
    return implode('', array_slice($chars, 0, $length));
}

function render($template, $data = array()){
	if (file_exists($template)){
		extract($data);
		ob_start();
			require($template);
			$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}

function rus_ending($num, $str1, $str2, $str3){
    $val = $num % 100;
    if ($val > 10 && $val < 20) return "$num $str3";
    else {
        $val = $num % 10;
        if ($val == 1) return "$num $str1";
        elseif ($val > 1 && $val < 5) return "$num $str2";
        else return "$num $str3";
    }
}

function str_replace_once($search, $replace, $text){
   $pos = strpos($text, $search); 
   return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text; 
}

function getSelect($data, $selected, $props = ''){
	$select = "\n<select {$props}>\n";
	foreach ($data as $value => $title){
		$select .= "\t<option value=\"{$value}\"";
		$select .= in_array($value, (array)$selected) ? ' selected' : '';
		$select .= ">{$title}</option>\n";
	}
	return "{$select}</select>\n";
}

function getPaginator($url, $count, $current, $perPage = 10, $links = 3){
	$pages = ceil($count / $perPage);
	if ($pages < 2) return;
	
	$url .= strpos($url, '?') === false ? '?' : '&';
	$thisPage = floor($current / $perPage);

	if ($pages < ($links * 2) + 2){
		$from = 1;
		$to = $pages;
	} else {
		if ($thisPage < $links + 1){
			$from = 1;
			$to = ($links * 2) + 1;
		} elseif ($thisPage < $pages - $links - 1){
			$from = $thisPage - ($links - 1);
			$to = $thisPage + ($links + 1);
		} elseif ($thisPage > $pages - $links - 2){
			$from = $pages - ($links * 2);
			$to = $pages;
		}
	}
	$html = '';
	for ($i = $from; $i <= $to; $i++){
		$skip = ($i - 1) * $perPage;
		if ($current == $skip){
			$html .= '<span class="active">' . $i . '</span> ';
		} else {
			$html .= '<a href="' . $url . 'skip=' . $skip . '">' . $i . '</a> ';
		}
	}
	if ($pages > ($links * 2) + 1){
		$html = '<a href="' . $url . 'skip=0"><<</a> ' . $html . '<a href="' . $url . 'skip=' . ($pages - 1) * $perPage . '">>></a>';
	}

	return '<div class="paginator">' . $html . '</div>';
}

function getList($data, $current, $props = '', $which = 'ul'){
	$html = "\n<{$which} {$props}>\n";
	$format = '<li><a href="%1$s>%2$s</a></li>' . PHP_EOL;
	foreach ($data as $value => $title){
		$value .= $value == $current ? '" active' : '"';
		$html .= sprintf($format, $value, $title);
	}
	return $html . "</{$which}>\n";
}

function getPageData($db, $locale, $where){
	if (is_numeric($where)){
		$stmt = $db -> prepare("SELECT pages.*, pages_content.title, pages_content.content FROM pages, pages_content WHERE pages.status = '1' AND pages.id = :id AND pages_content.locale = :locale AND pages_content.page_id = pages.id");
		$stmt->execute(array('id' => (int)$where, 'locale' => $locale));
	} else {
		$stmt = $db -> prepare("SELECT pages.*, pages_content.title, pages_content.content FROM pages, pages_content WHERE pages.status = '1' AND pages.fullpath = :fullpath AND pages_content.locale = :locale AND pages_content.page_id = pages.id");
		$stmt->execute(array('fullpath' => '/'.$where, 'locale' => $locale));
	}
	return $stmt -> fetchAll(PDO::FETCH_ASSOC);
}

function getMetaData($db, $locale, $id){
	$stmt = $db -> query("SELECT key, value FROM global_meta WHERE page_id = 1");
	$metas = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	$stmt = $db -> query("SELECT key, value FROM pages_meta WHERE page_id = {$id}");
	$metas = array_merge($metas, $stmt -> fetchAll(PDO::FETCH_ASSOC));
	foreach($metas as $m){
		if (isset($m['key'][2]) and $m['key'][2] == '_' and substr($m['key'], 0, 2) == $locale){
			$meta[substr($m['key'], 3)] = $m['value'];
		} else {
			$meta[$m['key']] = $m['value'];
		}
	}
	return $meta;
}

function addFiles($type, $files){
	$format = $type == 'css' ? '<link href="%1$s" rel="stylesheet" media="all">' : '<script src="%1$s"></script>';
	$html = '';
	foreach($files as $file){
		$html .= sprintf($format, $file) . PHP_EOL;
	}
	return $html;
}

function getBreadcrumbs($crumbs){
	$html = '<ol class="breadcrumb"><li><a href=".">' . lang('home') . '</a></li>';
	foreach ($crumbs as $crumb){
		if (isset($crumb['url'])){
			$html .= '<li><a href="' . $crumb['url'] . '">' . $crumb['title'] . '</a></li>';
		} else {
			$html .= '<li class="active">' . $crumb['title'] . '</li>';
		}
	}
	return $html . '</ol>';
}

function getCheckbox($name, $checked, $text){
	$checked = $checked ? ' checked' : '';
	return '<div class="checkbox"><label><input name="' . $name . '" type="checkbox" value="1"' . $checked . '>' . $text . '</label></div>';
}



/*       TODO нижние фунекции еще проверить надо       */

/**
* Selects data from array and create inputs
* @param array $data
* @return string
*/
function getForm($data){
	$form = '';
	foreach($data as $name => $input){
		if($input['tag'] == 'textarea'){
			$form .= '<div class="title">' . $input['title'] . '</div><div><textarea name="' . $name . '" ' . $input['props'] . '>' . $input['val'] . '</textarea></div>' . PHP_EOL;
		} elseif ($input['tag'] == 'select'){
			$form .= '<div class="title">' . $input['title'] . '</div><div>' . getSelect($input['values'], '', $input['val'], 'name="' . $name . '" '.$input['props']) . '</div>';
		} else {
			$form .= '<div class="title">' . $input['title'] . '</div><div><input type="' . $input['tag'] . '" name="' . $name . '" value="' . $input['val'] . '" ' . $input['props'] . '></div>' . PHP_EOL;
		}
	}
	return $form . PHP_EOL;
}

function getSaveButton($file){
	return is_writable($file) ? '<button type="submit" class="full button">' . lang('save') . '</button>' : '<button type="button" class="full button">' . lang('not_writeable') . '</button>';
}

function getCodeInput(){
	if (!isset($_SESSION['master_code']) or $_SESSION['master_code'] != Core::$config['master_code']){
		return '<div class="title"><span class="help" title="' . lang('tip_developer_code') . '">' . lang('developer_code') . '</span>:</div><div><input type="text" name="code" class="full" required></div>';
	}
}

function canEdit(){
	if (!isset($_SESSION['master_code']) or $_SESSION['master_code'] != Core::$config['master_code']){
		if (md5($_POST['code']) == Core::$config['master_code']){
			$_SESSION['master_code'] = Core::$config['master_code'];
		} else return false;
	} 
	return true;
}

function issetTable($table){
	$stmt = Core::$db -> query("SELECT 1 FROM {$table} WHERE 1");
	if ($stmt) return true;
	else return false;
}

function createTable($table, $cols){
	$a = '';
	foreach($cols as $name => $col){
		$a .= ', ' . $name . ' ' . $col['type'];
	}
	Core::$db -> exec("CREATE TABLE {$table} (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT{$a})");
}

function clearAllCache(){
	if($handle = opendir(SYS_DIR . 'cache/')){
		while(false !== ($file = readdir($handle)))
			if($file != "." && $file != "..") unlink(ROOT_DIR . 'cache/' . $file);
		closedir($handle);
	}
}

?>