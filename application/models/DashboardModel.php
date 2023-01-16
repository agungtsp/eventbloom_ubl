<?php
class DashboardModel extends  CI_Model{
	var $table = 'agent';
	var $tableAs = 'agent a';
	function __construct(){
		parent::__construct();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select('*');
		if($is_single_row==1){
			return $this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return $this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	
	function fetchRow($where) {
		return $this->findBy($where,1);
	}
}