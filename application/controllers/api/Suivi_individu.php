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
        $this->load->model('enquete_menage_model', 'EnquetemenageManager');
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
				$data=array();
                $id_prog = '"%'.$id_programme.'%"' ;
                $list_suivi_individu = $this->SuiviindividuManager->findAllByProgrammeAndIndividu($id_programme,$id_individu);
                if ($list_suivi_individu) 
                {
						$nutrition=array();
						$transfert_argent=array();
						$mariage_precoce=array();
						$promotion_genre=array();
					
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
						$situation_matrimoniale = array();
						if($value->id_situation_matrimoniale && intval($value->id_situation_matrimoniale) >0) {
							$situation_matrimoniale = $this->EnquetemenageManager->findById($value->id_situation_matrimoniale,"situation_matrimoniale");
						}	
						$type_mariage = array();
						if($value->id_type_mariage && intval($value->id_type_mariage) >0) {
							$type_mariage = $this->EnquetemenageManager->findById($value->id_type_mariage,"type_mariage");
						}	
						$type_violence = array();
						if($value->id_type_violence && intval($value->id_type_violence) >0) {
							$type_violence = $this->EnquetemenageManager->findById($value->id_type_violence,"type_violence");
						}	
						$tmp=array();
						$tmp['id'] = $value->id;
						$tmp['id_individu'] = ($value->id_individu);
						$tmp['Nom'] = ($value->Nom);
						$tmp['DateNaissance'] = ($value->DateNaissance);
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
						$tmp['cause_mariage'] = $value->cause_mariage;
						$tmp['age'] = $value->age;
						$tmp['infraction'] = $value->infraction;
						$tmp['lieu_infraction'] = $value->lieu_infraction;
						$tmp['id_situation_matrimoniale'] = $value->id_situation_matrimoniale;
						$tmp['situation_matrimoniale'] = $situation_matrimoniale;
						$tmp['id_type_mariage'] = $value->id_type_mariage;
						$tmp['type_mariage'] = $type_mariage;
						$tmp['id_type_violence'] = $value->id_type_violence;
						$tmp['type_violence'] = $type_violence;
						$tmp['type_formation_recue'] = $value->type_formation_recue;
						if(intval($id_programme)==5) {
							// Promotion genre : séparer en 2 les enregistrements
							if(intval($value->id_type_mariage) >0) {
								$mariage_precoce[]=$tmp;
							}	
							if(intval($value->id_type_violence) >0) {
								$promotion_genre[]=$tmp;
							} 
						} else if(intval($id_programme)==3) {
							// Nutrition
							$nutrition[] =$tmp;
						} else  {
							// Transfert monétaire par défaut
							$transfert_argent[]=$tmp;
						}				   
					}
					$data[0]['mariage_precoce']=$mariage_precoce;
					$data[0]['promotion_genre']=$promotion_genre;
					$data[0]['nutrition']=$nutrition;
					$data[0]['transfert_argent']=$transfert_argent;					
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
		$id_situation_matrimoniale=null;
		$id_type_mariage=null;
		$id_type_violence=null;
		$age=null;
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
		$tmp=$this->post('id_situation_matrimoniale') ;
		if($tmp && intval($tmp) >0) {
			$id_situation_matrimoniale=$tmp;
		}
		$tmp=$this->post('id_type_mariage') ;
		if($tmp && intval($tmp) >0) {
			$id_type_mariage=$tmp;
		}
		$tmp=$this->post('id_type_violence') ;
		if($tmp && intval($tmp) >0) {
			$id_type_violence=$tmp;
		}
		$tmp=$this->post('age') ;
		if($tmp && intval($tmp) >0) {
			$age=$tmp;
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
				'id_individu' => $this->post('id_individu'),
				'id_programme' => $this->post('id_programme'),
				'id_partenaire' => $id_partenaire,
				'id_acteur' => $id_acteur,
				'id_type_transfert' => $id_type_transfert,
				'date_suivi' => $this->post('date_suivi'),
				'montant' => $montant,
				'observation' => $this->post('observation'),
				'poids' => $poids,
				'perimetre_bracial' => $perimetre_bracial,
				'age_mois' => $age_mois,
				'taille' => $taille,
				'zscore' => $zscore,
				'mois_grossesse' => $mois_grossesse,
				'cause_mariage' => $this->post('cause_mariage'),
				'age' => $age,
				'lieu_infraction' => $this->post('lieu_infraction'),
				'infraction'      => $this->post('infraction'),
				'id_situation_matrimoniale' => $id_situation_matrimoniale,
				'id_type_mariage' => $id_type_mariage,
				'id_type_violence' => $id_type_violence,
				'type_formation_recue' => $this->post('type_formation_recue'),
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