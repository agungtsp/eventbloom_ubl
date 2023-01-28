<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Event extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
    function index($page=1, $category=''){
    	$this->load->model("EventPublicModel");
        $perpage = 9;
        $limit   = $perpage;
        $offset  = ($page) ? ((int)$perpage * (int)$page) - $perpage : 0;
        $where   = array();
        $get     = purify(null_empty($this->input->get()));
        if(isset($get['kategori'])){
            $category = $get['kategori'];
        }
        if(isset($get['judul'])){
            $this->db->or_like('LOWER(a.name)', strtolower($get['judul']));
            $this->db->or_like('LOWER(a.description)', strtolower($get['judul']));
            $is_search_event                    = 1;
            $is_search_event_by_name            = 1;
            $data['search_event_name']          = $get['judul'];
        }
        if($category){
            $where['b.uri_path']           = $category;
            $is_search_event               = 1;
            $is_search_event_by_category   = 1;
            $data['search_event_category'] = db_get_one('ref_event_category','name',array("uri_path"=>$category));
        }
        $list_event = $this->EventPublicModel->list_event($where, $limit, $offset, null);
    	$counter = 0;
        $list_all_event = array();
    	foreach ($list_event['data'] as $key => $value) {
			if($key%3==0){
                $counter++;
            }
            $value['event_start_date'] = tgl_indo(iso_date($value['event_start_date']));
            $value['event_end_date']   = tgl_indo(iso_date($value['event_end_date']));
            $value['event_desc'] = limit_text($value['event_desc'], 20);
            if( strpos($value['event_image'],',') !== false ) {
                $list_images = explode(',', $value['event_image']);
                foreach ($list_images as $key_image => $value_image) {
                    // display first image
                    if($key_image==0){
                        $value['list_event_image'][$key_image]['img_event'] = image($value_image,'large');
                    }
                }
            } else {
                $value['list_event_image'][]['img_event'] = $value['event_image'] ? image($value['event_image'],'large') : '';
            }
            $value['event_date'] = ($value['event_start_date']==$value['event_end_date']) ? $value['event_start_date'] : $value['event_start_date'] . " - " . $value['event_end_date'];
            $list_all_event[$counter]['data_event'][] = $value;
    	}
        $data['list_event']                  = $list_all_event;
        $data['pagination']                  = paging_event($list_event['total_data'], $perpage, base_url("event/page"), 3);
        $data['active_event']                = "active";
        $data['header_search_event']         = 1;
        $data['is_search_event']             = ($is_search_event==1) ? "" : "hidden";
        $data['is_search_event_by_name']     = ($is_search_event_by_name==1) ? "" : "hidden";
        $data['is_search_event_by_category'] = ($is_search_event_by_category==1) ? "" : "hidden";
        $data['is_empty_data']               = (count($list_all_event) > 0) ? "hidden" : "";
        $data['page_name']                   = "Event";
		render("event", $data);
    }
}