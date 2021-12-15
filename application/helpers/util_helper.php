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
