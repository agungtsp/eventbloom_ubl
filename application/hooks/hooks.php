<?php
/**
 * @file
 */

 /**
  * fungsi utk mengecek autentifikasi 
  */
function auth(){
	$CI		= & get_instance();
	$dir						= $CI->router->directory;
	$class						= $CI->router->fetch_class();
	$base_url					= str_replace('http://'.$_SERVER['HTTP_HOST'],'',base_url());
	$CI->baseUrl				= str_replace('https://'.$_SERVER['HTTP_HOST'],'',$base_url);//jika https
	$CI->currentController 		= $CI->baseUrl.$dir.$class.'/';
	
	menus();
	

}
 /**
  * 
  */
function view(){
	$CI						= & get_instance();
	if(isset($CI->layout) && $CI->layout == 'none'){
		return;
	}
	$dir						= $CI->router->directory;
	$class						= $CI->router->fetch_class();
	$method						= $CI->router->fetch_method();
	$method						= ($method=='index') ? $class : $method;
	$data						= (isset($CI->data)) ? $CI->data : array();
	$base_url					= str_replace('http://'.$_SERVER['HTTP_HOST'],'',base_url());
	$data['base_url']			= str_replace('https://'.$_SERVER['HTTP_HOST'],'',$base_url);//jika https
	$data['this_controller'] 	= $data['base_url'].$dir.$class.'/';
	$data['content']			= $CI->load->view($dir.$class.'/'.$method,$data,true);
	$defaultLayout				= ($dir) ? 'admin' : 'front';
	$layout 					= (isset($CI->layout)) ? $CI->layout : $defaultLayout;
	$CI->load->view('/layout/'.$layout,$data);
}
