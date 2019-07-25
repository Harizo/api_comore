<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Enquete_menage_traitement extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('enquete_menage_traitement_model', 'EnquetemenageManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');
        $data = array() ;
        if ($cle_etrangere) 
        {
            $enquete_menage_traitement = $this->EnquetemenageManager->findAllByMenage($cle_etrangere);

            

            if ($enquete_menage_traitement) 
            {
                $data['id'] = ($enquete_menage_traitement->id);
                $data['id_serveur_centrale'] = ($enquete_menage_traitement->id_serveur_centrale);
                $data['id_menage'] = ($enquete_menage_traitement->id_menage);
                $data['bien_equipement'] = unserialize($enquete_menage_traitement->bien_equipement);
                $data['revetement_mur'] = unserialize($enquete_menage_traitement->revetement_mur);
                $data['revetement_sol'] = unserialize($enquete_menage_traitement->revetement_sol);
                $data['revetement_toit'] = unserialize($enquete_menage_traitement->revetement_toit);
                $data['source_eau'] = unserialize($enquete_menage_traitement->source_eau);
                $data['toilette'] = ($enquete_menage_traitement->toilette);
                $data['type_culture'] = unserialize($enquete_menage_traitement->type_culture);
                $data['type_elevage'] = unserialize($enquete_menage_traitement->type_elevage);
            }
        }
        else
        {
            if ($id) {
               
                $data = $this->EnquetemenageManager->findById($id);
                /*$data['id'] = $Enquetemenage->id;
                $data['code'] = $Enquetemenage->code;
                $data['libelle'] = $Enquetemenage->libelle;*/
                
            } else {
                $data = $this->EnquetemenageManager->findAll();
                /*if ($Enquetemenage) {
                    foreach ($Enquetemenage as $key => $value) {
                        
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
                    'id_serveur_centrale' => null,
                    'id_menage' => $this->post('id_menage'),
                    'source_eau' => serialize($this->post('source_eau')),
                    'toilette' => $this->post('toilette'),
                    'bien_equipement' => serialize($this->post('bien_equipement')),
                    'revetement_sol' => serialize($this->post('revetement_sol')),
                    'revetement_toit' => serialize($this->post('revetement_toit')),
                    'revetement_mur' => serialize($this->post('revetement_mur')),
                    'type_culture' => serialize($this->post('type_culture')),
                    'type_elevage' => serialize($this->post('type_elevage'))
                );               
                if (!$data) 
                {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }

                $dataId = $this->EnquetemenageManager->add($data);

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
            } 
            else 
            {
                $data = array(
                    'id_serveur_centrale' => $this->post('id_serveur_centrale'),
                    'id_menage' => $this->post('id_menage'),
                    'source_eau' => serialize($this->post('source_eau')),
                    'toilette' => $this->post('toilette'),
                    'bien_equipement' => serialize($this->post('bien_equipement')),
                    'revetement_sol' => serialize($this->post('revetement_sol')),
                    'revetement_toit' => serialize($this->post('revetement_toit')),
                    'revetement_mur' => serialize($this->post('revetement_mur')),
                    'type_culture' => serialize($this->post('type_culture')),
                    'type_elevage' => serialize($this->post('type_elevage'))
                );                 
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->EnquetemenageManager->update($id, $data);              
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
            $delete = $this->EnquetemenageManager->delete($id);          
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