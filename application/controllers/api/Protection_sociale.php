<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Protection_sociale extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('protection_sociale_model', 'Protection_socialeManager');
        $this->load->model('ile_model', 'ileManager');
        $this->load->model('village_model', 'villageManager');
        $this->load->model('programme_model', 'ProgrammeManager');
    }

    public function index_get() {
        $id = $this->get('id');
		$data = array();
		if ($id) {
			$tmp = $this->Protection_socialeManager->findById($id);
			if($tmp) {
				    $ile = $this->ileManager->findById($value->ile_id);
                    $prog = $this->ProgrammeManager->findById($value->programme_id);
                    $vil = $this->villageManager->findById($value->village_id);
                    $data['id'] = $value->id;
                    $data['Code'] = $value->Code;
                    $data['Nom'] = $value->Nom;
                    $data['Contact'] = $value->Contact;
                    $data['NumeroTelephone'] = $value->NumeroTelephone;
                    $data['Representant'] = $value->Representant;
                    $data['ile'] = $ile;
                    $data['vil'] = $vil;
                    $data['programme'] = $prog[0];
			}
		} else {			
			$tmp = $this->Protection_socialeManager->findAll();
			if ($tmp) {
				foreach ($tmp as $key => $value) {
					
					$ile = $this->ileManager->findById($value->ile_id);
                    $prog = $this->ProgrammeManager->findById($value->programme_id);
                    $vil = $this->villageManager->findById($value->village_id);
                    $data[$key]['id'] = $value->id;
                    $data[$key]['Code'] = $value->Code;
                    $data[$key]['Nom'] = $value->Nom;
                    $data[$key]['Contact'] = $value->Contact;
                    $data[$key]['NumeroTelephone'] = $value->NumeroTelephone;
                    $data[$key]['Representant'] = $value->Representant;
                    $data[$key]['ile'] = $ile;
                    $data[$key]['vile'] = $vile;
                    $data[$key]['programme'] = $prog[0];
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
				
		$data = array(
			'Code' => $this->post('Code'),
            'Nom' => $this->post('Nom'),
            'Contact' => $this->post('Contact'),
            'NumeroTelephone' => $this->post('NumeroTelephone'),
            'Representant' => $this->post('Representant'),
            'ile_id' => $this->post('ile_id'),
            'village_id' => $this->post('village_id'),
            'programme_id' => $this->post('programme_id')
		);               
        if ($supprimer == 0) {
            if ($id == 0) {
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->Protection_socialeManager->add($data);              
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
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->Protection_socialeManager->update($id, $data);              
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
            $delete = $this->Protection_socialeManager->delete($id);          
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
?>