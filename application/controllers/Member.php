<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Member extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->helper('url');
		redirect('/member/login');
	}

	public function login()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$buyer = $this->Buyer_model->select(array('now' => true));

		if (count($buyer) > 0) {
			$team = explode(',', $buyer[0]['invite']);
			$buyer[0]['invite'] = strToTeam($team);
		}

		$str = '해당하는 주문을 선택하세요.';
		if (empty($buyer)) {
			$admin = $this->config->item('admin');
			if (in_array($SES_USER['name'], $admin['member'])) {
				return $this->load->view('view', array('status' => 400, 'url' => '/admin', 'data' => '당신은 관리자. 주문을 생성하세요.'));
			}
			$str = '생성된 주문이 없습니다. 관리자에게 문의하세요.';
			$buyer = array(array(
				'ordnum' => '',
				'invite' => '',
				'comment' => '',
				'creator' => '',
				'start' => '',
				'end' => ''
			));
		}

//		if (!empty($SES_USER['dept'])) {
//			return $this->load->view('view', array('status' => 308, 'url' => '/order', 'data' => ''));
//		}

		$members = $this->Member_model->select();
		$list = array();
		foreach ($members as $value) {
			$list[] = $value['name'];
		}
		return $this->load->view('view', array('status' => 200, 'data' => array('member' => $list, 'order_list' => $buyer, 'str' => $str)));
	}


	public function login_ok()
	{
		$user = $this->input->post('user');
		$SES_KEY = $this->input->post('KEY');

		if (empty($user)) {
			return $this->load->view('json', array('status' => 400, 'data' => '이름이 없습니다.'));
		}
		$SES_USER = $this->session->userdata($SES_KEY);

		if (empty($SES_USER)) {
			$this->session->set_userdata($SES_KEY, array('name' => $user, 'pos' => '', 'dept' => '', 'team' => '', 'part' => ''));
		}

//		if (empty($SES_USER['dept'])) { //세션 유지하는 if문
		//세션 덮어쓰기
		$usr = $this->Member_model->select(array('name' => $user));
		if (empty($usr)) {
			return $this->load->view('json', array('status' => 400, 'data' => '사원 정보가 없습니다.'));
		}
		$this->session->set_userdata($SES_KEY, array('name' => $usr[0]['name'], 'pos' => $usr[0]['pos'], 'dept' => $usr[0]['dept'], 'team' => $usr[0]['team'], 'part' => $usr[0]['part']));
		$SES_USER = $this->session->userdata($SES_KEY);
//		}

		return $this->load->view('json', array('status' => 200, 'data' => array('name' => $SES_USER['name'])));
	}

	public function logout()
	{
		$SES_KEY = $this->input->post('KEY');
		$this->session->unset_userdata($SES_KEY);
		return $this->load->view('view', array('status' => 308, 'url' => '/member/login', 'data' => ''));
	}

	public function get($page = 0)
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$admin = $this->config->item('admin');
		if (!(in_array($SES_USER['name'], $admin['member']))) {
			return $this->load->view('view', array('status' => 400, 'data' => '권한이 없습니다.'));
		}

		$name = $this->input->get('name');
		$team = $this->input->get('team');
		$pos= $this->input->get('pos');
		$dept = $this->input->get('dept');
		$part = $this->input->get('part');

		$param = array();
		if (!empty($name)) {
			$param['name'] = $name;
		}
		if (!empty($team)) {
			$param['team'] = $team;
		}
		if (!empty($pos)) {
			$param['pos'] = $pos;
		}
		if (!empty($dept)) {
			$param['dept'] = $dept;
		}
		if (!empty($part)) {
			$param['part'] = $part;
		}

		if (empty($page)) {
			$page = 0;
		}
		$this->load->library('pagination');
		$param['start'] = $page;
		$param['limit'] = $this->pagination->per_page;

		$members = $this->Member_model->select($param);
		$config['total_rows'] = $this->Member_model->total_rows($param);
		$this->pagination->initialize($config);

		$team_list = $this->Member_model->team();
		$part_list = $this->Member_model->part();
		return $this->load->view('view', array('status' => 200, 'data' => $members, 'pagination' => array('total_rows' => $config['total_rows']), 'team' => $team_list, 'part' => $part_list));
	}

	public function delete()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$admin = $this->config->item('admin');
		if (!(in_array($SES_USER['name'], $admin['member']))) {
			return $this->load->view('json', array('status' => 400, 'data' => '권한이 없습니다.'));
		}

		$name = $this->input->post('name');

		if (empty($name)) {
			return $this->load->view('json', array('status' => 400, 'data' => '이름이 없습니다.'));
		}

		if ($this->Member_model->delete(array('name' => $name))) {
			return $this->load->view('json', array('status' => 200, 'data' => '삭제 하였습니다.'));
		}
		return $this->load->view('json', array('status' => 400, 'data' => '삭제 실패하였습니다.'));

	}
	public function insert()
	{
		$SES_KEY = $this->input->post('KEY');
		$SES_USER = $this->session->userdata($SES_KEY);

		$admin = $this->config->item('admin');
		if (!(in_array($SES_USER['name'], $admin['member']))) {
			return $this->load->view('json', array('status' => 400, 'data' => '권한이 없습니다.'));
		}

		$name = $this->input->get_post('name');
		$pos = $this->input->get_post('pos');
		$dept = $this->input->get_post('dept');
		$team = $this->input->get_post('team');
		$part = $this->input->get_post('part');

		if (empty($name)) {
			return $this->load->view('json', array('status' => 400, 'data' => '이름이 없습니다.'));
		}
		if (empty($pos)) {
			return $this->load->view('json', array('status' => 400, 'data' => '직급이 없습니다.'));
		}
		if (empty($dept)) {
			return $this->load->view('json', array('status' => 400, 'data' => '부서가 없습니다.'));
		}
		if (empty($team)) {
			return $this->load->view('json', array('status' => 400, 'data' => '팀이 없습니다.'));
		}
		if (empty($part)) {
			return $this->load->view('json', array('status' => 400, 'data' => '파트가 없습니다.'));
		}

		$param = array(
			'name' => $name,
			'pos' => $pos,
			'dept' => $dept,
			'team' => $team,
			'part' => $part
		);
		if (!$this->Member_model->insert($param)) {
			return $this->load->view('json', array('status' => 400, 'data' => '사원 추가에 실패하였습니다.'));
		}
		return $this->load->view('json', array('status' => 200, 'data' => $name . '님이 추가 되었습니다.'));
	}

}
