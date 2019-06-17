<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Menage_programme extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('menage_programme_model', 'EnquetemenageManager');
        $this->load->model('menage_model', 'menageManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');
        $id_programme = $this->get('id_programme');
        $data = array() ;
        if ($cle_etrangere) 
        {
            $menage_programme = $this->EnquetemenageManager->findAllByMenage($cle_etrangere);

            

            if ($menage_programme) 
            {
                $data['id'] = ($menage_programme->id);
                $data['id_menage'] = ($menage_programme->id_menage);
                $data['id_programme'] = unserialize($menage_programme->id_programme);
                
            }
        }
        else
        {
            if ($id_programme) 
            {
                $id_prog = '"'.$id_programme.'"' ;
                $list_menage_programme = $this->EnquetemenageManager->findAllByProgramme($id_prog);
                if ($list_menage_programme) 
                {
                    foreach ($list_menage_programme as $key => $value) 
                    {
                        $data[$key]['id'] = $value->id;
                        $data[$key]['NomInscrire'] = ($value->NomInscrire);
                        $data[$key]['PersonneInscription'] = ($value->PersonneInscription);
                        $data[$key]['AgeInscrire'] = ($value->AgeInscrire);
                        $data[$key]['Addresse'] = ($value->Addresse);
                        $data[$key]['NumeroEnregistrement'] = ($value->NumeroEnregistrement);
                       // $data['id_menage'] = ($menage_programme->id_menage);
                        $data[$key]['id_programme'] = ($id_programme);
                        //$data[$key]['menage'] = $this->menageManager->findById($value->id_menage);
                       
                    }
                }
            }
            else
            {
                if ($id) 
                {
                    $data = $this->EnquetemenageManager->findById($id);
                } 
                else 
                {
                    $data = $this->EnquetemenageManager->findAll();                   
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
        if ($supprimer == 0) 
        {
            if ($id == 0) 
            {
                $data = array(
                    'id_menage' => $this->post('id_menage'),
                    'id_programme' => serialize($this->post('id_programme'))
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
                    'id_menage' => $this->post('id_menage'),
                    'id_programme' => serialize($this->post('id_programme'))
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