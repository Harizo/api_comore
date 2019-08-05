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
                $list_suivi_menage = $this->SuivimenageManager->findAllByProgrammeAndMenage($id_programme,$id_menage);
                if ($list_suivi_menage) 
                {
						$nutrition=array();
						$transfert_argent=array();
                    foreach ($list_suivi_menage as $key => $value) 
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
 						$tmp=array();
						$tmp['id'] = $value->id;
                        $tmp['id_menage'] = ($value->id_menage);
                        $tmp['nomchefmenage'] = ($value->nomchefmenage);
                        $tmp['PersonneInscription'] = ($value->PersonneInscription);
                        $tmp['AgeInscrire'] = ($value->AgeInscrire);
                        $tmp['Addresse'] = ($value->Addresse);
                        $tmp['NumeroEnregistrement'] = ($value->NumeroEnregistrement);
                        $tmp['date_suivi'] = $value->date_suivi;
                        $tmp['id_partenaire'] = $value->id_partenaire;
                        $tmp['id_acteur'] = $value->id_acteur;
                        $tmp['id_type_transfert'] = $value->id_type_transfert;
                        $tmp['id_programme'] = ($id_programme);
                        $tmp['montant'] = $value->montant;
                        $tmp['observation'] = $value->observation;
                        $tmp['typetransfert'] = $typetransfert;
                        $tmp['partenaire'] = $partenaire;
                        $tmp['acteur'] = $acteur;
						$tmp['poids'] = $value->poids;
						$tmp['perimetre_bracial'] = $value->perimetre_bracial;
						$tmp['age_mois'] = $value->age_mois;
						$tmp['taille'] = $value->taille;
						$tmp['zscore'] = $value->zscore;
						$tmp['mois_grossesse'] = $value->mois_grossesse;
						if(intval($id_programme)==3) {
							// Nutrition
							$nutrition[] =$tmp;
						} else if(intval($id_programme)==1) {
							// Transfert monétaire
							$transfert_argent[]=$tmp;
						}				   
                    }
					$data[0]['nutrition']=$nutrition;
					$data[0]['transfert_argent']=$transfert_argent;					
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
		$id_partenaire=null;
		$id_acteur=null;
		$id_type_transfert=null;
		$montant=null;
		$poids=null;
		$perimetre_bracial=null;
		$age_mois=null;
		$taille=null;
		$zscore=null;
		$mois_grossesse=null;
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
		$tmp=$this->post('montant') ;
		if($tmp && intval($tmp) >0) {
			$montant=$tmp;
		}
		$tmp=$this->post('poids') ;
		if($tmp && intval($tmp) >0) {
			$poids=$tmp;
		}
		$tmp=$this->post('perimetre_bracial') ;
		if($tmp && intval($tmp) >0) {
			$perimetre_bracial=$tmp;
		}
		$tmp=$this->post('age_mois') ;
		if($tmp && intval($tmp) >0) {
			$age_mois=$tmp;
		}
		$tmp=$this->post('taille') ;
		if($tmp && intval($tmp) >0) {
			$taille=$tmp;
		}
		$tmp=$this->post('zscore') ;
		if($tmp) {
			$zscore=$tmp;
		}
		$tmp=$this->post('mois_grossesse') ;
		if($tmp && intval($tmp) >0) {
			$mois_grossesse=$tmp;
		}
        if ($supprimer == 0) {
			$data = array(
				'id_menage' => $this->post('id_menage'),
				'id_programme' => $this->post('id_programme'),
				'id_partenaire' => $id_partenaire,
				'id_acteur' => $id_acteur,
				'id_type_transfert' => $id_type_transfert,
				'date_suivi' => $this->post('date_suivi'),
				'montant' => $montant,
				'poids' => $poids,
				'perimetre_bracial' => $perimetre_bracial,
				'age_mois' => $age_mois,
				'taille' => $taille,
				'zscore' => $zscore,
				'mois_grossesse' => $mois_grossesse,
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
                        'response' => $id,
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