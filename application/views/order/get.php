<?php
include_once APPPATH . 'views/_common/header.php';
echo "<xmp>";
//print_r($data);
echo "</xmp>";

$list = '';
foreach	($data['order'] as $val) {
	$list .= <<<HTML
<li>{$val['product_nm']} {$val['product_size']} {$val['product_cnt']}</li>\n
HTML;
	}
?>

<script>

</script>

<body>
<ul>
	<?=$list?>

</ul>
</body>
