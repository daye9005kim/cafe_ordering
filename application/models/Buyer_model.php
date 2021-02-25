<?php

/**
 * 쏘는 사람
 * Class Buyer_model
 */
class Buyer_model extends CI_Model
{
    /**
     * Buyer_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 조회
     * @param $param
     * @return array
     */
    public function select($param)
    {
        $escape = $this->db->escape($param);
        $arr = array();
        if (isset($param['now']) && $param['now'] === true) {
            $arr[] = 'NOW() between `start` and `end`';
        }
		if (isset($param['interval'])) {
			$arr[] = sprintf('`end` >= DATE_SUB(NOW(), INTERVAL %s DAY)', $escape['interval']);
		}

        if (isset($param['member_name'])) {
            $arr[] = sprintf('member_name = %s', $escape['member_name']);
        }
        if (isset($param['ordnum'])) {
            $arr[] = sprintf('ordnum = %s', $escape['ordnum']);
        }

        $where = '';
        if (count($arr) > 0) {
            $where = 'WHERE ' . join(' AND ', $arr);
        } else {
            return array();
        }

        $sql = <<<SQL
SELECT ordnum, member_name, start, `end`, comment, `option`, regdate 
FROM buyer
{$where}
ORDER BY regdate DESC
SQL;
//        echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 입력
     * @param $param
     * @return bool
     */
    public function insert($param)
    {
        if (empty($param['ordnum'])) {
            return false;
        }
        if (empty($param['member_name'])) {
            return false;
        }
        if (empty($param['start'])) {
            return false;
        }
        if (empty($param['end'])) {
            return false;
        }
        if (empty($param['comment'])) {
            return false;
        }
        if (empty($param['option'])) {
            $param['option'] = '0';
        }

        $escape = $this->db->escape($param);
        $sql = <<<SQL
INSERT INTO buyer SET 
ordnum = {$escape['ordnum']},
member_name = {$escape['member_name']},
start = {$escape['start']},
`end` = {$escape['end']},
comment = {$escape['comment']},
`option` = {$escape['option']},
regdate = now()     
SQL;
        $this->db->query($sql);
        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    /**
     * 쏘는 사람 테이블
     * @return bool
     */
    public function create()
    {

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `buyer` (
   `ordnum` char(13) NOT NULL,
   `member_name` varchar(50) NOT NULL,
   `start` datetime NOT NULL,
   `end` datetime NOT NULL,
   `comment` text NOT NULL,
   `option` char(1) NOT NULL DEFAULT '0',
   `regdate` datetime NOT NULL,
   PRIMARY KEY (`ordnum`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        if ($this->db->simple_query($sql)) {
            return true;
        }
        return false;
    }

}
