<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kabupaten extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('kabupaten_model');
        $this->load->model('provinsi_model');
    }

    function index()
    {
        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
        
        render('apps/kabupaten/index', $data, 'apps');
    }

    function proses($idedit='')
    {
        $this->layout   = 'none';
        $post           = purify($this->input->post());
        $ret['error']   = 1;

        $this->form_validation->set_rules('kd_kab', '"Kd_Kab"', 'required');
        $this->form_validation->set_rules('kabupatenkota', '"Kabupaten/Kota"', 'required');
        $this->form_validation->set_rules('kode_kabupaten', '"Kode Kabupaten"', 'required');
        

        if ($this->form_validation->run() == FALSE)
        {
            $ret['message']  = validation_errors(' ',' ');
        }
        else
        {
            $provinsi   = $this->provinsi_model->findBy(array('kode_provinsi'=>$post['kode_provinsi']),1);
            $this->db->trans_start();
            if ($idedit)
            {
                auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update News";
                $idedit         = $this->kabupaten_model->update($post, $idedit);

            }
            else
            {
                auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert News";
                $post['kd_prov'] = $provinsi['kd_prov'];
                $post['kode_provinsi'] = $provinsi['kode_provinsi'];
                $idedit         = $this->kabupaten_model->insert($post);

            }

            $ret['error']   = 0;
            $this->db->trans_complete();
            autocomplete_provinsi_generate();
        }

        echo json_encode($ret);
    }

    function add($id='')
    {
        if ($id)
        {
            $data = $this->kabupaten_model->findById($id);

            if (!$data)
            {
                die('404');
            }

            $data           = quote_form($data);
            $data['judul']  = 'Sunting';
            $data['proses'] = 'Update';
        }

        else
        {
            $data['judul'] = 'Tambah';
            $data['proses'] = 'Simpan';
            $data['kd_kab'] = '';
            $data['kode_kabupaten'] = '';
            $data['kabupatenkota'] = '';
            $data['id_kabupaten'] = '';
            $data['kode_provinsi'] = '';

            $data['status_publish']     = '';
            // $data['create_date']        = date("m/d/Y g:i A", strtotime(date('h:i')));
            $data['modify_date']        = date("m/d/Y g:i A", strtotime(date('h:i')));
            $data['user_id_create']     = '';
            $data['user_id_modify']     = '';
            $data['uri_path']     = '';
        }

        $img_thumb                      = image($data['img'],'small');
        $imagemanager                   = imagemanager('img',$img_thumb,277,150);
        $data['img']                    = $imagemanager['browse'];
        $data['imagemanager_config']    = $imagemanager['config'];

        $data['list_provinsi'] = selectlist2(
            array(
            'table'=>'ref_provinsi',
            'id' => 'kode_provinsi',
            'name' => 'provinsi',
            'title'=>'Pilih Provinsi',
            'selected'=>$data['kode_provinsi'],
            'provinsi'=>'provinsi',
            'where'=> 'is_delete=0')
        );

        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['status_publish']));

        render('apps/kabupaten/add', $data, 'apps');
    }

    function del()
    {
        auth_delete();
        $id     = $this->input->post('iddel');
        $data   = $this->kabupaten_model->delete($id);
        autocomplete_provinsi_generate();
        detail_log();
        insert_log("Delete Pages");
    }

    function records()
    {
        $data = $this->kabupaten_model->records();

        render('apps/kabupaten/records', $data, 'blank');
    }

    public function import()
    {
        $this->db->trans_start();
        $ret['status']  = 0;
        $ret['message'] = 'Import Success';
        $act            = "Import Master Kabupaten";

        $fileName = $_FILES['import']['name'];

        $config = upload_file('import','','xls|xlsx|csv',10000,UPLOAD_DIR_KABUPATEN);
        $inputFileName = $config['full_path'];

        $user_sess = $this->session->userdata('ADM_SESS');
        $is_instansi = $user_sess['is_instansi'];
        $id_instansi = $user_sess['id_instansi'];

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $sheet_kabupaten = $objPHPExcel->getSheet(0);
        $highestRow_kabupaten = $sheet_kabupaten->getHighestRow();
        $highestColumn_kabupaten = $sheet_kabupaten->getHighestColumn();

        for ($row = 2; $row <= $highestRow_kabupaten; $row++)
        {
            $rowData = $sheet_kabupaten->rangeToArray('A' . $row . ':' . $highestColumn_kabupaten . $row, NULL,   TRUE, FALSE);
            $uri_path = generate_url($rowData[0][3]);
            $data = array(
                        "kd_kab"=>$rowData[0][1],
                        "kode_provinsi" => $rowData[0][2],        
                        "kabupatenkota" => $rowData[0][3],   
                        "kode_kabupaten" => $rowData[0][4],   
                        "kd_prov" => $rowData[0][5],     
                        "uri_path" => $uri_path,    
                        "is_delete" => $rowData[0][7],
                        "status_publish" => $rowData[0][8],  
                        "user_id_create" => $rowData[0][9],  
                        "user_id_create"=>id_user(),
                        "create_date"=>date('Y-m-d H:i:s'),
                        "user_id_modify" => $rowData[0][11],  
                        "modify_date" => $rowData[0][12]
                        );
            $this->Kabupaten_model->insert($data);
        }
        //$this->db->query("DELETE FROM ref_kabupaten WHERE uri_path = 'n-a' AND is_delete = 0");
        $this->db->trans_complete();
        detail_log();
        insert_log('Import data Kabupaten');
        $ret['error'] = 0;
        echo json_encode($ret);
    }

}
