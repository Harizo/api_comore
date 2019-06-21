<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Reporting extends REST_Controller 
{

    public function __construct() {
        parent::__construct();
        $this->load->model('reporting_model', 'ReportingManager');
    }

    public function index_get() 
    {
    	$type_etat = $this->get('type_etat');
    	if ($type_etat == 'transfert_monetaire_menage') 
    	{
    		$data = $this->ReportingManager->find_sum();
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

}
?>