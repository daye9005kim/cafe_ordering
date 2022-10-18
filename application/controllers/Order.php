<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER)) {
			return $this->load->view('view', array('status' => 308, 'url' => '/member/login', 'data' => '로그인 해주세요.'));
		}

		$ordnum = $this->input->get('ordnum');
		if (empty($ordnum)) {
			return $this->load->view('view', array('status' => 400, 'data' => '주문번호가 없습니다.'));
		}
		$buyer = $this->Buyer_model->select(array('ordnum' => $ordnum));
		$conf_admin = $this->config->item('admin');
		$admin = in_array($SES_USER['name'], $conf_admin['member']);

		if (empty($buyer)) {
			if ($admin) {
				return $this->load->view('view', array('status' => 308, 'url' => '/admin/index', 'data' => '생성된 주문이 없습니다. 관리자에게 문의하세요.'));
			}

			return $this->load->view('view', array('status' => 400, 'data' => '생성된 주문이 없습니다. 관리자에게 문의하세요.'));
		}

		$team = explode(',', $buyer[0]['invite']);

		if ($buyer[0]['invite'] !== 'all' && !empty($SES_USER['team'])) { //본부장 제외
			if (!in_array($SES_USER['team'], $team)) {
				return $this->load->view('view', array('status' => 400, 'data' => '당신은 주문에 초대되지 않으셨습니다.'));
			}
		}

		$menu = $this->Drink_model->select(array('cafe' => $buyer[0]['cafe']));
		$param = array('ordnum' => $buyer[0]['ordnum'], 'member_name' => $SES_USER['name']);
		$order = $this->Order_model->select($param);
		$buyer[0]['invite'] = strToTeam($team);
		$pickup = $this->Pickup_model->select($param);

		$return = array(
			'user' => $SES_USER,
			'menu' => $menu,
			'buyer' => $buyer[0],
			'order' => isset($order[0]) ? $order[0] : array(),
			'timer' => date('m/d/Y H:i', strtotime($buyer[0]['end'])),
			'admin' => $admin,
			'pickup' => !empty($pickup), //pickup volunteer
		);
		return $this->load->view('view', array('status' => 200, 'data' => $return));

	}

	/**
	 * 메뉴 가져오기
	 * @return object|string
	 */
	public function menu()
	{
		$code = $this->input->post('code');
		$cafe = $this->input->post('cafe');

		if (empty($cafe)) {
			return $this->load->view('json', array('status' => 400, 'data' => 'cafe가 없습니다.'));
		}

		$menu = $this->Drink_model->select(array('product_cd' => $code, 'cafe' => $cafe));

		$info = array(
			"product_cd" => $menu[0]['product_cd'],
			"product_nm" => $menu[0]['product_nm'],
			"product_img" => $menu[0]['product_img'],
			"cate_cd" => $menu[0]['cate_cd'],
			"content" => $menu[0]['content']
		);

		return $this->load->view('json', array('status' => 200, 'data' => array('menu' => $info)));

	}

	/**
	 * 내 주문 보기
	 * @return object|string
	 */
	public function get()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);
		$cafe = $this->input->post('cafe');

		if (empty($SES_USER)) {
			return $this->load->view('json', array('status' => 400, 'data' => '로그인 해주세요.'));
		}
		if (empty($cafe)) {
			return $this->load->view('json', array('status' => 400, 'data' => '카페가 없습니다.'));
		}

		$order = $this->Order_model->select(array('member_name' => $SES_USER['name'], 'cafe' => $cafe));

		$return = array();
		foreach ($order as $value) {
			$return[$value['num']] = $value;
		}
		krsort($return);

		return $this->load->view('json', array('status' => 200, 'data' => array('order' => array_values($return))));
	}


	/**
	 * 최종 주문서
	 * @return object|string
	 */
	public function prnt()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);
		$ordnum = $this->input->get('ordnum');

		if (empty($SES_USER)) {
			return $this->load->view('json', array('status' => 400, 'data' => '로그인 해주세요.'));
		}

		$order = $this->Order_model->select(array('ordnum' => $ordnum));
		$arr = array();
		$total = 0;
		foreach ($order as $item) {
			$total += $item['product_cnt'];
			$cnt = $item['product_cnt'];
			if (!isset($arr[$item['product_nm']][$item['product_size']])) {
				$arr[$item['product_nm']] = array(
					'tall' => 0,
					'grande' => 0,
					'venti' => 0
				);
			}
			$arr[$item['product_nm']][$item['product_size']] = $arr[$item['product_nm']][$item['product_size']] + $cnt;
		}
		return $this->load->view('view', array('status' => 200, 'data' => array('order' => $arr, 'total' => $total, 'ordnum' => $ordnum)));
	}

	/**
	 * 회원별 주문서
	 * @return object|string
	 */
	public function mprnt()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);
		$ordnum = $this->input->get('ordnum');

		if (empty($SES_USER)) {
			return $this->load->view('view', array('status' => 400, 'data' => '로그인 해주세요.'));
		}

		$cafe =  $this->Order_model->get_cafe(array('ordnum' => $ordnum));
		if (empty($cafe)) {
			return $this->load->view('view', array('status' => 400, 'data' => '카페 코드가 없습니다.'));
		}
		$order = $this->Order_model->select(array('ordnum' => $ordnum, 'cafe' => $cafe));
		$arr = array();
		$total = 0;
		$config = $this->config->item('cafe');
		if (empty($order)) {
			return $this->load->view('view', array('status' => 400, 'data' => '주문해주세요.'));
		}
		foreach ($order as $item) {
			$total += $item['product_cnt'];
			$cnt = $item['product_cnt'];
			if (!isset($arr[$item['product_nm']][$item['product_size']])) {
				$arr[$item['product_nm']] = $config[$item['cafe']]['size'];
			}
			$arr[$item['product_nm']][$item['product_size']]['cnt'] = $arr[$item['product_nm']][$item['product_size']]['cnt'] + $cnt;

			if ($item['cafe'] === GONGCHA) {
				$hot = $item['hot'] === '1' ? 'HOT' : 'ICED:' . $item['ice'];
				$temp = array($hot, '당도:' . $item['sweet']);
				if (!empty($item['comment'])) {
					$temp[] = $item['comment'];
				}
				$comment = join('/', $temp);
			} else if ($item['cafe'] === TWOSOME) {
				$comment = $item['comment'] . $item['hot'] === '1' ? 'HOT' : 'ICED';
			} else {
				$comment = $item['comment'];
			}
			$arr[$item['product_nm']][$item['product_size']]['comment'][] = !empty($comment) ? masking($item['name']) . ' : ' . $comment : masking($item['name']);
		}

		return $this->load->view('view', array('status' => 200, 'data' => array('order' => $arr, 'total' => $total, 'ordnum' => $ordnum, 'size' => $config[$cafe]['size'])));
	}


	/**
	 * 회원별 주문서 쿼리로
	 * @return object|string
	 */
	public function orderprint()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);
		$ordnum = $this->input->get('ordnum');

		if (empty($SES_USER)) {
			return $this->load->view('view', array('status' => 400, 'data' => '로그인 해주세요.'));
		}
		$cafe =  $this->Order_model->get_cafe(array('ordnum' => $ordnum));
		if (empty($cafe)) {
			return $this->load->view('view', array('status' => 400, 'data' => '카페 코드가 없습니다.'));
		}
		$order = $this->Order_model->order_print(array('ordnum' => $ordnum, 'cafe' => $cafe));

		if (empty($order)) {
			return $this->load->view('view', array('status' => 400, 'data' => '주문해주세요.'));
		}

		$config = $this->config->item('cafe');

		$pickup = $this->Pickup_model->get_volunteer(array('ordnum' => $ordnum));

		array_walk($pickup, function (&$item) {
			$item['member_name'] = masking($item['member_name']);
		});

		$volunteer = join(', ', array_column($pickup, 'member_name'));

		return $this->load->view('view', array('status' => 200, 'data' => array('order' => $order, 'total' => $order['total'], 'ordnum' => $ordnum, 'size' => $config[$cafe]['size'], 'pickup' => $volunteer)));
	}


	/**
	 * 주문하기
	 * todo:: order table 단독으로
	 * @return object|string
	 */
	public function set()
	{
		$code = $this->input->post('menu_code');
		$size = $this->input->post('size');
		$cnt = $this->input->post('cnt');
		$hot = $this->input->post('hot');
		$ice = $this->input->post('ice');
		$sweet = $this->input->post('sweet');
		$comment = $this->input->post('comment');
		$ordnum = $this->input->post('ordnum');
		$menu_nm = $this->input->post('menu_nm');

		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER)) {
			return $this->load->view('json', array('status' => 400, 'data' => '로그인 해주세요.'));
		}
		if (empty($ordnum)) {
			return $this->load->view('json', array('status' => 400, 'data' => '주문번호가 없습니다.'));
		}
		if (empty($code)) {
			return $this->load->view('json', array('status' => 400, 'data' => '메뉴를 입력해주세요.'));
		}
		if (empty($size)) {
			return $this->load->view('json', array('status' => 400, 'data' => '사이즈를 입력해주세요.'));
		}
		if (intval($cnt) > 5) {
			return $this->load->view('json', array('status' => 400, 'data' => '최대 5개까지 선택 가능합니다.'));
		}
		if (intval($cnt) > 1) {
			$cnt = 1;
		}
		if (empty($hot)) {
			$hot = '0'; //0 ice, 1 hot
		}
		if (empty($ice)) {
			$ice = 'Regular'; //L less, R regular, F full
		}

		//다중 중복 주문 체크
		$dupl = $this->Order_model->check(array('ordnum' => $ordnum, 'member_name' => $SES_USER['name']));
		if ($dupl > 0) {
			return $this->load->view('json', array('status' => 400, 'data' => '중복하여 주문할 수 없습니다.'));
		}

		$menu = $this->Drink_model->select(array('product_cd' => $code));

		if (empty($menu)) {
			return $this->load->view('json', array('status' => 400, 'data' => '일치하는 메뉴가 없습니다.'));
		}

		$buyer = $this->Buyer_model->select(array('ordnum' => $ordnum, 'now' => true));

		if (empty($buyer)) {
			return $this->load->view('json', array('status' => 400, 'data' => '생성된 주문이 없습니다.'));
		}

		$param = array(
			'ordnum' => $buyer[0]['ordnum'],
			'status' => '1',
			'member_name' => $SES_USER['name'],
			'product_cd' => $code,
			'product_nm' => $menu_nm,
			'product_size' => $size,
			'product_cnt' => empty($cnt) ? 1 : intval($cnt),
			'hot' => $hot,
			'ice' => $ice,
			'sweet' => intval($sweet),
			'comment' => $comment
		);

		$order = $this->Order_model->select(array('ordnum' => $buyer[0]['ordnum'], 'member_name' => $SES_USER['name']));

		if (!empty($order)) {
			$result = $this->Order_model->update($param);
		} else {
			$result = $this->Order_model->insert($param);
		}

		if (!$result) {
			return $this->load->view('json', array('status' => 400, 'data' => '주문 실패'));
		}

		return $this->load->view('json', array('status' => 200, 'data' => array('info' => $param, 'msg' => '주문 성공')));

	}


	/**
	 * 주문 시작
	 * @return object|string
	 */
	public function start()
	{
		$invite = $this->input->post('invite');
		$end_time = $this->input->post('end_time');
		$comment = $this->input->post('comment');
		$option = $this->input->post('option');
		$pickup = $this->input->post('pickup');
		$cafe = $this->input->post('cafe');

		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$admin = $this->config->item('admin');

		if (!(in_array($SES_USER['name'], $admin['member']))) {
			return $this->load->view('json', array('status' => 400, 'data' => '주문을 생성할 권한이 없습니다.'));
		}
		if (empty($invite)) {
			return $this->load->view('json', array('status' => 400, 'data' => '초대 그룹을 입력해주세요.'));
		}
		if (empty($cafe)) {
			return $this->load->view('json', array('status' => 400, 'data' => '카페를 입력해주세요.'));
		}
		if (empty($end_time)) {
			return $this->load->view('json', array('status' => 400, 'data' => '종료시간을 입력해주세요.'));
		}
		if (empty($comment)) {
			return $this->load->view('json', array('status' => 400, 'data' => '코멘트를 입력해주세요.'));
		}
		if (empty($pickup)) {
			$pickup = '0';
		}


		if (!preg_match('/\d+:\d+/', $end_time)) {
			return $this->load->view('json', array('status' => 400, 'data' => '시간 형식이 맞지 않습니다.'));
		}

		$start_time =  date('Y-m-d H:i:s');
		$end_time = date('Y-m-d ') . $end_time . ':00';

		if ($start_time > $end_time) {
			return $this->load->view('json', array('status' => 400, 'data' => '시작 시간이 종료 시간보다 큽니다.'));
		}

//		$buyer = $this->Buyer_model->select(array('now' => true));
//		if (count($buyer) > 0) {
//			return $this->load->view('json', array('status' => 400, 'data' => '생성된 주문이 존재합니다. 아직 주문이 완료되지 않았습니다.'));
//		}

		$config = $this->config->item('cafe');
		$file_name_drink = $config[$cafe]['file_name'];
		$file_name_mmbr = '/tmp/member.log';
		$period = strtotime('-1 hour');

		if (!is_file($file_name_drink)) {
			return $this->load->view('json', array('status' => 400, 'data' => '음료 데이터를 생성하십시오.'));
		}
		if (!is_file($file_name_mmbr)) {
			return $this->load->view('json', array('status' => 400, 'data' => '사원 데이터를 생성하십시오.'));
		}

		$msg = '';
		if (filemtime($file_name_drink) < $period) {
			$this->Drink_model->fetch($cafe);
			$msg .= 'drinks updated';
		}
		if (filemtime($file_name_mmbr) < $period) {
			$this->Member_model->fetch();
			$msg .= '/members updated';
		}

		$param = array(
			'ordnum' => uniqid(),
			'invite' => join(',', $invite),
			'cafe' => $cafe,
			'start' => $start_time,
			'end' => $end_time,
			'comment' => $comment,
			'creator' => $SES_USER['name'],
			'option' => $option, // 0 : 옵션 안 받기, 1 : 옵션 받기
			'pickup' => $pickup, // 0 : 미노출, 1 : 노출
		);

		$this->Buyer_model->insert($param);

		return $this->load->view('json', array('status' => 200, 'data' => '주문이 생성 되었습니다. ' . $msg));
	}

	/**
	 * 주문 삭제
	 * @return object|string
	 */
	public function delete()
	{
		$ordnum = $this->input->post('ordnum');

		if ($this->Buyer_model->delete(array('ordnum' => $ordnum))) {
			return $this->load->view('json', array('status' => 200, 'data' => '삭제 되었습니다.'));
		}
		return $this->load->view('json', array('status' => 400, 'data' => '삭제 실패'));
	}

	/**
	 * 주문 수정
	 * @return object|string
	 */
	public function edit()
	{
		$ordnum = $this->input->post('ordnum');
		$invite = $this->input->post('name');
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		$comment = $this->input->post('comment');

		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($ordnum)) {
			return $this->load->view('json', array('status' => 400, 'data' => '주문번호가 없습니다.'));
		}

		if (!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})$/', $start)) {
			return $this->load->view('json', array('status' => 400, 'data' => '날짜 형식을 확인해주세요.'));
		}
		if (!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})$/', $end)) {
			return $this->load->view('json', array('status' => 400, 'data' => '날짜 형식을 확인해주세요.'));
		}

		$admin = $this->config->item('admin');
		if (!(in_array($SES_USER['name'], $admin['member']))) {
			return $this->load->view('json', array('status' => 400, 'data' => '권한이 없습니다.'));
		}

		$param = array(
			'ordnum' => $ordnum,
			'invite' => $invite,
			'comment' => $comment,
			'start' => $start,
			'end' => $end
		);

		if ($this->Buyer_model->update($param)) {
			return $this->load->view('json', array('status' => 200, 'data' => '수정 되었습니다.'));
		}
		return $this->load->view('json', array('status' => 400, 'data' => '수정 내역이 없습니다.'));
	}
}
