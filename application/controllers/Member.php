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
    	$name = $this->input->post('name');
    	$dept = $this->input->post('dept');

    	if (empty($name)) {
    		return $this->load->view('json', array('status' => 400, 'data' => '이름이 없습니다.'));
		}
		if (empty($dept)) {
			$usr = $this->Member_model->select(array('name' => $name));
			if (empty($usr)) {
				return $this->load->view('json', array('status' => 400, 'data' => '사원 정보가 없습니다.'));
			}
			$this->session->set_userdata($name, array('name' => $usr[0]['name'], 'pos' => $usr[0]['pos'], 'dept' => $usr[0]['dept'], 'team' => $usr[0]['team'], 'part' => $usr[0]['part']));
		}

		print_r($this->session->userdata($name));
		die();
		return $this->load->view('json', array('status' => 200, 'data' => array('msg' => '로그인성공')));
    }

    public function logout()
    {

    }
}
