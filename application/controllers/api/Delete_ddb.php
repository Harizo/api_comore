<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Delete_ddb extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('delete_ddb_model', 'DeleteddbManager');
    }

    
    public function index_post() {

    	$nom_table = $this->post('nom_table') ;
    	$supprimer = $this->post('supprimer') ;

        if ($nom_table == 'source_financement') 
        {
            $data = array(
                'id' => $this->post('id'),
                'nom' => $this->post('nom')
            ); 
        }
        else
        {
            $data = array(
                'id' => $this->post('id'),
                'description' => $this->post('description')
            ); 
        }

    	

    	if ($supprimer == 0) 
    	{
    		if (!$data) 
    		{
                $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->DeleteddbManager->add($data, $nom_table);              
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
    		$delete = $this->DeleteddbManager->delete($nom_table);          
			if (!is_null($delete)) {
				$this->response([
					'status' => TRUE,
					'response' => 1,
					'message' => "Delete data success"
				], REST_Controller::HTTP_OK);
			} 
			else 
			{
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