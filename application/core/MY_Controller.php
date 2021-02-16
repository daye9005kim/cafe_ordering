<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class MY_Controller
 * 코어 확장
 */
class MY_Controller extends CI_Controller
{
    public $db;

    /**
     * MY_Controller constructor.
     */
    function __construct()
    {
        parent::__construct();

        $appdb = 'default';
        if ($_SERVER['HTTP_HOST'] === 'superglue-dttks.run.goorm.io') {
            $appdb = 'superglue4';
        } else if ($_SERVER['HTTP_HOST'] === 'jasoncafe-ghebk.run.goorm.io') {
			$appdb = 'jasoncafe';
		} else if ($_SERVER['HTTP_HOST'] === 'starbucks-qmtuw.run.goorm.io') {
			$appdb = 'dayecafe';
		}

        $this->db = $this->load->database($appdb, true);

		// $this->session->unset_userdata('');
//		$this->session->sess_destroy(); die();

		$user = $this->input->get_post('user');
		if (empty($user)) {
			return $this->load->view('view', array('status' => 400, 'data' => '당신의 이름은 무엇입니까?'));
		}

		$SES_USER = $this->session->userdata($user);
		if(empty($SES_USER)) {
			$this->session->set_userdata($user, array('part' => '개발팀', 'name' => $user));
		}

		if (!isset($SES_USER['dept'])) {
			$usr = $this->Member_model->select(array('name' => $user));
			if (empty($usr)) {
				return $this->load->view('view', array('status' => 400, 'data' => '사원 정보가 없습니다.'));
			}
			$this->session->set_userdata($user, array('name' => $usr[0]['name'], 'pos' => $usr[0]['pos'], 'dept' => $usr[0]['dept'], 'team' => $usr[0]['team'], 'part' => $usr[0]['part']));
		}
		$SES_USER = $this->session->userdata($user);

		$_POST['name'] = $SES_USER['name'];
		$_POST['pos'] = $SES_USER['pos'];
		$_POST['dept'] = $SES_USER['dept'];
		$_POST['team'] = $SES_USER['team'];
		$_POST['part'] = $SES_USER['part'];

		return true;
    }


    /**
     *
     */
    function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }
}
