<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//harizo
// afaka fafana refa ts ilaina
require APPPATH . '/libraries/REST_Controller.php';

class Menage extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('menage_model', 'menageManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');

        $max_id = $this->get('max_id');

        if ($max_id == 1) 
        {
            $data = $this->menageManager->find_max_id();
        }
        else
        {
            if ($cle_etrangere) 
            {
                $data = $this->menageManager->findAllByVillage($cle_etrangere);
            }
            else
            {
                if ($id) {
                   
                    $data = $this->menageManager->findById($id);
                    
                    
                } else {
                    $data = $this->menageManager->findAll();
                   
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
            if ($id == 0) {
               
                $data = array(
                    'DateInscription' => $this->post('DateInscription'),
                    'village_id' => $this->post('village_id'),
                    'NumeroEnregistrement' => $this->post('NumeroEnregistrement'),
                    'NomInscrire' => $this->post('NomInscrire'),
                    'PersonneInscription' => $this->post('PersonneInscription'),
                    'AgeInscrire' => $this->post('AgeInscrire'),
                    'SexeChefMenage' => $this->post('SexeChefMenage'),
                    'Addresse' => $this->post('Addresse')
                );               
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->menageManager->add($data);              
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
                    'DateInscription' => $this->post('DateInscription'),
                    'village_id' => $this->post('village_id'),
                    'NumeroEnregistrement' => $this->post('NumeroEnregistrement'),
                    'NomInscrire' => $this->post('NomInscrire'),
                    'PersonneInscription' => $this->post('PersonneInscription'),
                    'AgeInscrire' => $this->post('AgeInscrire'),
                    'SexeChefMenage' => $this->post('SexeChefMenage'),
                    'Addresse' => $this->post('Addresse')
                );              
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->menageManager->update($id, $data);              
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
            $delete = $this->menageManager->delete($id);          
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