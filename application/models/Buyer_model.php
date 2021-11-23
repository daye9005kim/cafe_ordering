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
	 * Total Count
	 * @param $param
	 * @return int
	 */
	public function total_rows($param = array())
	{
		$escape = $this->db->escape($param);
		$arr = array();
		if (isset($param['now']) && $param['now'] === true) {
			$arr[] = 'NOW() between `start` and `end`';
		}
		if (isset($param['interval'])) {
			$arr[] = sprintf('`end` >= DATE_SUB(NOW(), INTERVAL %s DAY)', $escape['interval']);
		}

		if (isset($param['creator'])) {
			$arr[] = sprintf('creator = %s', $escape['creator']);
		}
		if (isset($param['ordnum'])) {
			$arr[] = sprintf('ordnum = %s', $escape['ordnum']);
		}

		$where = '';
		if (count($arr) > 0) {
			$where = 'WHERE ' . join(' AND ', $arr);
		}

		$sql = <<<SQL
SELECT count(*) as count FROM buyer {$where}
SQL;
		$query = $this->db->query($sql);
		$count = $query->row_array();
		return $count['count'];
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

        if (isset($param['creator'])) {
            $arr[] = sprintf('creator = %s', $escape['creator']);
        }
        if (isset($param['ordnum'])) {
            $arr[] = sprintf('ordnum = %s', $escape['ordnum']);
        }

        $where = '';
        if (count($arr) > 0) {
            $where = 'WHERE ' . join(' AND ', $arr);
        }
		$limit = '';
		if (isset($param['limit']) && isset($param['start'])) {
			$limit = sprintf('LIMIT %d, %d', $param['start'], $param['limit']);
		}

        $sql = <<<SQL
SELECT ordnum, invite, start, `end`, comment, `option`, regdate, creator 
FROM buyer
{$where}
ORDER BY regdate DESC
{$limit}
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
        if (empty($param['invite'])) {
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
        if (empty($param['creator'])) {
            return false;
        }
        if (empty($param['option'])) {
            $param['option'] = '0';
        }

        $escape = $this->db->escape($param);
        $sql = <<<SQL
INSERT INTO buyer SET 
ordnum = {$escape['ordnum']},
invite = {$escape['invite']},
start = {$escape['start']},
`end` = {$escape['end']},
comment = {$escape['comment']},
creator = {$escape['creator']},
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
   `invite` varchar(100) DEFAULT NULL,
   `start` datetime NOT NULL,
   `end` datetime NOT NULL,
   `comment` text NOT NULL,
   `option` char(1) NOT NULL DEFAULT '0',
   `creator` varchar(50) DEFAULT NULL,
   `regdate` datetime NOT NULL,
   PRIMARY KEY (`ordnum`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        if ($this->db->simple_query($sql)) {
            return true;
        }
        return false;
    }


	/**
	 * delete
	 * @param $param
	 * @return bool
	 */
	public function delete($param)
	{

		if (!isset($param['ordnum'])) {
			return false;
		}

		$escape = $this->db->escape($param);

		$arr = array();

		if (!empty($param['ordnum'])) {
			$arr[] = sprintf('ordnum = %s', $escape['ordnum']);
		}

		if (count($arr) > 0) {
			$where = join(' AND ', $arr);
		} else {
			return false;
		}

		$sql = <<<SQL
DELETE FROM buyer WHERE {$where}
SQL;
//        echo $sql;
		$this->db->query($sql);
		if ($this->db->affected_rows()) {
			return true;
		}
		return false;
	}


	/**
	 * update
	 * @param $param
	 * @return bool
	 */
	public function update($param)
	{

		if (!isset($param['ordnum'])) {
			return false;
		}

		$escape = $this->db->escape($param);

		$arr = array();
		if (!empty($param['name'])) {
			$arr[] = sprintf('`invite` = %s', $escape['name']);
		}
		if (!empty($param['start'])) {
			$arr[] = sprintf('`start` = %s', $escape['start']);
		}
		if (!empty($param['end'])) {
			$arr[] = sprintf('`end` = %s', $escape['end']);
		}
		if (!empty($param['comment'])) {
			$arr[] = sprintf('`comment` = %s', $escape['comment']);
		}
		if (!empty($param['option'])) {
			$arr[] = sprintf('`option` = %s', $escape['option']);
		}

		if (count($arr) > 0) {
			$update = join(' , ', $arr);
		} else {
			return false;
		}

		$sql = <<<SQL
UPDATE buyer SET 
{$update}
WHERE ordnum = {$escape['ordnum']}
SQL;
		$this->db->query($sql);
		if ($this->db->affected_rows()) {
			return true;
		}
		return false;
	}
}
