<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Enquete_individu_traitement extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('enquete_individu_traitement_model', 'EnqueteindividuManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');
        $data = array() ;
        if ($cle_etrangere) 
        {
            $enquete_individu_traitement = $this->EnqueteindividuManager->findAllByindividu($cle_etrangere);

            

            if ($enquete_individu_traitement) 
            {
                $data['id'] = ($enquete_individu_traitement->id);
                $data['id_individu'] = $enquete_individu_traitement->id_individu;
                $data['id_lien_parente'] = $enquete_individu_traitement->id_lien_parente;
                $data['id_handicap_visuel'] = $enquete_individu_traitement->id_handicap_visuel;
                $data['id_handicap_parole'] = $enquete_individu_traitement->id_handicap_parole;
                $data['id_handicap_auditif'] = $enquete_individu_traitement->id_handicap_auditif;
                $data['id_handicap_mental'] = $enquete_individu_traitement->id_handicap_mental;
                $data['id_handicap_moteur'] = $enquete_individu_traitement->id_handicap_moteur;
                $data['vaccins'] = unserialize($enquete_individu_traitement->vaccins);
                $data['poids'] = $enquete_individu_traitement->poids;
                $data['perimetre_bracial'] = $enquete_individu_traitement->perimetre_bracial;
                $data['age_mois'] = $enquete_individu_traitement->age_mois;
                $data['taille'] = $enquete_individu_traitement->taille;
                $data['zscore'] = $enquete_individu_traitement->zscore;
              
            }
        }
        else
        {
            if ($id) {
               
                $data = $this->EnqueteindividuManager->findById($id);
                /*$data['id'] = $Enqueteindividu->id;
                $data['code'] = $Enqueteindividu->code;
                $data['libelle'] = $Enqueteindividu->libelle;*/
                
            } else {
                $data = $this->EnqueteindividuManager->findAll();
                /*if ($Enqueteindividu) {
                    foreach ($Enqueteindividu as $key => $value) {
                        
                        $data[$key]['id'] = $value->id;
                        $data[$key]['code'] = $value->code;
                        $data[$key]['libelle'] = $value->libelle;
                        
                    };
                } else
                    $data = array();*/
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
        if ($supprimer == 0) 
        {
            if ($id == 0) 
            {
                $data = array(
                    'id_individu' 			=> $this->post('id_individu'),
                    'id_lien_parente' 		=> $this->post('id_lien_parente'),
                    'id_handicap_visuel' 	=> $this->post('id_handicap_visuel'),
                    'id_handicap_parole' 	=> $this->post('id_handicap_parole'),
                    'id_handicap_auditif' 	=> $this->post('id_handicap_auditif'),
                    'id_handicap_mental' 	=> $this->post('id_handicap_mental'),
                    'id_handicap_moteur' 	=> $this->post('id_handicap_moteur'),
                    'vaccins'               => serialize($this->post('vaccins')),
                    'poids'                 => $this->post('poids'),
                    'perimetre_bracial'     => $this->post('perimetre_bracial'),
                    'age_mois'              => $this->post('age_mois'),
                    'taille'                => $this->post('taille'),
                    'zscore'                => $this->post('zscore'),
                    'mois_grossesse'        => $this->post('mois_grossesse')
                    
                );               
                if (!$data) 
                {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'Data 0'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }

                $dataId = $this->EnqueteindividuManager->add($data);

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
                        'message' => 'No request foundQSqs'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } 
            else 
            {
                $data = array(
                    'id_individu'           => $this->post('id_individu'),
                    'id_lien_parente'       => $this->post('id_lien_parente'),
                    'id_handicap_visuel'    => $this->post('id_handicap_visuel'),
                    'id_handicap_parole'    => $this->post('id_handicap_parole'),
                    'id_handicap_auditif'   => $this->post('id_handicap_auditif'),
                    'id_handicap_mental'    => $this->post('id_handicap_mental'),
                    'id_handicap_moteur'    => $this->post('id_handicap_moteur'),
                    'vaccins'               => serialize($this->post('vaccins')),
                    'poids'                 => $this->post('poids'),
                    'perimetre_bracial'     => $this->post('perimetre_bracial'),
                    'age_mois'              => $this->post('age_mois'),
                    'taille'                => $this->post('taille'),
                    'zscore'                => $this->post('zscore'),
                    'mois_grossesse'                => $this->post('mois_grossesse')
                );                 
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->EnqueteindividuManager->update($id, $data);              
                if(!is_null($update)){
                    $this->response([
                        'status' => TRUE, 
                        'response' => $update,
                        'message' => 'Update data success'
                            ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No request found dqsdqsd'
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
            $delete = $this->EnqueteindividuManager->delete($id);          
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