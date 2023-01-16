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
		$data = $this->db->select("a.name as category_name, a.uri_path as category_uri_path")->get_where("ref_event_category a",array("is_delete"=>0))->result_array();
		return $data;
	}
 }
