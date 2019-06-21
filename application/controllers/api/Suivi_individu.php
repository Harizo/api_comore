<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Suivi_individu extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('suivi_individu_model', 'SuiviindividuManager');
        $this->load->model('sourcefinancement_model', 'SourcefinancementManager');
        $this->load->model('type_transfert_model', 'TypetransfertManager');
        $this->load->model('agence_p_model', 'AgencepaiementManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');
        $id_programme = $this->get('id_programme');
        $id_individu = $this->get('id_individu');
        $data = array() ;
        if ($cle_etrangere) 
        {
            $suivi_individu = $this->SuiviindividuManager->findAllByMenage($cle_etrangere);

            

            if ($suivi_individu) 
            {
                $data['id'] = ($suivi_individu->id);
                $data['id_individu'] = ($suivi_individu->id_individu);
                $data['id_programme'] = unserialize($suivi_individu->id_programme);
                
            }
        }
        else
        {
            if ($id_programme && $id_individu) 
			{ 
                $id_prog = '"%'.$id_programme.'%"' ;
                $list_suivi_individu = $this->SuiviindividuManager->findAllByProgrammeAndIndividu($id_programme,$id_individu);
                if ($list_suivi_individu) 
                {
                    foreach ($list_suivi_individu as $key => $value) 
                    {
						$typetransfert = array();
						if($value->id_type_transfert && intval($value->id_type_transfert) >0) {
							$typetransfert = $this->TypetransfertManager->findById($value->id_type_transfert);
						}	
						$partenaire = array();
						if($value->id_partenaire && intval($value->id_partenaire) >0) {
							$partenaire = $this->SourcefinancementManager->findById($value->id_partenaire);
						}	
						$acteur = array();
						if($value->id_acteur && intval($value->id_acteur) >0) {
							$acteur = $this->AgencepaiementManager->findById($value->id_acteur);
						}	
                        $data[$key]['id'] = $value->id;
                        $data[$key]['id_individu'] = ($value->id_individu);
                        $data[$key]['Nom'] = ($value->Nom);
                        $data[$key]['DateNaissance'] = ($value->DateNaissance);
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
                        $data[$key]['poids'] = $value->poids;
                        $data[$key]['perimetre_bracial'] = $value->perimetre_bracial;
                        $data[$key]['age_mois'] = $value->age_mois;
                        $data[$key]['taille'] = $value->taille;
                        $data[$key]['zscore'] = $value->zscore;
                        $data[$key]['mois_grossesse'] = $value->mois_grossesse;
                   }
                }				
			} 
			else	
            if ($id_programme) 
            {
                $id_prog = '"'.$id_programme.'"' ;
                $list_suivi_individu = $this->SuiviindividuManager->findAllByProgramme($id_prog);
                if ($list_suivi_individu) 
                {
                    foreach ($list_suivi_individu as $key => $value) 
                    {
                        $data[$key]['id'] = $value->id;
                        $data[$key]['NomInscrire'] = ($value->NomInscrire);
                        $data[$key]['PersonneInscription'] = ($value->PersonneInscription);
                        $data[$key]['AgeInscrire'] = ($value->AgeInscrire);
                        $data[$key]['Addresse'] = ($value->Addresse);
                        $data[$key]['NumeroEnregistrement'] = ($value->NumeroEnregistrement);
                       // $data['id_individu'] = ($suivi_individu->id_individu);
                        $data[$key]['id_programme'] = ($id_programme);
                        //$data[$key]['menage'] = $this->menageManager->findById($value->id_individu);
                       
                    }
                }
            }
            else
            {
                if ($id) 
                {
                    $data = $this->SuiviindividuManager->findById($id);
                } 
                else 
                {
                    $data = $this->SuiviindividuManager->findAll();                   
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
		$id_partenaire=null;
		$id_acteur=null;
		$id_type_transfert=null;
		$tmp=$this->post('id_partenaire') ;
		if($tmp && intval($tmp) >0) {
			$id_partenaire=$tmp;
		}
		$tmp=$this->post('id_acteur') ;
		if($tmp && intval($tmp) >0) {
			$id_acteur=$tmp;
		}
		$tmp=$this->post('id_type_transfert') ;
		if($tmp && intval($tmp) >0) {
			$id_type_transfert=$tmp;
		}
        if ($supprimer == 0) {
			$data = array(
				'id_individu' => $this->post('id_individu'),
				'id_programme' => $this->post('id_programme'),
				'id_partenaire' => $this->post('id_partenaire'),
				'id_acteur' => $this->post('id_acteur'),
				'id_type_transfert' => $this->post('id_type_transfert'),
				'date_suivi' => $this->post('date_suivi'),
				'montant' => $this->post('montant'),
				'observation' => $this->post('observation'),
				'poids' => $this->post('poids'),
				'perimetre_bracial' => $this->post('perimetre_bracial'),
				'age_mois' => $this->post('age_mois'),
				'taille' => $this->post('taille'),
				'zscore' => $this->post('zscore'),
				'mois_grossesse' => $this->post('mois_grossesse'),
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

                $dataId = $this->SuiviindividuManager->add($data);

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
                $update = $this->SuiviindividuManager->update($id, $data);              
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
            $delete = $this->SuiviindividuManager->delete($id);          
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