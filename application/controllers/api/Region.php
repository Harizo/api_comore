<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//harizo
// afaka fafana refa ts ilaina
require APPPATH . '/libraries/REST_Controller.php';

class Region extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('region_model', 'RegionManager');
       // $this->load->model('site_model', 'SiteManager');
        $this->load->model('ile_model', 'ileManager');
        $this->load->model('programme_model', 'ProgrammeManager');
    }

    public function index_get() {
        $id = $this->get('id');
        $cle_etrangere = $this->get('cle_etrangere');
        $ile_id = $this->get('ile_id');
        
        if ($ile_id) {
            $tmp = $this->RegionManager->findPrefectureByIle($ile_id);
                if ($tmp)
                {
                    foreach ($tmp as $key => $value)
                    {
                        $data[$key]['id'] = $value->id;
                        $data[$key]['Code'] = $value->Code;
                        $data[$key]['Region'] = $value->Region;
                    };
                } else
                    $data = array();
        } 
        else
        {

            if ($cle_etrangere) 
            {
            $data = $this->RegionManager->findAllByIle($cle_etrangere);
            
            } else {
                if ($id)
                {
                    $data = array();
                    $region = $this->RegionManager->findById($id);
                    $data['id'] = $region->id;
                    $data['Code'] = $region->Code;
                    $data['Region'] = $region->Region;
                    
                } 
                else
                {
                    $region = $this->RegionManager->findAll();
                    if ($region)
                    {
                        foreach ($region as $key => $value)
                        {
                            $ile = $this->ileManager->findById($value->ile_id);
                            $prog = $this->ProgrammeManager->findById($value->programme_id);

                            $data[$key]['id'] = $value->id;
                            $data[$key]['Code'] = $value->Code;
                            $data[$key]['Region'] = $value->Region;
                            $data[$key]['ile'] = $ile;
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
                    'Region' => $this->post('Region'),
                    'ile_id' => $this->post('ile_id'),
                    'programme_id' => $this->post('programme_id')
                );               
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->RegionManager->add($data);              
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
                    'Region' => $this->post('Region'),
                    'ile_id' => $this->post('ile_id'),
                    'programme_id' => $this->post('programme_id')
                );              
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->RegionManager->update($id, $data);              
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
            $delete = $this->RegionManager->delete($id);          
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