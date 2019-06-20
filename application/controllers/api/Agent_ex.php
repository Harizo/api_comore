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
		if ($id) {
			$tmp = $this->AgentexManager->findById($id);
			if($tmp) {
				$data=$tmp;
			}
		} else {			
			$tmp = $this->AgentexManager->findAll();
			if ($tmp) {
				//$data=$tmp;
                foreach ($tmp as $key => $value)
                {
                    $ile = $this->ileManager->findById($value->ile_id);
                    $prog = $this->ProgrammeManager->findById($value->programme_id);
                    
                    $data['id'] = $value->id;
                    $data['Code'] = $value->Code;
                    $data['Nom'] = $value->Nom;
                    $data['Contact'] = $value->Contact;
                    $data['Representant'] = $value->Representant;
                    $data['ile'] = $ile;
                    $data['programme'] = $prog[0];
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
            } else {
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