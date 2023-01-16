<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class About_us extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
    function index(){
    	$this->load->model("HomeModel");
		$data['active_about_us'] = "active";
		$data['page_name']       = "Tentang Kami";
		render('about_us',$data);
	}
}