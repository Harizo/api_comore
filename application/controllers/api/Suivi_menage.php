<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Suivi_menage extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('suivi_menage_model', 'SuivimenageManager');
        $this->load->model('sourcefinancement_model', 'SourcefinancementManager');
        $this->load->model('type_transfert_model', 'TypetransfertManager');
        $this->load->model('agence_p_model', 'AgencepaiementManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');
        $id_programme = $this->get('id_programme');
        $id_menage = $this->get('id_menage');
        $data = array() ;
        if ($cle_etrangere) 
        {
            $suivi_menage = $this->SuivimenageManager->findAllByMenage($cle_etrangere);

            

            if ($suivi_menage) 
            {
                $data['id'] = ($suivi_menage->id);
                $data['id_menage'] = ($suivi_menage->id_menage);
                $data['id_programme'] = unserialize($suivi_menage->id_programme);
                
            }
        }
        else
        {
            if ($id_programme && $id_menage) 
			{ 
                $id_prog = '"%'.$id_programme.'%"' ;
                $list_suivi_menage = $this->SuivimenageManager->findAllByProgrammeAndMenage($id_prog,$id_menage);
                if ($list_suivi_menage) 
                {
                    foreach ($list_suivi_menage as $key => $value) 
                    {
						$typetransfert = $this->TypetransfertManager->findById($value->id_type_transfert);
						$partenaire = $this->SourcefinancementManager->findById($value->id_partenaire);
						$acteur = $this->AgencepaiementManager->findById($value->id_acteur);
                        $data[$key]['id'] = $value->id;
                        $data[$key]['id_menage'] = ($value->id_menage);
                        $data[$key]['nomchefmenage'] = ($value->nomchefmenage);
                        $data[$key]['PersonneInscription'] = ($value->PersonneInscription);
                        $data[$key]['AgeInscrire'] = ($value->AgeInscrire);
                        $data[$key]['Addresse'] = ($value->Addresse);
                        $data[$key]['NumeroEnregistrement'] = ($value->NumeroEnregistrement);
                        $data[$key]['date_suivi'] = $value->date_suivi;
                        $data[$key]['id_partenaire'] = $value->id_partenaire;
                        $data[$key]['id_acteur'] = $value->id_acteur;
                        $data[$key]['id_type_transfert'] = $value->id_type_transfert;
                        $data[$key]['id_programme'] = ($id_programme);
                        $data[$key]['montant'] = $value->montant;
                        $data[$key]['observation'] = $value->observation;
                        $data[$key]['typetransfert'] = $typetransfert;
                        $data[$key]['partenaire'] = $partenaire;
                        $data[$key]['acteur'] = $acteur;
                    }
                }				
			} 
			else	
            if ($id_programme) 
            {
                $id_prog = '"'.$id_programme.'"' ;
                $list_suivi_menage = $this->SuivimenageManager->findAllByProgramme($id_prog);
                if ($list_suivi_menage) 
                {
                    foreach ($list_suivi_menage as $key => $value) 
                    {
                        $data[$key]['id'] = $value->id;
                        $data[$key]['NomInscrire'] = ($value->NomInscrire);
                        $data[$key]['PersonneInscription'] = ($value->PersonneInscription);
                        $data[$key]['AgeInscrire'] = ($value->AgeInscrire);
                        $data[$key]['Addresse'] = ($value->Addresse);
                        $data[$key]['NumeroEnregistrement'] = ($value->NumeroEnregistrement);
                       // $data['id_menage'] = ($suivi_menage->id_menage);
                        $data[$key]['id_programme'] = ($id_programme);
                        //$data[$key]['menage'] = $this->menageManager->findById($value->id_menage);
                       
                    }
                }
            }
            else
            {
                if ($id) 
                {
                    $data = $this->SuivimenageManager->findById($id);
                } 
                else 
                {
                    $data = $this->SuivimenageManager->findAll();                   
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
			$data = array(
				'id_menage' => $this->post('id_menage'),
				'id_programme' => $this->post('id_programme'),
				'id_partenaire' => $this->post('id_partenaire'),
				'id_acteur' => $this->post('id_acteur'),
				'id_type_transfert' => $this->post('id_type_transfert'),
				'date_suivi' => $this->post('date_suivi'),
				'montant' => $this->post('montant'),
				'observation' => $this->post('observation'),
			);               
            if ($id == 0) {
                if (!$data) 
                {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }

                $dataId = $this->SuivimenageManager->add($data);

                if (!is_null($dataId)) 
                {
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
                $update = $this->SuivimenageManager->update($id, $data);              
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
            $delete = $this->SuivimenageManager->delete($id);          
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