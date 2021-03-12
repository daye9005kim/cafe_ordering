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
		$str = '';
		$msg = array('buyer'=>'','time'=>'');
		if (empty($buyer)) {
			$admin = $this->config->item('admin');
			if (in_array($SES_USER['name'], $admin['member'])) {
				return $this->load->view('view', array('status' => 400, 'url' => '/admin','data' => '당신은 관리자. 구매자를 생성하세요.'));
			}
			$str = '구매자가 없습니다. 관리자에게 문의하세요.';
			echo $str;
			$buyer = array(array(
				'member_name' => '',
				'comment' => '',
				'start' => '',
				'end' => ''
			));
		}
		if (empty($str)) {
			$msg['buyer'] = sprintf('%s님이 생성한 주문 %s<br>', $buyer[0]['member_name'], $buyer[0]['comment']);
			$msg['time'] = sprintf('주문기한 : %s ~ %s', date('Y-m-d H:i', strtotime($buyer[0]['start'])),date('Y-m-d H:i', strtotime($buyer[0]['end'])));
		}

		if (!empty($SES_USER['dept'])) {
			return $this->load->view('view', array('status' => 308, 'url' => '/order', 'data' => ''));
		}

		$members = $this->Member_model->select();
		$list = array();
		foreach ($members as $value) {
			$list[] = $value['name'];
		}
		return $this->load->view('view', array('status' => 200, 'data' => array('member' => $list, 'msg' => $msg)));
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

		if (empty($SES_USER['dept'])) {
			$usr = $this->Member_model->select(array('name' => $user));
			if (empty($usr)) {
				return $this->load->view('json', array('status' => 400, 'data' => '사원 정보가 없습니다.'));
			}
			$this->session->set_userdata($SES_KEY, array('name' => $usr[0]['name'], 'pos' => $usr[0]['pos'], 'dept' => $usr[0]['dept'], 'team' => $usr[0]['team'], 'part' => $usr[0]['part']));
			$SES_USER = $this->session->userdata($SES_KEY);
		}

		return $this->load->view('json', array('status' => 200, 'data' => array('name' => $SES_USER['name'])));
	}

	public function logout()
	{
		$SES_KEY = $this->input->post('KEY');
		$this->session->unset_userdata($SES_KEY);
		return $this->load->view('view', array('status' => 308, 'url' => '/member/login', 'data' => ''));
	}
}
