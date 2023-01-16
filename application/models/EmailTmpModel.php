<?php
class EmailTmpModel extends  CI_Model{
	var $table = 'email_tmp';
	var $tableAs = 'email_tmp a';
    function __construct(){
       parent::__construct();

    }
	function records($where=array(),$isTotal=0){
		$grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
        $alias['search_email_category'] = 'e.id';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,c.name as status,d.username,e.name as email_category");
		$this->db->where('a.is_delete',0);
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
        $this->db->join('ref_email_category e',"e.id = a.id_ref_email_category",'left');
		
		$query = $this->db->get($this->tableAs);
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
        $generate =  generate_email_template_file($data['template_name'],$data['page_content']);
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
        $generate =  generate_email_template_file($data['template_name'],$data['page_content']);
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
        $this->db->where('id',$id);
		$this->db->delete($this->table); 
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 0;
		$this->db->select('a.*,b.username');
		$this->db->join('auth_user b','b.id_auth_user = a.user_id_create');

		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}

	function fetchRow($where) {
		return $this->findBy($where,1);
	}
    function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	} 

 }
