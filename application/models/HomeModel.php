<?php
class HomeModel extends  CI_Model{
    function __construct(){
       parent::__construct();
	   
    }
	function popular_category(){
		$query = $this->db->query("
			SELECT count(a.id) as total_event, b.name as popular_category_name, b.uri_path FROM event a left join ref_event_category b on a.id_ref_event_category = b.id where b.is_delete =0 group by b.name order by total_event desc"
		,$where);

		$data = $query->result_array();
		return $data;
	}

	function list_all_category(){
		$this->db->order_by("a.name", "asc");
		$data = $this->db->select("a.name as category_name, a.uri_path as category_uri_path, a.description as category_description, a.image as category_image")->get_where("ref_event_category a",array("is_delete"=>0))->result_array();
		return $data;
	}

	function list_featured_event(){
		$where = array(
			'early_bird_start_date <=' => date('Y-m-d'),
			'early_bird_end_date >=' => date('Y-m-d'),
			'id_ref_event_status' => 1,
			'is_featured' => 1
		);
		$this->db->select("a.id,
			a.name as event_name, 
			a.description as event_desc,
			a.start_date,
			a.end_date,
			b.name as event_category, 
			c.name as event_status,
			location_name,
			a.image as event_image,
			a.uri_path as event_uri_path,
			a.uri_path as event_uri_path_2,
			b.uri_path as event_category_uri_path
		");
		$this->db->join('ref_event_category b','b.id=a.id_ref_event_category','left');
		$this->db->join('ref_event_status c','c.id=a.id_ref_event_status','left');
		$this->db->where('a.is_delete', 0);
		$this->db->order_by("start_date", "asc");
		$this->db->limit(6);
		$query = $this->db->get_where('event a',$where);
		$data = $query->result_array();
		return $data;
	}

	function list_newest_event(){
		$where = array(
			'early_bird_start_date <=' => date('Y-m-d'),
			'early_bird_end_date >=' => date('Y-m-d'),
			'id_ref_event_status' => 1
		);
		$this->db->select("a.id,
			a.name as newest_event_name, 
			a.description as newest_event_desc,
			a.start_date as newest_start_date,
			a.end_date as newest_end_date,
			b.name as newest_event_category, 
			c.name as newest_event_status,
			location_name as newest_location_name,
			a.image as newest_event_image,
			a.uri_path as newest_event_uri_path,
			a.uri_path as newest_event_uri_path_2,
			b.uri_path as newest_event_category_uri_path
		");
		$this->db->join('ref_event_category b','b.id=a.id_ref_event_category','left');
		$this->db->join('ref_event_status c','c.id=a.id_ref_event_status','left');
		$this->db->where('a.is_delete', 0);
		$this->db->order_by("create_date", "desc");
		$this->db->limit(6);
		$query = $this->db->get_where('event a',$where);
		$data = $query->result_array();
		return $data;
	}
 }
