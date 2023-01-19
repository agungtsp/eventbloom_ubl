<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Event_detail extends CI_Controller {
	function __construct(){
		parent::__construct();
	}
    function index($uri_path){
    	$this->load->model("EventPublicModel");
    	$where = array(
			'a.early_bird_start_date <=' => date('Y-m-d'),
			'a.early_bird_end_date >=' => date('Y-m-d'),
			'a.id_ref_event_status' => 1,
			'a.uri_path' => $uri_path
		);
    	$data = $this->EventPublicModel->find_event($where, 1);
    	if(!$data){
    		redirect(base_url("notfound"));
    		exit();
    	}
    	if( strpos($data['event_image'],',') !== false ) {
			$list_images = explode(',', $data['event_image']);
			foreach ($list_images as $key_image => $data_image) {
				$data['list_image'][$key_image]['img']    = image($data_image,'large');
				$data['list_image'][$key_image]['key']    = image($data_image,'large');
				$data['list_image'][$key_image]['status'] = ($key_image==0) ? "active" : "";
			}
		} else {
			$data['list_image'][0]['img']    = $data['event_image'] ? image($data['event_image'],'large') : '';
			$data['list_image'][0]['key']    = 0;
			$data['list_image'][0]['status'] = "active";
		}
		$data['list_image_icon'] = $data['list_image'];
		$data['event_date']      = ($data['start_date']==$data['end_date']) ? $data['start_date'] : $data['start_date'] . " - " . $data['end_date'];
		
		$data['list_speakers']   = $this->EventPublicModel->find_speaker(array("id_event" => $data['id_event']));
    	foreach ($data['list_speakers'] as $key => $value) {
    		$data['list_speakers'][$key]['speaker_img'] = image($value['speaker_img'],'large');
    	}
    	
		$data['show_speakers_tab']   = (count($data['list_speakers']) > 0) ? "" : "hidden";
		$data['active_event']        = "active";
		$data['header_search_event'] = 1;
		$data['page_name']           = $data['event_name'];
		$data['idx_event']           = md5plus($data['id_event']);
		$data['current_url'] 		 = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		render("event_detail", $data);
    }
}