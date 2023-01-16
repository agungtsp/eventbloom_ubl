<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Provinsi_model extends CI_Model {

    var $table = 'ref_provinsi';
    var $tableAs = 'ref_provinsi a';

    function __construct()
    {
        parent::__construct();
    }

    function findBy($where,$is_single_row=0)
    {
        $where['is_delete'] = 0;
        $this->db->select('*');
        if($is_single_row==1){
            return  $this->db->get_where($this->tableAs,$where)->row_array();
        }
        else{
            return  $this->db->get_where($this->tableAs,$where)->result_array();
        }
    }  

    function fetchRow($where) {
        return $this->findBy($where,1);
    }

    function records($where = array(), $isTotal = 0)
    {
        $grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
        $alias['search_uri_provinsi'] = 'a.provinsi';

        query_grid($alias,$isTotal);
        $this->db->select("a.*,c.name as status");
        $this->db->join('status_publish c',"c.id = a.status_publish",'left');
        $this->db->where('a.is_delete',0);

        $query = $this->db->get($this->tableAs);

        if ($isTotal == 0)
        {
            $data = $query->result_array();
        }
        else
        {
            return $query->num_rows();
        }

        $ttl_row = $this->records($where, 1);

        return ddi_grid($data, $ttl_row);
      }

    function findById($id)
    {
        $where['is_delete'] = 0;
        $where['a.id_provinsi'] = $id;


        return $this->db->get_where($this->table.' a', $where)->row_array();
    }

    function update($data, $id)
    {
        $where['id_provinsi'] = $id;
        $this->db->update($this->table, $data, $where);

    }

    function updateToDelete($data, $id, $del = 0)
    {
        $data = db_escape_data($data);

        if ($del == 1)
        {
            $this->db->query("UPDATE ref_provinsi SET is_delete = $data[is_delete] WHERE id_provinsi = $id");

        }
        else
        {
            $this->db->query("UPDATE ref_provinsi SET is_delete = N '$data[kode_provinsi]', provinsi = N'$data[provinsi]' WHERE id_provinsi = $id");
        }
    }

    function delete($id)
    {
        $data['is_delete'] = 1;
        $this->updateToDelete($data, $id, 1);
    }

    function insert($data)
    {
        $this->db->insert($this->table, array_filter($data));
        $id = $this->db->insert_id();

        return $id;
    }

    function getProvince(){
        $where['a.is_delete'] = 0;
        $where['status_publish'] = 2;
        $this->db->distinct();
        $this->db->select('a.kd_prov,a.kode_provinsi,a.provinsi,a.uri_path as uri_path_provinsi');
        $this->db->order_by('a.provinsi','asc');
        if($is_single_row==1){
            return  $this->db->get_where($this->tableAs,$where)->row_array();
        }
        else{
            return  $this->db->get_where($this->tableAs,$where)->result_array();
        }
    }

    function findByProvinsiAutocomplete($where,$is_single_row=0)
    {
        $where['is_delete'] = 0;
        $where['status_publish'] = 2;
        $this->db->select('provinsi,uri_path');
        if($is_single_row==1){
            return  $this->db->get_where($this->tableAs,$where)->row_array();
        }
        else{
            return  $this->db->get_where($this->tableAs,$where)->result_array();
        }
    }

    function getProvinceSearch($kode_provinsi)
    {
        $where['a.is_delete'] = 0;
        $where['b.is_delete'] = 0;
        $where['a.status_publish'] = 2;
        $where['b.status_publish'] = 2;
        $this->db->distinct();
        $this->db->select('a.provinsi,a.uri_path as uri_path_provinsi,b.kabupatenkota,b.uri_path as uri_path_kabupatenkota');
        $this->db->join('ref_kabupaten b',"b.kode_provinsi = a.kode_provinsi",'left');
        $this->db->order_by('a.provinsi','asc');

        if($is_single_row==1){
            return  $this->db->get_where($this->tableAs,$where)->row_array();
        }
        else{
            return  $this->db->get_where($this->tableAs,$where)->result_array();
        }
    }

    function getPathMapsProvince($where,$is_single_row=0){
        $where['is_delete'] = 0;
        $this->db->select('kode_provinsi,id_path_maps,provinsi,point_path_maps');
        $this->db->where('provinsi !=','-');
        if($is_single_row==1){
            return  $this->db->get_where($this->tableAs,$where)->row_array();
        }
        else{
            return  $this->db->get_where($this->tableAs,$where)->result_array();
        }
    }
}
