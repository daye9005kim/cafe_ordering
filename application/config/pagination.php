<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$config['reuse_query_string'] = TRUE;
$config['first_link'] = '&laquo;';
$config['last_link'] = '&raquo;';
$config['next_link'] = FALSE;
$config['prev_link'] = FALSE;
$config['per_page'] = 20;
$config['base_url'] = '//' . $_SERVER['HTTP_HOST'] . $CI->uri->slash_segment(1, 'both') . $CI->uri->slash_segment(2);
$config['uri_segment'] = 3;
$config['full_tag_open'] = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
$config['num_tag_open'] = '<li class="page-item">';
$config['num_tag_close'] = '</li>';
$config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
$config['cur_tag_close'] = '</a></li>';
$config['attributes'] = array('class' => 'page-link');
$config['full_tag_close'] = '</ul></nav>';
