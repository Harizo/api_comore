<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Agent_ex extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('agent_ex_model', 'AgentexManager');
        $this->load->model('ile_model', 'ileManager');
        $this->load->model('programme_model', 'ProgrammeManager');
    }

    public function index_get() {
        $id = $this->get('id');
		$data = array();
        $taiza='';
		if ($id) {
			$tmp = $this->AgentexManager->findById($id);
			if($tmp) {
				$data=$tmp;
                $taiza='taoid';
			}
		} else {			
			$tmp = $this->AgentexManager->findAll();
			if ($tmp) {
				//$data=$tmp;
                foreach ($tmp as $key => $value)
                {
                    $ile = $this->ileManager->findById($value->ile_id);
                    $prog = $this->ProgrammeManager->findById($value->programme_id);
                    
                    $data[$key]['id'] = $value->id;
                    $data[$key]['Code'] = $value->Code;
                    $data[$key]['Nom'] = $value->Nom;
                    $data[$key]['Contact'] = $value->Contact;
                    $data[$key]['Representant'] = $value->Representant;
                    $data[$key]['ile'] = $ile;
                    $data[$key]['programme'] = $prog[0];
                     $taiza='findall';
                }
                
			}
		}
        if (count($data)>0) {
            $this->response([
                'status' => TRUE,
                'response' => $data,
                'taiz' => $taiza,
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
        $etat_download = $this->post('etat_download') ; 
		$data = array(
			'Code' => $this->post('Code'),
            'Nom' => $this->post('Nom'),
            'Contact' => $this->post('Contact'),
            'Representant' => $this->post('Representant'),
            'ile_id' => $this->post('ile_id'),
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
                $dataId = $this->AgentexManager->add($data);              
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
            } 
            else 
            {
                if ($etat_download) 
                {
                    if (!$data) {
                        $this->response([
                            'status' => FALSE,
                            'response' => 0,
                            'message' => 'No request found'
                                ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                    $dataId = $this->AgentexManager->add_down($data, $id);              
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
                }
                else
                {
                    if (!$data || !$id) {
                        $this->response([
                            'status' => FALSE,
                            'response' => 0,
                            'message' => 'No request found'
                                ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                    $update = $this->AgentexManager->update($id, $data);              
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
            }
        } else {
            if (!$id) {
            $this->response([
            'status' => FALSE,
            'response' => 0,
            'message' => 'No request found'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
            $delete = $this->AgentexManager->delete($id);          
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