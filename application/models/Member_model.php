<?php

/**
 * 회원
 * Class Member_model
 */
class Member_model extends CI_Model
{
    /**
     * Member_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 회원 긁어오기
     * @return int
     */
    public function fetch()
    {
        $success = 0;
        //테이블 만들기
        $this->create();
        //삭제
        $this->delete();

        $contents = file_get_contents('http://test.jasongroup.co.kr/main/jasonCafe.html', false, stream_context_create(array('http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => array()
        ))));

        $member = json_decode($contents, true);
        foreach ($member as $row) {
            if ($this->insert($row)) {
                $success++;
            }
        }
        return $success;
    }

    /**
     * 테이블 생성
     * @return bool
     */
    private function create()
    {
        $sql = <<<SQL
CREATE TABLE `member` (
  `name` VARCHAR(50) NOT NULL,
  `pos` VARCHAR(50) NOT NULL,
  `dept` VARCHAR(50) NOT NULL,
  `team` VARCHAR(50) NULL DEFAULT '',
  `part` VARCHAR(50) NULL DEFAULT '',
  PRIMARY KEY (`name`),
  INDEX `dept` (`dept` ASC),
  INDEX `team` (`team` ASC)
)
SQL;
        if ($this->db->simple_query($sql)) {
            return true;
        }
        return false;
    }


    /**
     * 테이블 삭제
     * @return bool
     */
    public function delete()
    {
        $sql = <<<SQL
DELETE FROM member
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
        if (empty($param['name'])) {
            return false;
        }
        if (empty($param['pos'])) {
            return false;
        }
        if (empty($param['dept'])) {
            return false;
        }
        if (empty($param['team'])) {
            $param['team'] = '';
        }
        if (empty($param['part'])) {
            $param['part'] = '';
        }

        $escape = $this->db->escape($param);
        $sql = <<<SQL
INSERT INTO member SET 
name = {$escape['name']},
pos = {$escape['pos']},
dept = {$escape['dept']},
team = {$escape['team']},
part = {$escape['part']}     
SQL;
        $this->db->query($sql);
        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

}