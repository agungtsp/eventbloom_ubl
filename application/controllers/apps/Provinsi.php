<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Provinsi extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Provinsi_model');
    }

    function index()
    {   
        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));

        render('apps/provinsi/index', $data, 'apps');
    }

    function add($id = '')
    {
        if ($id)
        {
            $data = $this->Provinsi_model->findById($id);
            if (!$data)
            {
                die('404');
            }

            $data                   = quote_form($data);
            $data['judul']          = 'Sunting';
            $data['proses']         = 'Update';
            $data['page_name_data'] = $data['page_name'];
        }
        else
        {
            $data['judul']          = 'Tambah';
            $data['proses']         = 'Simpan';
            $data['kd_prov']        = '';
            $data['kode_provinsi']  = '';
            $data['provinsi']       = '';
            $data['id_provinsi']    = '';

            $data['status_publish']     = '';
            //$data['create_date']        = date("m/d/Y g:i A", strtotime(date('h:i')));
            $data['modify_date']        = date("m/d/Y g:i A", strtotime(date('h:i')));
            $data['user_id_create']     = '';
            $data['user_id_modify']     = '';
            $data['uri_path']     = '';

        }

        $img_thumb                      = image($data['img'],'small');
        $imagemanager                   = imagemanager('img',$img_thumb,277,150);
        $data['img']                    = $imagemanager['browse'];
        $data['imagemanager_config']    = $imagemanager['config'];

        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['status_publish']));

        render('apps/provinsi/add', $data, 'apps');
    }

    function proses($idedit = '')
    {
        $this->layout = 'none';
        $post = purify($this->input->post());
        $ret['error'] = 1;

        $this->form_validation->set_rules('kd_prov', '"Kd Prov"', 'required');
        $this->form_validation->set_rules('kode_provinsi', '"Kode Provinsi"', 'required');
        $this->form_validation->set_rules('provinsi', '"Nama Provinsi"', 'required');
       
        if ($this->form_validation->run() == FALSE)
        {
            $ret['message'] = validation_errors(' ',' ');
        }
        else
        {
            $this->db->trans_start();

            if ($idedit)
            {
                auth_update();
                $ret['message'] = 'Update Success';
                $act            = 'Update News';

                $this->Provinsi_model->update($post, $idedit);
            }
            else
            {
                auth_insert();
                $ret['message'] ='Insert Success';
                $act            ='Insert News';
                $idedit         = $this->Provinsi_model->insert($post);

            }

            $this->db->trans_complete();
            $this->session->set_flashdata('message', $ret['message']);
            $ret['error'] = 0;
        }
        echo json_encode($ret);
    }

    function del()
    {
        $this->db->trans_start();
        $id = $this->input->post('iddel');
        $this->Provinsi_model->delete($id);
        $this->db->trans_complete();
    }

    function records()
    {
	    $data = $this->Provinsi_model->records();

        foreach ($data['data'] as $key => $value)
        {
            $data['data'][$key]['id_provinsi'] = quote_form($value['id_provinsi']);
            $data['data'][$key]['provinsi'] = quote_form($value['provinsi']);
            $data['data'][$key]['kode_provinsi'] = quote_form($value['kode_provinsi']);
            $data['data'][$key]['tahun'] = quote_form($value['tahun']);
        }
        $data['edit_hidden'] = (auth_access('u') != 1) ? 'hidden' : '';
        $data['delete_hidden'] = (auth_access('d') != 1) ? 'hidden' : '';  

	    render('apps/provinsi/records',$data,'blank');
  }

}
