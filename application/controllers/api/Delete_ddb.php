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
        }  else if($nom_table == 'see_village') {
 			$datemodification=null;
			if($this->post('datemodification')) {
				$datemodification = $this->post('datemodification');
			} 
           $data = array(
                'id' => $this->post('id'),
                'commune_id' => $this->post('commune_id'),
                'Code' => $this->post('Code'),
                'Village' => $this->post('Village'),
                'programme_id' => $this->post('programme_id'),
                'zone_id' => $this->post('zone_id'),
                'nbrpopulation' => $this->post('nbrpopulation'),
                'a_ete_modifie' => $this->post('a_ete_modifie'),
                'supprime' => $this->post('supprime'),
                'userid' => $this->post('userid'),
                'datemodification' => $datemodification,
            ); 			
		} else if ($nom_table == 'see_commune') {
			$programme_id=null;
			if($this->post('programme_id')) {
				$programme_id = $this->post('programme_id');
			} 
			$zone_id=null;
			if($this->post('zone_id')) {
				$zone_id = $this->post('zone_id');
			} 
			$datemodification=null;
			if($this->post('datemodification')) {
				$datemodification = $this->post('datemodification');
			} 
            $data = array(
                'id' => $this->post('id'),
                'Code' => $this->post('Code'),
                'Commune' => $this->post('Commune'),
                'zone_id' => $zone_id,
                'nombremenage' => $this->post('nombremenage'),
                'programme_id' => $programme_id,
                'region_id' => $this->post('region_id'),
                'a_ete_modifie' => $this->post('a_ete_modifie'),
                'supprime' => $this->post('supprime'),
                'userid' => $this->post('userid'),
                'datemodification' => $datemodification,
            ); 						
		} else if($nom_table == 'see_region') {
			$datemodification=null;
			if($this->post('datemodification')) {
				$datemodification = $this->post('datemodification');
			} 
            $data = array(
                'id' => $this->post('id'),
                'ile_id' => $this->post('ile_id'),
                'Code' => $this->post('Code'),
                'Region' => $this->post('Region'),
                'programme_id' => $this->post('programme_id'),
                'a_ete_modifie' => $this->post('a_ete_modifie'),
                'supprime' => $this->post('supprime'),
                'userid' => $this->post('userid'),
                'datemodification' => $datemodification,
            ); 						
		} else if($nom_table == 'see_ile') {
			$programme_id=null;
			if($this->post('programme_id')) {
				$programme_id = $this->post('programme_id');
			} 
			$a_ete_modifie=null;
			if($this->post('a_ete_modifie')) {
				$a_ete_modifie = $this->post('a_ete_modifie');
			} 
			$supprime=null;
			if($this->post('supprime')) {
				$supprime = $this->post('supprime');
			} 
			$userid=null;
			if($this->post('userid')) {
				$userid = $this->post('userid');
			} 
			$datemodification=null;
			if($this->post('datemodification')) {
				$datemodification = $this->post('datemodification');
			} 
            $data = array(
                'id' => $this->post('id'),
                'Code' => $this->post('Code'),
                'Ile' => $this->post('Ile'),
                'programme_id' => $programme_id,
                'a_ete_modifie' => $a_ete_modifie,
                'supprime' => $supprime,
                'userid' => $userid,
                'datemodification' => $datemodification,
            ); 			
		} else {
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
    	} else 	{
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