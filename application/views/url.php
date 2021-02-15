<?php

if (!isset($url)) {
    $url = '';
    die();
}

if (!isset($data)) {
    $data = '잘못된 요청입니다.';
}


header('Refresh:0;url=' . $url);
echo <<<HTML
<script type="text/javascript">
alert("{$data}");
</script>
HTML;
exit;