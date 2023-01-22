<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * common function
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 */

function is_file_exsist($path,$file){
	$fl = str_replace('//', '/', $path.'/'.$file);
	if(is_file($fl)){
		return $file;
	}
}
function last_query(){
	 $CI 		= & get_instance();
	echo '<pre>'.$CI->db->last_query().'</pre>';
}
function is_file_copy($path,$file){
	$fl = str_replace('//', '/', $path.'/'.$file);
	return $fl;	
}
function upload_file($field,$path='',$allowed_type='*',$max_size=0,$upload_dir=UPLOAD_DIR){
	 $CI 		= & get_instance();
	 $name 		= strtolower($_FILES[$field]['name']);
	 $ext		= end(explode('.',$name));
	 $CI->load->helper(array('form', 'url'));
	 $config['upload_path'] 	= $upload_dir.$path;
	 $config['allowed_types'] 	= $allowed_type;
	 $config['file_name'] 		= url_title(str_replace($ext,'',$name));
	 $config['max_size']		= $max_size;
	 
	 $CI->load->library('upload', $config);
	 if(! $CI->upload->do_upload($field)){
		  return $CI->upload->display_errors(' ', ' ');
	 }
	 else{
		  return $CI->upload->data();
	 }
}

function imagemanager($field='img',$img='',$max_width_cropzoom=277,$max_height_cropzoom=150,$id='',$array='',$name_img=''){
	$CI 			= & get_instance();
	$html['config'] = '
	<div class="modal fade invis" id="popImageManager">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title">Image Manager - Max filesize('.ini_get('upload_max_filesize').')</h4>
					<div class="form-inline">
						<input type="text" id="search-picture" style="width: 50%; height: 100%; padding: 6px 12px; display: inline-block;
						margin-top: 10px;" placeholder="Search...">
						<a class="btn btn-primary" id="search_image_manager"><i class="fa fa-search"></i> Search</a>
					</div>
				</div>
				<div class="modal-body">
					<div class="row-fluid" id="list-image-manager">
						<i class="fa fa-refresh fa-spin"></i> Loading...
					</div>
				</div>
				<span style="margin-left: 3%;">
					<div class="pagination"></div>
				</span>
				<div class="modal-footer">
					<div class="col-md-12">
						<div class="col-md-4">
							<input type="file" id="imagemanagersource"  name="img">
						</div>
						<div class="col-md-4">
							<!-- <label style="display:inline;"><input type="checkbox" value="1" id="is_public"> Public Access</label> -->
						</div>
						<div class="col-md-4">
							<a class="btn btn-primary" id="upload-img">Upload</a>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="modal fade invis" id="popImageManagerDetail">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title">Image Detail </h4>
				</div>
				<div class="modal-body">
					<img id="imageDetail">
				</div>
			</div>
		</div>
	</div>
	<div class="modal modal-message fade invis" id="modal-crop">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title">Create Thumbnail </h4>
				</div>
				<div class="modal-body">
					<div id="crop_container"></div>
	
					<div id="image-thumb">
						<img src="">
					</div>
					<a id="crop" class="btn btn-success">Create Thumbnail</a>

				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="modal-footer">
					<a id="imagemanager-cancel" class="btn btn-warning">Cancel</a>
					<a class="btn btn-primary" id="imagemanager-save"> Simpan </a>
					<input type="hidden" id="imagemanager-name">
				</div>
			</div>
		</div>
	</div>
	<script>
	    var max_width_cropzoom = "'.$max_width_cropzoom.'";
	    var max_height_cropzoom = "'.$max_height_cropzoom.'";
	    var function_pagination = 0;
	</script>
	<link href="'.$CI->baseUrl.'assets/plugins/cropzoom/jquery.cropzoom.css" rel="Stylesheet" type="text/css" /> 
	<script type="text/javascript" src="'.$CI->baseUrl.'assets/plugins/cropzoom/jquery.cropzoom.js"></script>
';
$html['browse'] = '<div class="browse-image" id="'.$field.$id.'">
				<img src="'.$img.'" width="100%">
				<div>Select Image</div>
				<i class="fa fa-file-picture-o" style="font-size: 60px;"></i>
			</div>
			<input type="hidden" name="'.$field.$array.'" value="'.$name_img.'">';
return $html;
}
function is_edit_news($id_news,$user_id_create,$approval_level_news,$type,$base_controller){
 	$CI 			= & get_instance();
	$CI->load->model('newsModel');
	if($base_controller){
		$controller = $CI->baseUrl.'apps/news/';
	} else {
		$controller = $CI->currentController;
	}
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	$edit_enable	= "<a href='".$controller."add/$id_news' title='Edit Data' class='fa fa-pencil-square-o tangan action-form-icon update'></a>";
	$edit_disable	= '<i class="fa fa-pencil-square-o tangan action-form-icon update"></i>';
	if($id_news == '' || ((id_user() == $user_id_create  && $approval_level_news === 0 ) || $approval_level_news == $CI->newsModel->approvalLevelGroup) || $grup == 4){
		$ret =  $type == 'return' ? 1 : $edit_enable ;
	}
	else{
		$ret =  $type == 'return'  ? 0 : $edit_disable ;
	}
	return $ret;
}
function is_delete_news($id_news,$user_id_create,$approval_level_news,$type){
 	$CI 			= & get_instance();
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	$delete_enable 	= "<a title='Delete Data' class='fa fa-trash-o action-form-icon tangan hapus delete' id='$id_news' data-url-rm='del'></a>";
	$delete_disable = '<i class="fa fa-trash-o tangan delete action-form-icon"></i>';
	if((($grup == 1 || id_user() == $user_id_create || $approval_level_news !=0) && $approval_level_news === 0) || $grup == 1){
		$ret =  $type == 'return' ? 1 : $delete_enable ;
	}
	else{
		$ret =  $type == 'return'  ? 0 : $delete_disable ;
	}
	return $ret;
}
function is_edit_publish_status(){
    $CI 			= & get_instance();
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	if($grup == 4){
		$ret =  '';
	}
	else{
		$ret =  'hide';
	}
	return $ret;
}
function enable_edit_editors_choice(){
	$CI 			= & get_instance();
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	if($grup == 2){
		$ret =  'hide';
	}
	else{
		$ret =  '';
	}
	return $ret;
}
function iso_date_custom_format($date,$format){
    if($date == '1900-01-01' or $date == ''){
	return '';
    } else {
       return date("$format",strtotime($date));
    }
}

/**
* Encrypt Data
* @author          Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
*/
function md5plus($string){
	$CI = & get_instance();
	if ($string == null || $string == '') {
		return $string;
	} else {
		return '_'.md5($CI->config->item('encryption_key').$string);
	}
}

/**
* Query Encrypt Field
* @author          Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
*/
function md5field($string,$alias=''){
	$CI = & get_instance();
	$alias = ($alias) ? "as $alias" : '';
	return "CONCAT('_',md5(CONCAT('".$CI->config->item('encryption_key')."', $string ))) $alias";
}

/**
* Load JS File
* @author          Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
*/
function load_js($file,$path='asset/js'){
    $CI = & get_instance();
    $files = explode(',',$file);
    foreach($files as $fl){
        if(substr_count($fl, ".min.") == 0){
            if(IS_MINIFY==1){
                $fl = str_replace(".js", ".min.js", $fl);
            } 
        }
        if($fl)
            $js .= '<script type="text/javascript" src="'.base_url().$path."/".$fl."?ver=".ASSET_VERSION.'"></script>'."\n";
    }
    $CI->data['js_file'] .= $js;
}


/**
* Load CSS File
* @author          Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
*/
function load_css($file,$path='asset/css'){
    $CI=& get_instance();
    $files = explode(',',$file);
    foreach($files as $fl){
        if(substr_count($fl, ".min.") == 0){
            if(IS_MINIFY==1){
                $fl = str_replace(".css", ".min.css", $fl);
            } 
        }
        if($fl)
            $js .= '<link rel="stylesheet" type="text/css" href="'.base_url().$path."/".$fl."?ver=".ASSET_VERSION.'">'."\n";
    }
    $CI->data['css_file'] .= $js;
}

function limit_text($text, $limit) {
	$text = strip_tags($text);
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos = array_keys($words);
		$text = substr($text, 0, $pos[$limit]) . '...';
	}
	return $text;
}
function paging_event($total_row,$perpage,$uri_path, $uri_segment=3){
	$CI 	= & get_instance();
	$CI->load->library('pagination');
	$config['base_url']           = $uri_path;
	$config['total_rows']         = $total_row;
	$config['uri_segment']        = $uri_segment;
	$config['anchor_class']       = 'class="tangan"';
	$config['per_page']           = $perpage;
	$config['first_tag_open']     = '<li class="page-item">';
	$config['first_tag_close']    = '</li>';
	$config['first_link']         = false;
	$config['last_link']          = false;
	$config['num_tag_open']       = '<li class="page-item">';
	$config['num_tag_close']      = '</li>';
	$config['last_tag_close']     = '</li>';
	$config['last_tag_open']      = '<li class="page-item">';
	$config['first_tag_close']    = '</li>';
	$config['first_tag_open']     = '<li class="page-item">';
	$config['next_link']          = '&raquo;';
	$config['prev_link']          = '&laquo;';
	$config['prev_tag_open']      = '<li class="page-item">';
	$config['prev_tag_close']     = '</li>';
	$config['next_tag_open']      = '<li class="page-item">';
	$config['next_tag_close']     = '</li>';
	$config['next_tag_open']      = '<li class="page-item">';
	$config['next_tag_close']     = '</li>';
	$config['cur_tag_open']       = '<li class="page-item active"><a class="page-link">';
	$config['cur_tag_close']      = '</a></li>';
	$config['use_page_numbers']   = TRUE;
	$config['reuse_query_string'] = TRUE;
	$CI->pagination->initialize($config);
	
	$n 		 = $param['page'];
	$n2 	 = $n+1;
	$sd 	 = $n + $param['perpage'];
	$sd 	 = ($total_row < $sd) ? $total_row : $sd;
	$remark	 = ($sd > 0) ? ("$n2 - $sd Total $total_row") : '';
	$paging  = '<ul class="pagination">';
	$paging .= $CI->pagination->create_links();
	$paging .= '</ul>';
	return $paging;
}

function generateRandomString($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}