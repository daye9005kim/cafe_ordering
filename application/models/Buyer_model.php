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
SELECT ordnum, member_name, start, `end`, comment, regdate 
FROM buyer
{$where}
SQL;
        echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}