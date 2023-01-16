<?php
class EventPriceModel extends  CI_Model{
	var $table = 'event_price';
	var $tableAs = 'event_price a';
    function __construct(){
       parent::__construct();
	   
    }
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] 			= $id;
		$data['user_id_modify'] = id_user();
		$data['update_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select("*");
		$this->db->where('a.is_delete',0);
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	function multi_delete($id_parent, $ids) {
		$data['is_delete']      = 1;
		$data['user_id_modify'] = id_user();
		$data['update_date']    = date('Y-m-d H:i:s');
		
		$this->db->where('id_event',$id_parent);
		$this->db->where_not_in('id',$ids);
		$this->db->update($this->table,$data,$where);
	}
 }
