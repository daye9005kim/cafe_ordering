<?php

/**
 * 주문
 * Class Order_model
 */
class Order_model extends CI_Model
{
    /**
     * Order_model constructor.
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
        if (isset($param['comment'])) {
            $param['comment'] = '%' . $param['comment'] . '%';
        }

        $escape = $this->db->escape($param);
        $arr = array();

        if (isset($param['num'])) {
            $arr[] = sprintf('o.num = %s', $escape['num']);
        }
        if (isset($param['ordnum'])) {
            $arr[] = sprintf('o.ordnum = %s', $escape['ordnum']);
        }
        if (isset($param['status'])) {
            $arr[] = sprintf('o.status = %s', $escape['status']);
        }
        if (isset($param['member_name'])) {
            $arr[] = sprintf('o.member_name = %s', $escape['member_name']);
        }
        if (isset($param['product_cd'])) {
            $arr[] = sprintf('o.product_cd = %s', $escape['product_cd']);
        }
        if (isset($param['product_cnt'])) {
            $arr[] = sprintf('o.product_cnt = %s', $escape['product_cnt']);
        }
        if (isset($param['comment'])) {
            $arr[] = sprintf('o.comment like %s', $escape['comment']);
        }

        if (isset($param['pos'])) {
            $arr[] = sprintf('m.pos = %s', $escape['pos']);
        }
        if (isset($param['dept'])) {
            $arr[] = sprintf('m.dept = %s', $escape['dept']);
        }
        if (isset($param['team'])) {
            $arr[] = sprintf('m.team = %s', $escape['team']);
        }
        if (isset($param['part'])) {
            $arr[] = sprintf('m.part = %s', $escape['part']);
        }

        $where = '';
        if (count($arr) > 0) {
            $where = 'WHERE ' . join(' AND ', $arr);
        } else {
            return array();
        }

        $sql = <<<SQL
SELECT o.num, o.ordnum, o.status, m.name, m.pos, m.dept, m.team, m.part, product_cd, product_cnt, o.comment, o.regdate 
FROM `order` AS o INNER JOIN member as m ON o.member_name = m.name
{$where}
SQL;
        //echo $sql;
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
        if (empty($param['status'])) {
            return false;
        }
        if (empty($param['member_name'])) {
            return false;
        }
        if (empty($param['product_cd'])) {
            return false;
        }
        if (empty($param['product_cnt'])) {
            return false;
        }
        if (empty($param['comment'])) {
            $param['comment'] = '';
        }

        $escape = $this->db->escape($param);
        $sql = <<<SQL
INSERT INTO `order` SET 
ordnum = {$escape['ordnum']}, 
status = {$escape['status']}, 
member_name = {$escape['member_name']}, 
product_cd = {$escape['product_cd']}, 
product_cnt = {$escape['product_cnt']}, 
comment = {$escape['comment']},    
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
CREATE TABLE IF NOT EXISTS `order` (
   `num` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `ordnum` char(13) NOT NULL,
   `status` char(1) DEFAULT '1' COMMENT '1 : 대기, 2 : 완료, 3 : 주문',
   `member_name` varchar(50) DEFAULT NULL,
   `product_cd` varchar(20) DEFAULT NULL,
   `product_cnt` tinyint(4) DEFAULT '0',
   `comment` text,
   `regdate` datetime DEFAULT NULL,
   PRIMARY KEY (`num`),
   KEY `product_cd` (`product_cd`),
   KEY `ordnum` (`ordnum`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        if ($this->db->simple_query($sql)) {
            return true;
        }
        return false;
    }

}