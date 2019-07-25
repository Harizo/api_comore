<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//harizo
// afaka fafana refa ts ilaina
require APPPATH . '/libraries/REST_Controller.php';

class Individu extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('individu_model', 'individuManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');

        if ($cle_etrangere) 
        {
            $data = $this->individuManager->findAllByMenage($cle_etrangere);
        }
        else
        {
            if ($id) {
               
                $data = $this->individuManager->findById($id);
               
                
            } else {
                $data = $this->individuManager->findAll();
                
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
            if ($id == 0) {
                $data = array(
                    'id_serveur_centrale' => null,
                    'menage_id' => $this->post('menage_id'),
                    'Nom' => $this->post('Nom'),
                    'DateNaissance' => $this->post('DateNaissance'),
                    'Activite' => $this->post('Activite'),
                    'travailleur' => $this->post('travailleur'),
                    'sexe' => $this->post('sexe')
                );               
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->individuManager->add($data);              
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
            } else {
                $data = array(
                    'id_serveur_centrale' => $this->post('id_serveur_centrale'),
                    'menage_id' => $this->post('menage_id'),
                    'Nom' => $this->post('Nom'),
                    'DateNaissance' => $this->post('DateNaissance'),
                    'Activite' => $this->post('Activite'),
                    'travailleur' => $this->post('travailleur'),
                    'sexe' => $this->post('sexe')
                );              
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->individuManager->update($id, $data);              
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
            $delete = $this->individuManager->delete($id);          
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
/* End of file controllername.php */
/* Location: ./application/controllers/controllername.php */