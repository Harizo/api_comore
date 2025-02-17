<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Programme extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('programme_model', 'ProgrammeManager');
    }

    public function index_get() {
        $id = $this->get('id');
		$data = array();
		if ($id) {
			$data = $this->ProgrammeManager->findById($id);
			
		} else {			
			$data = $this->ProgrammeManager->findAll();
		
				
			
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
        $download = $this->post('download') ;

        if ($download == 1) 
        {
            $data = array(
                'id' => $this->post('id'),
                'libelle' => $this->post('libelle')
            );   

            if (!$data) 
            {
                $this->response([
                    'status' => FALSE,
                    'response' => 0,
                    'message' => 'No request found'
                        ], REST_Controller::HTTP_BAD_REQUEST);
            }
            $dataId = $this->ProgrammeManager->add_down($data);              
            if (!is_null($dataId)) 
            {
                $this->response([
                    'status' => TRUE,
                    'response' => $dataId,
                    'message' => 'Data insert success'
                        ], REST_Controller::HTTP_OK);
            } 
            else 
            {
                $this->response([
                    'status' => FALSE,
                    'response' => 0,
                    'message' => 'No request found'
                        ], REST_Controller::HTTP_BAD_REQUEST);
            }


        }
        else
        {
            $data = array(
                'libelle' => $this->post('libelle')
            );   

            if ($supprimer == 0) 
            {
                if ($id == 0) 
                {
                    if (!$data) 
                    {
                        $this->response([
                            'status' => FALSE,
                            'response' => 0,
                            'message' => 'No request found'
                                ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                    $dataId = $this->ProgrammeManager->add($data);              
                    if (!is_null($dataId)) 
                    {
                        $this->response([
                            'status' => TRUE,
                            'response' => $dataId,
                            'message' => 'Data insert success'
                                ], REST_Controller::HTTP_OK);
                    } 
                    else 
                    {
                        $this->response([
                            'status' => FALSE,
                            'response' => 0,
                            'message' => 'No request found'
                                ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                } 
                else 
                {
                    if (!$data || !$id) 
                    {
                        $this->response([
                            'status' => FALSE,
                            'response' => 0,
                            'message' => 'No request found'
                                ], REST_Controller::HTTP_BAD_REQUEST);
                    }
                    $update = $this->ProgrammeManager->update($id, $data);              
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
            else 
            {
                if (!$id) {
                $this->response([
                'status' => FALSE,
                'response' => 0,
                'message' => 'No request found'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $delete = $this->ProgrammeManager->delete($id);          
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
}
?>