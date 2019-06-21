<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//harizo
// afaka fafana refa ts ilaina
require APPPATH . '/libraries/REST_Controller.php';

class Village extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('village_model', 'villageManager');
        $this->load->model('commune_model', 'CommuneManager');
        $this->load->model('programme_model', 'ProgrammeManager');
    }

    public function index_get()
    {
        $id = $this->get('id');
        $cle_etrangere = $this->get('cle_etrangere');
        $ile_id = $this->get('ile_id');
       if ($ile_id) {
          $tmp = $this->villageManager->findAllByIle($ile_id);
            if($tmp){
                foreach ($tmp as $key => $value) {
                $data[$key]['id'] = $value->id;
                $data[$key]['Code'] = $value->Code;
                $data[$key]['Village'] = $value->Village;
                };
            }else
                    $data = array();
       } else {
           if ($cle_etrangere) {
            $tmp = $this->villageManager->findAllByCommune($cle_etrangere);
            if($tmp){
                foreach ($tmp as $key => $value) {
                $data[$key]['id'] = $value->id;
                $data[$key]['Code'] = $value->Code;
                $data[$key]['Village'] = $value->Village;
                };
            }else
                    $data = array();
            
        } else {
            if ($id) {
                $data = array();
                $village = $this->villageManager->findById($id);
                $com = $this->CommuneManager->findById($value->commune_id);
                $prog = $this->ProgrammeManager->findById($value->programme_id);

                $data['id'] = $village->id;
                $data['Code'] = $village->Code;
                $data['Village'] = $village->Village;
                $data['commune'] = $com[0];
                $data['programme'] = $prog[0];
                
            } else {
                $village = $this->villageManager->findAll();
                if ($village) {
                    foreach ($village as $key => $value) {
                        
                        $prog = $this->ProgrammeManager->findById($value->programme_id);
                        $com = $this->CommuneManager->findById($value->commune_id);

                        $data[$key]['id'] = $value->id;
                        $data[$key]['Code'] = $value->Code;
                        $data[$key]['Village'] = $value->Village;
                        $data[$key]['commune'] = $com[0];
                        $data[$key]['programme'] = $prog[0];
                        
                    };
                } else
                    $data = array();
            }
        }
       }
       
        
        
        if (count($data)>0) {
            $this->response([
                'status' => TRUE,
                'response' => $data,
                'message' => 'Get data success',
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'response' => array(),
                'message' => 'No data were found'
            ], REST_Controller::HTTP_OK);
        }
    }
    public function index_post() {
        $id = $this->post('id') ;
        $supprimer = $this->post('supprimer') ;
        if ($supprimer == 0) {
            if ($id == 0) {
                $data = array(
                    'Code' => $this->post('Code'),
                    'Village' => $this->post('Village'),
                    'commune_id' => $this->post('commune_id'),
                    'programme_id' => $this->post('programme_id')
                );               
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->villageManager->add($data);              
                if (!is_null($dataId)) {
                    $this->response([
                        'status' => TRUE,
                        'response' => $dataId,
                        'message' => 'Data insert success'
                            ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } else {
                $data = array(
                    'Code' => $this->post('Code'),
                    'Village' => $this->post('Village'),
                    'commune_id' => $this->post('commune_id'),
                    'programme_id' => $this->post('programme_id')
                );              
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->villageManager->update($id, $data);              
                if(!is_null($update)){
                    $this->response([
                        'status' => TRUE, 
                        'response' => 1,
                        'message' => 'Update data success'
                            ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_OK);
                }
            }
        } else {
            if (!$id) {
            $this->response([
            'status' => FALSE,
            'response' => 0,
            'message' => 'No request found'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
            $delete = $this->villageManager->delete($id);          
            if (!is_null($delete)) {
                $this->response([
                    'status' => TRUE,
                    'response' => 1,
                    'message' => "Delete data success"
                        ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'response' => 0,
                    'message' => 'No request found'
                        ], REST_Controller::HTTP_OK);
            }
        }   
    }
}
/* End of file controllername.php */
/* Location: ./application/controllers/controllername.php */