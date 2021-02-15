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


    public function fetch()
    {
        $contents = file_get_contents('https://www.starbucks.co.kr/menu/drink_list.do');

        print_r($contents);
    }

}