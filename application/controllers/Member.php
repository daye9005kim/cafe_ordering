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

	}

	public function login()
	{
		$members = $this->Member_model->select();
		$list = array();
		foreach ($members as $value) {
			$list[] = $value['name'];
		}
		return $this->load->view('view', array('status' => 200, 'data' => array('member' => $list)));
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
		echo 'logout';
	}
}
