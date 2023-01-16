<?php
class EventPublicModel extends  CI_Model{
	var $table = 'event';
	var $tableAs = 'event a';
    function __construct(){
       parent::__construct();
	   
    }
    function list_event($where=array(),$limit, $offset, $isTotal=0){
    	$where = array_merge($where,
    		array(
				'early_bird_start_date <=' => date('Y-m-d'),
				'early_bird_end_date >=' => date('Y-m-d'),
				'id_ref_event_status' => 1
			)
    	);
		$this->db->select("a.id,
			a.name as event_name, 
			a.description as event_desc,
			a.start_date as event_start_date,
			a.end_date as event_end_date,
			b.name as event_category, 
			c.name as event_status,
			location_name as event_location_name,
			a.image as event_image,
			a.uri_path as event_uri_path,
			b.uri_path as event_category_uri_path
		");
		$this->db->join('ref_event_category b','b.id=a.id_ref_event_category','left');
		$this->db->join('ref_event_status c','c.id=a.id_ref_event_status','left');
		$this->db->where('a.is_delete', 0);
		$this->db->order_by("create_date", "desc");
		if($limit){
			$this->db->limit($limit, $offset);
		}
		$query = $this->db->get_where('event a',$where);
		if($isTotal==0){
			$data['data'] = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		$data['total_data'] = $this->list_event($where, null, null, 1);
		return $data;
    }
	function find_event($where,$is_single_row=0){
		$this->db->select("a.id as id_event,
			a.name as event_name, 
			a.description as event_desc,
			a.start_date,
			a.end_date,
			b.name as event_category, 
			c.name as event_status,
			location_name,
			a.image as event_image,
			a.uri_path as event_uri_path,
			b.uri_path as event_category_uri_path,
			d.username as author_name,
			a.address as event_address,
			longitude,
			latitude
		");
		$this->db->join('ref_event_category b','b.id=a.id_ref_event_category','left');
		$this->db->join('ref_event_status c','c.id=a.id_ref_event_status','left');
		$this->db->join('auth_user d','d.id_auth_user=a.user_id_create','left');
		$this->db->where('a.is_delete', 0);
		
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	function find_speaker($where,$is_single_row=0){
		$this->db->select("name as speaker_name, description as speaker_description, image as speaker_img, topic as speaker_topic");
		$this->db->where('is_delete', 0);
		if($is_single_row==1){
			return 	$this->db->get_where("ref_event_speaker",$where)->row_array();
		}
		else{
			return 	$this->db->get_where("ref_event_speaker",$where)->result_array();
		}
	}
	function find_workshop($where,$is_single_row=0){
		$this->db->select("name as workshop_name, description as workshop_description, image as workshop_img, max_participant as workshop_max_participant");
		$this->db->where('is_delete', 0);
		if($is_single_row==1){
			return 	$this->db->get_where("ref_event_workshop",$where)->row_array();
		}
		else{
			return 	$this->db->get_where("ref_event_workshop",$where)->result_array();
		}
	}
 }
