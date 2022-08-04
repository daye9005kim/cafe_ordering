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
	 * @return string
	 */
	public function get_cafe($param)
	{
		if (empty($param['ordnum'])) {
			return '';
		}

		$escape = $this->db->escape($param);

		$sql = <<<SQL
SELECT cafe 
FROM `buyer` 
WHERE ordnum = {$escape['ordnum']}
SQL;
		//echo $sql;
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return $row['cafe'];
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
		$limit = '';
		$orderby = '';

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
			$orderby = 'o.num DESC,';
            $limit = 'LIMIT 10';
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
        if (isset($param['cafe'])) {
            $arr[] = sprintf('s.cafe = %s', $escape['cafe']);
        }

        if (count($arr) < 1) {
            return array();
        }
		$where = 'WHERE ' . join(' AND ', $arr);

        $sql = <<<SQL
SELECT o.num, o.ordnum, o.status, o.product_cd, o.product_size, o.product_cnt, o.hot, o.ice, o.sweet, o.comment, o.regdate,
       m.name, m.pos, m.dept, m.team, m.part, 
       s.product_nm, s.cafe 
FROM `order` AS o 
INNER JOIN member as m ON o.member_name = m.name
JOIN drink AS s ON o.product_cd = s.product_cd
{$where}
ORDER BY {$orderby} o.product_cd DESC
{$limit}
SQL;
        //echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

	/**
	 * 수정
	 * @param $param
	 * @return bool
	 */
	public function update($param)
	{
		if (empty($param['ordnum'])) {
			return false;
		}
		if (empty($param['member_name'])) {
			return false;
		}
		if (empty($param['product_cd'])) {
			return false;
		}
		if (empty($param['product_nm'])) {
			return false;
		}
		if (empty($param['product_cnt'])) {
			return false;
		}
		if (empty($param['product_size'])) {
			return false;
		}
		if (empty($param['hot'])) {
			$param['hot'] = '0';
		}
		if (empty($param['ice'])) {
			$param['ice'] = 'Regular';
		}
		if (empty($param['sweet'])) {
			$param['sweet'] = 0;
		}
		if (empty($param['comment'])) {
			$param['comment'] = null;
		}

		$escape = $this->db->escape($param);

		$arr = array();

		$arr[] = sprintf('ordnum = %s', $escape['ordnum']);
		$arr[] = sprintf('member_name = %s', $escape['member_name']);

		$where = 'WHERE ' . join(' AND ', $arr);


		$sql = <<<SQL
UPDATE `order` SET 
status = {$escape['status']},
product_cd = {$escape['product_cd']},
product_nm = {$escape['product_nm']},
product_cnt = {$escape['product_cnt']},
product_size = {$escape['product_size']},
hot = {$escape['hot']},
ice = {$escape['ice']},
sweet = {$escape['sweet']},
comment = {$escape['comment']},
regdate = now()
{$where}
SQL;
		$this->db->query($sql);
		if ($this->db->affected_rows()) {
			return true;
		}
		return false;
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
        if (empty($param['product_nm'])) {
            return false;
        }
        if (empty($param['product_cnt'])) {
            return false;
        }
        if (empty($param['product_size'])) {
            return false;
        }
		if (empty($param['hot'])) {
			$param['hot'] = '0';
		}
		if (empty($param['ice'])) {
			$param['ice'] = 'Regular';
		}
		if (empty($param['sweet'])) {
			$param['sweet'] = 0;
		}
        if (empty($param['comment'])) {
            $param['comment'] = null;
        }

        $escape = $this->db->escape($param);
        $sql = <<<SQL
INSERT INTO `order` SET 
ordnum = {$escape['ordnum']},
status = {$escape['status']},
member_name = {$escape['member_name']},
product_cd = {$escape['product_cd']},
product_nm = {$escape['product_nm']},
product_cnt = {$escape['product_cnt']},
product_size = {$escape['product_size']},
hot = {$escape['hot']},
ice = {$escape['ice']},
sweet = {$escape['sweet']},
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
   `product_cd` varchar(50) DEFAULT NULL,
   `product_nm` varchar(50) DEFAULT NULL,
   `product_cnt` tinyint(4) DEFAULT '0',
   `product_size` varchar(20) DEFAULT NULL,
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

	/**
	 * 다중 주문 체크
	 * @param $param
	 * @return int
	 */
	public function check($param)
	{
		if (empty($param['member_name'])) {
			return 0;
		}
		if (empty($param['ordnum'])) {
			return 0;
		}

		$escape = $this->db->escape($param);
		$arr = array();

		if (isset($param['ordnum'])) {
			$arr[] = sprintf('o.ordnum != %s', $escape['ordnum']);
		}
		if (isset($param['status'])) {
			$arr[] = sprintf('o.status = %s', $escape['status']);
		}
		if (isset($param['member_name'])) {
			$arr[] = sprintf('o.member_name = %s', $escape['member_name']);
		}

		if (count($arr) < 1) {
			return 0;
		}
		$where = 'AND ' . join(' AND ', $arr);

		$sql = <<<SQL
SELECT o.ordnum,o.member_name FROM `order`AS o 
JOIN buyer AS b ON o.ordnum = b.ordnum 
WHERE NOW() BETWEEN `start` AND `end`
{$where}
SQL;
		//echo $sql;
		$query = $this->db->query($sql);
		return  $query->num_rows();
	}

	/**
	 * 주문서 출력하기
	 * @return array
	 */
	function order_print($param) {
		if (empty($param['ordnum'])) {
			return array();
		}
		$escape = $this->db->escape($param);

		$sql = <<<SQL
SELECT 
    o.product_cd, s.product_nm, o.product_size,
    CASE 
		WHEN s.cafe = '01' THEN GROUP_CONCAT(concat_ws('-', MASK_NAME(m.name),IF(o.hot = 1, 'HOT', concat('ICED:', o.ice)), CONCAT('당도:',o.sweet), o.comment) SEPARATOR '||')
		WHEN s.cafe = '06' THEN GROUP_CONCAT(concat_ws(' : ', MASK_NAME(m.name),IF(o.hot = 1, 'HOT', 'ICED'), o.comment) ORDER BY o.comment DESC SEPARATOR '||')
        ELSE GROUP_CONCAT(concat_ws(' : ', MASK_NAME(m.name), o.comment) ORDER BY o.comment DESC SEPARATOR '||')
    END as comments,
    sum(o.product_cnt) as cnt,
    s.cafe
FROM `order` AS o
JOIN member AS m ON o.member_name = m.name
JOIN drink AS s ON o.product_cd = s.product_cd AND s.cafe = {$escape['cafe']} 
WHERE
    o.ordnum = {$escape['ordnum']}
GROUP BY o.product_cd, o.product_size
ORDER BY o.product_cd DESC
SQL;

//		echo $sql;
		$query = $this->db->query($sql);

		$return = array();
		$size = array();
		$total = 0;

		$config = $this->config->item('cafe');

		foreach ($query->result_array() as $row) {
			$total += (int)$row['cnt'];
			$comments = explode('||', $row['comments']);

			if (!isset($size[$row['product_cd']][$row['product_size']])) {
				$size[$row['product_cd']] = $config[$row['cafe']]['size'];
			}
			$size[$row['product_cd']][$row['product_size']]['cnt'] = $row['cnt'];
			$size[$row['product_cd']][$row['product_size']]['comment'] = $comments;

			$return[$row['product_cd']] = array(
			'menu' => $row['product_nm'],
			'size' => $size[$row['product_cd']],
			'cafe' => $row['cafe'],
			);
		}
		unset($size);
		if ($total > 0) {
			$return['total'] = $total;
		}
		return $return;
	}

}
