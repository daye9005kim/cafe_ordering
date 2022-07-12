<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

function strToTeam($param)
{
	$arr_team = array();
	$str_team = '';
	foreach ($param as $value) {
		if ($value === 'all') {
			return '전체';
		}
		$tmp = explode('_', $value);
		if (!empty($tmp[1])) $arr_team[] = $tmp[1];
	}
	if (!empty($arr_team)) {
		$str_team = join(', ', $arr_team);
	}
	return $str_team;
}

function masking($name)
{
	if (empty($name)) {
		return '';
	}
	if (preg_match('/^(.)(.)(\D)?$/u', $name)) {
		$pattern = '/^(.)(.)(\D)?/u';
		$replace = '${1}⭐${3}';

	} elseif (preg_match('/^(.)(.)(.)(\D)+$/u', $name)) {
		$pattern = '/^(.)(.)(.)(\D)+$/u';
		$replace = '${1}⭐⭐${4}';

	} elseif (preg_match('/^(.)(.)(.)(\d)$/u', $name)) {
		$pattern = '/^(.)(.)(.)(\d)$/u';
		$replace = '${1}⭐${3}${4}';
	} else {
		return $name;
	}
	return preg_replace($pattern, $replace, $name);
}


/**
 * 로그기록
 * @param string $udf
 * @param string $msg
 */
function putlog($udf = 'udf', $msg = '')
{
	$CI =& get_instance();
	$path = APPPATH . 'logs/';
	$filename = $path . $CI->uri->segment(1) . '-' . $udf . '-' . date('Y-m-d') . '.php';

	$header = null;
	if (!file_exists($filename)) {
		$header = <<<TEXT
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


TEXT;
	}


	$fp = fopen($filename, 'a');
	if (!empty($header)) {
		fwrite($fp, $header);
	}
	fwrite($fp, date('Y-m-d H:i:s') . ' --> ' . $msg . "\n");
	fclose($fp);

	if (!empty($header)) {
		chmod($filename, 0777);
	}
}

/**
 * 이미지 파일명 가져오기
 * @param $src
 * @return mixed|string
 *
 */
function get_imgName($src) {
	if (empty($src)) {
		return '';
	}
	$exp = explode('/', $src);
	$name = end($exp);

	return empty($name) ? '' : $name;
}
