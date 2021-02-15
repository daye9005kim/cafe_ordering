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
