<?php

/**
 * 스타벅스 모델
 * Class Starbucks_model
 */
class Starbucks_model extends CI_Model
{
    /**
     * Starbucks_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 조회
     * @param $param
     * @return array()
     */
    public function select($param)
    {
        if (isset($param['content'])) {
            $param['content'] = '%' . $param['content'] . '%';
        }
        $escape = $this->db->escape($param);
        $arr = array();
        if (isset($param['product_cd'])) {
            $arr[] = sprintf('product_cd = %s', $escape['product_cd']);
        }
        if (isset($param['cate_cd'])) {
            $arr[] = sprintf('cate_cd = %s', $escape['cate_cd']);
        }

        if (isset($param['content'])) {
            $arr[] = sprintf('content like %s', $escape['content']);
        }

        $where = '';
        if (count($arr) > 0) {
            $where = 'WHERE ' . join(' AND ', $arr);
        }
        $sql = <<<SQL
SELECT product_cd, product_nm, product_img, cate_nm, cate_cd, content, caffeine, regdate 
FROM drink
{$where}
SQL;
//        echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    /**
     * 스타벅스로부터 drink테이블에 값 넣기
     * @return int
     */
    public function fetch()
    {
        $success = 0;
        //테이블 만들기
        $this->create();
        //삭제
        $this->delete();

        $contents = file_get_contents('https://www.starbucks.co.kr/menu/drink_list.do');
        preg_match_all("/result = \"(W[0-9]+)\"/", $contents, $matches);

        foreach ($matches[1] as $cate_code) {

            $recv = file_get_contents('https://www.starbucks.co.kr/upload/json/menu/' . $cate_code . '.js');
            $memu = json_decode($recv, true);

            if (!isset($memu['list'])) {
                continue;
            }

            foreach ($memu['list'] as $drink) {

                if (empty($drink['product_NM'])) {
                    continue;
                }
                if (strpos($drink['product_NM'], '리저브') !== false) {
                    continue;
                }
                if (strpos($drink['product_NM'], '피지오') !== false) {
                    continue;
                }

                if ($this->insert(array(
                    'product_cd' => $drink['product_CD'],
                    'product_nm' => $drink['product_NM'],
                    'product_img' => $drink['img_UPLOAD_PATH'] . $drink['file_PATH'],
                    'cate_nm' => $drink['cate_NAME'],
                    'cate_cd' => $cate_code,
                    'content' => $drink['content'],
                    'caffeine' => $drink['caffeine'],
                ))) {
                    $success++;
                }
            }
        }
        return $success;
    }

    /**
     * 테이블 생성
     * @return bool
     */
    public function create()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `drink` (
`product_cd` VARCHAR(20) NOT NULL,
`product_nm` VARCHAR(300) NULL,
`product_img` VARCHAR(500) NULL,
`cate_nm` VARCHAR(20) NULL,
`cate_cd` VARCHAR(10) NULL,
`content` TEXT NULL DEFAULT '',
`caffeine` TINYINT NOT NULL DEFAULT 0,
`regdate` DATETIME NULL,
PRIMARY KEY (`product_cd`),
INDEX `product_nm` (`product_nm` ASC),
INDEX `cate_cd` (`cate_cd` ASC),
INDEX `cate_nm` (`cate_nm` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
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
DELETE FROM drink
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
        if (empty($param['product_cd'])) {
            return false;
        }
        if (empty($param['product_nm'])) {
            return false;
        }
        if (empty($param['product_img'])) {
            $param['product_img'] = '';
        }
        if (empty($param['cate_nm'])) {
            return false;
        }
        if (empty($param['cate_cd'])) {
            return false;
        }
        if (empty($param['content'])) {
            $param['content'] = '';
        }
        if (empty($param['caffeine'])) {
            $param['caffeine'] = 0;
        }
        $escape = $this->db->escape($param);
        $sql = <<<SQL
INSERT INTO drink SET 
product_cd = {$escape['product_cd']},  
product_nm = {$escape['product_nm']},  
product_img = {$escape['product_img']}, 
cate_nm = {$escape['cate_nm']},     
cate_cd = {$escape['cate_cd']},     
content = {$escape['content']},     
caffeine = {$escape['caffeine']},    
regdate = now()     
SQL;
        $this->db->query($sql);
        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

}
