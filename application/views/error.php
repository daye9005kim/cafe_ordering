<?php
include_once APPPATH . 'views/_common/header.php';

if (!isset($status)) {
    $status = 404;
}

if (!isset($data)) {
    $data = array();
}

//echo $status;
print_r($data);
?>

<body>
<button class="btn btn-primary" onclick="location.href='/member/login'"> 홈 </button>
<button class="btn btn-primary" onclick="location.href='/member/logout'"> 로그아웃 </button>
<button class="btn btn-danger" onclick="location.href='/admin'"> 관리자 </button>
</body>
