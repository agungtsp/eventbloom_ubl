<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
    function index(){
    	$this->load->model("HomeModel");
        $data['popular_category']  = $this->HomeModel->popular_category();
        $data['list_all_category'] = $this->HomeModel->list_all_category();
        $list_all_category         = $this->HomeModel->list_all_category();
        $counter                   = 0;
        $number_icon               = 1;
    	foreach ($list_all_category as $key => $value) {
    		if($key%4==0){
    			$counter++;
    		}
            $value['number_icon']                                   = $number_icon++;
            $list_all_category_feature[$counter]['data_category'][] = $value;
    	}
    	$data['list_all_category_feature'] = $list_all_category_feature;
    	$featured_event = $this->HomeModel->list_featured_event();
    	$counter = 0;
		$list_featured_event = array();
    	foreach ($featured_event as $key => $value) {
    		if($key%3==0){
    			$counter++;
    		}
            $value['start_date'] = tgl_indo(iso_date($value['start_date']));
            $value['end_date']   = tgl_indo(iso_date($value['end_date']));
            $value['event_desc'] = limit_text($value['event_desc'], 20);
			if( strpos($value['event_image'],',') !== false ) {
				$list_images = explode(',', $value['event_image']);
				foreach ($list_images as $key_image => $value_image) {
					// display first image
					if($key_image==0){
						$value['list_image'][$key_image]['img'] = image($value_image,'large');
					}
				}
			} else {
				$value['list_image'][]['img'] = $value['event_image'] ? image($value['event_image'],'large') : '';
			}
            $value['event_date']                              = ($value['start_date']==$value['end_date']) ? $value['start_date'] : $value['start_date'] . " - " . $value['end_date'];
            $list_featured_event[$counter]['data_featured'][] = $value;
    	}
    	$data['list_featured_event'] = $list_featured_event;
		$data['show_list_featured_event'] = (count($list_featured_event) > 0) ? "" : "hidden";

    	$newest_event = $this->HomeModel->list_newest_event();
    	$counter = 0;
		$list_newest_event = array();
    	foreach ($newest_event as $key => $value) {
    		if($key%3==0){
    			$counter++;
    		}
			$value['newest_start_date'] = tgl_indo(iso_date($value['newest_start_date']));
			$value['newest_end_date']   = tgl_indo(iso_date($value['newest_end_date']));
			$value['newest_event_desc'] = limit_text($value['newest_event_desc'], 20);
			if( strpos($value['newest_event_image'],',') !== false ) {
				$list_images = explode(',', $value['newest_event_image']);
				foreach ($list_images as $key_image => $value_image) {
					// display first image
					if($key_image==0){
						$value['list_newest_image'][$key_image]['img_newest'] = image($value_image,'large');
					}
				}
			} else {
				$value['list_newest_image'][]['img_newest'] = $value['newest_event_image'] ? image($value['newest_event_image'],'large') : '';
			}
            $value['newest_event_date']                   = ($value['newest_start_date']==$value['newest_end_date']) ? $value['newest_start_date'] : $value['newest_start_date'] . " - " . $value['newest_end_date'];
            $list_newest_event[$counter]['data_newest'][] = $value;
    	}
        $data['list_newest_event'] = $list_newest_event;
		$data['show_list_newest_event'] = (count($list_newest_event) > 0) ? "" : "hidden";

        $data['active_home']       = "active";
        $data['page_name']         = "Home";
		render("home", $data);
    }
}