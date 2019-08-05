<?php
//harizo
defined('BASEPATH') OR exit('No direct script access allowed');

// afaka fafana refa ts ilaina
require APPPATH . '/libraries/REST_Controller.php';

class Commune extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('commune_model', 'CommuneManager');
        $this->load->model('region_model', 'RegionManager');
        $this->load->model('programme_model', 'ProgrammeManager');
    }
    public function index_get() {
        $id = $this->get('id');
        $cle_etrangere = $this->get('cle_etrangere');
        $id_commune = $this->get('id_commune');
       $id_district = $this->get('id_district');
        $id_region = $this->get('id_region');
 //miasa       
        $region_id = $this->get('region_id');
		$taiza="";
        
        if ($region_id)
        {
         $tmp = $this->CommuneManager->findCommuneByPrefecture($region_id);
            if ($tmp)
            {
                foreach ($tmp as $key => $value)
                {       
                    $data[$key]['id'] = $value->id;
                    $data[$key]['Code'] = $value->Code;
                    $data[$key]['Commune'] = $value->Commune;
                }
            } else
                $data = array();
        }
        else
        {
            if ($cle_etrangere) {
                $data = $this->CommuneManager->findAllByRegion($cle_etrangere);           
            } else {
     //miasa           
                if ($id)  {
                    $data = array();
                    $commune = $this->CommuneManager->findById($id);
                    $pref = $this->RegionManager->findById($commune[0]->region_id);
                    $prog = $this->ProgrammeManager->findById($commune[0]->programme_id);
                    $data['id'] = $commune[0]->id;
                    $data['Code'] = $commune[0]->Code;
                    $data['Commune'] = $commune[0]->Commune;
                    $data['prefecture'] = $pref;
                    $data['programme'] = $prog[0];
               
              /*  } else if($id_commune) {    
                    $taiza="Ato ambony ary id_commune=".$id_commune."  ary id_region=".$id_region; 
                    $menu = $this->CommuneManager->find_Fokontany_avec_District_et_Region($id_commune);
                    if ($menu) {
                        $data=$menu;
                    } else
                        $data = array();
            
                } else if($id_district) {   
                    $menu = $this->CommuneManager->find_Commune_avec_District_et_Region($id_district);
                    if ($menu) {
                        $data=$menu;
                    } else
                        $data = array();*/
    //miasa             
                } else {
                    $taiza="findAll no nataony";
                    $menu = $this->CommuneManager->findAll();
                    
                    if ($menu) {
                        foreach ($menu as $key => $value) {
                            
                            $pref = $this->RegionManager->findById($value->region_id);
                            $prog = $this->ProgrammeManager->findById($value->programme_id);
                            
                            $data[$key]['id'] = $value->id;
                            $data[$key]['Code'] = $value->Code;
                            $data[$key]['Commune'] = $value->Commune;
                            $data[$key]['prefecture'] = $pref;
                            $data[$key]['programme'] = $prog[0];
                        }
                    } else
                        $data = array();
                }
            }
        }
        
        
        if (count($data)>0) {
            $this->response([
                'status' => TRUE,
                'response' => $data,
                'message' => $taiza,
                // 'message' => 'Get data success',
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
                    'Commune' => $this->post('Commune'),
                    'region_id' => $this->post('region_id'),
                    'programme_id' => $this->post('programme_id')
                );
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->CommuneManager->add($data);
                if (!is_null($dataId))  {
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
                    'Commune' => $this->post('Commune'),
                    'region_id' => $this->post('region_id'),
                    'programme_id' => $this->post('programme_id')
                );
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->CommuneManager->update($id, $data);
                if(!is_null($update)) {
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
            $delete = $this->CommuneManager->delete($id);
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
