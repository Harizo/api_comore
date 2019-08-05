<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Reception_donnees_regional extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('reception_donnees_regional_model', 'reception_donnees_regionalManager');
        $this->load->model('menage_model', 'menageManager');
        $this->load->model('individu_model', 'individuManager');
        $this->load->model('enquete_individu_traitement_model', 'EnqueteindividuManager');
        $this->load->model('enquete_menage_traitement_model', 'EnquetemenageManager');
        $this->load->model('suivi_individu_model', 'SuiviindividuManager');
        $this->load->model('suivi_menage_model', 'SuivimenageManager');
        
    }

    public function index_get() 
    {
    	set_time_limit(0);
        ini_set ('memory_limit', '1024M');
        $id = $this->get('id');
		$data = array();

		//$menages = $this->menageManager->findAll();
		$individus = $this->SuivimenageManager->findAll();

		foreach ($individus as $key => $value) 
		{
			$reps = $this->reception_donnees_regionalManager->update_menage($value->id);
		}
		
        if (count($reps)>0) {
            $this->response([
                'status' => TRUE,
                'response' => $reps,
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

   
    public function index_post() 
    {
        $type_menu = $this->post('type_menu') ;

        switch ($type_menu) 
        {
        	case 'menage':

        	{
        		$data = array(
                    'DateInscription' => $this->post('DateInscription'),
                    'village_id' => $this->post('village_id'),
                    'NumeroEnregistrement' => $this->post('NumeroEnregistrement'),
                    'nomchefmenage' => $this->post('nomchefmenage'),
                    'PersonneInscription' => $this->post('PersonneInscription'),
                    'agechefdemenage' => $this->post('agechefdemenage'),
                    'SexeChefMenage' => $this->post('SexeChefMenage'),
                    'Addresse' => $this->post('Addresse')
                );  
                break;
        	}

        	case 'individu':
        		
        		break;
        	case 'enquete_individu':
        		
        		break;
        	case 'enquete_menage':
        		
        		break;
        	case 'suivi_individu':
        		
        		break;
        	case 'suivi_menage':
        		
        		break;
        	default:
        		
        		break;
        }
          
    }
}
?>