<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Individu_programme extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('individu_programme_model', 'EnqueteindividuManager');
        $this->load->model('individu_model', 'individuManager');
    }

    public function index_get() {
        $id = $this->get('id');

        $cle_etrangere = $this->get('cle_etrangere');
        $id_programme = $this->get('id_programme');
        $data = array() ;
        if ($cle_etrangere) 
        {
            $individu_programme = $this->EnqueteindividuManager->findAllByindividu($cle_etrangere);

            

            if ($individu_programme) 
            {
                $data['id'] = ($individu_programme->id);
                $data['id_individu'] = ($individu_programme->id_individu);
                $data['id_programme'] = unserialize($individu_programme->id_programme);
                
            }
        }
        else
        {
            if ($id_programme) 
            {
                $id_prog = '"'.$id_programme.'"' ;
                $list_individu_programme = $this->EnqueteindividuManager->findAllByProgramme($id_prog);
                if ($list_individu_programme) 
                {
                    foreach ($list_individu_programme as $key => $value) 
                    {
                        $data[$key]['id'] = $value->id;
                        $data[$key]['NomInscrire'] = ($value->NomInscrire);
                        $data[$key]['PersonneInscription'] = ($value->PersonneInscription);
                        $data[$key]['AgeInscrire'] = ($value->AgeInscrire);
                        $data[$key]['Addresse'] = ($value->Addresse);
                        $data[$key]['NumeroEnregistrement'] = ($value->NumeroEnregistrement);
                       // $data['id_individu'] = ($individu_programme->id_individu);
                        $data[$key]['id_programme'] = ($id_programme);
                        //$data[$key]['individu'] = $this->individuManager->findById($value->id_individu);
                       
                    }
                }
            }
            else
            {
                if ($id) 
                {
                    $data = $this->EnqueteindividuManager->findById($id);
                } 
                else 
                {
                    $data = $this->EnqueteindividuManager->findAll();                   
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
                    'id_individu' => $this->post('id_individu'),
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
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
            } 
            else 
            {
                $data = array(
                    'id_individu' => $this->post('id_individu'),
                    'id_programme' => serialize($this->post('id_programme'))
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