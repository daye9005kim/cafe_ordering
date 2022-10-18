<?php

/**
 * 픽업
 * Class Pickup_model
 */
class Pickup_model extends CI_Model
{
    /**
     * Pickup_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

	/**
	 * 당첨자 뽑기
	 * @param $param
	 * @return array
	 */
	public function set_random($param)
	{
		if (empty($param['ordnum'])) {
			return array();
		}
		if (empty($param['limit'])) {
			return array();
		}

		$escape = $this->db->escape($param);

		$sql = <<<SQL
SELECT 
    ordnum, member_name
FROM
    pickup
WHERE
    ordnum = {$escape['ordnum']}
AND volunteer = '0'
ORDER BY RAND()
LIMIT {$param['limit']}
SQL;
//		echo $sql;
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		foreach ($rows as $row) {
			$this->set(
				array(
					'ordnum' => $row['ordnum'],
					'member_name' => $row['member_name'],
				)
			);
		}
		return $rows;
	}

	/**
	 * 조회
	 * @param $param
	 * @return array
	 */
	public function select($param)
	{
		if (empty($param['ordnum'])) {
			return array();
		}
		if (empty($param['member_name'])) {
			return array();
		}

		$escape = $this->db->escape($param);

		$sql = <<<SQL
SELECT 
    ordnum, member_name
FROM
    pickup
WHERE
     ordnum = {$escape['ordnum']}
 AND member_name = {$escape['member_name']}
SQL;
		//echo $sql;
		$query = $this->db->query($sql);
		return $query->row_array();
	}


	/**
     * 당첨자 조회
     * @param $param
     * @return array
     */
    public function get_volunteer($param)
    {
		if (empty($param['ordnum'])) {
			return array();
		}

        $escape = $this->db->escape($param);

        $sql = <<<SQL
SELECT 
    ordnum, member_name, volunteer, regdate
FROM
    pickup
WHERE
    ordnum = {$escape['ordnum']}
  AND volunteer = '1'
ORDER BY member_name
SQL;
        //echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
    }

	/**
	 * insert update
	 * @param $param
	 * @return bool
	 */
	public function set($param)
	{
		if (empty($param['ordnum'])) {
			return false;
		}
		if (empty($param['member_name'])) {
			return false;
		}

		$escape = $this->db->escape($param);

		$sql = <<<SQL
INSERT INTO `pickup` SET 
ordnum = {$escape['ordnum']},
member_name = {$escape['member_name']},
regdate = NOW()
ON DUPLICATE KEY UPDATE
volunteer = '1',
regdate = NOW()
SQL;
		$this->db->query($sql);
		if ($this->db->affected_rows()) {
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
		if (empty($param['ordnum'])) {
			return false;
		}
		if (empty($param['member_name'])) {
			return false;
		}

		$escape = $this->db->escape($param);

		$sql = <<<SQL
DELETE FROM `pickup` 
WHERE
	  ordnum = {$escape['ordnum']}
  AND member_name = {$escape['member_name']}
  AND volunteer = '0'
SQL;
		$this->db->query($sql);
		if ($this->db->affected_rows()) {
			return true;
		}
		return false;
	}

    /**
     * 픽업 테이블
     * @return bool
     */
    public function create()
    {

        $sql = <<<SQL
CREATE TABLE `pickup` (
  `ordnum` char(13) NOT NULL,
  `member_name` varchar(50) NOT NULL,
  `volunteer` char(1) NOT NULL DEFAULT '0' COMMENT '0:희망, 1:당첨',
  `regdate` datetime NOT NULL,
  PRIMARY KEY (`ordnum`,`member_name`),
  KEY `ordnum` (`ordnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;

        if ($this->db->simple_query($sql)) {
            return true;
        }
        return false;
    }

}
