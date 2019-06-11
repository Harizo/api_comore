<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//harizo
// afaka fafana refa ts ilaina
require APPPATH . '/libraries/REST_Controller.php';

class Beneficiaire extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('beneficiaire_model', 'BeneficiaireManager');
        $this->load->model('fokontany_model', 'FokontanyManager');
        $this->load->model('type_beneficiaire_model', 'TypebeneficiaireManager');
    }
	public function convertDateAngular($daty){
		if(isset($daty) && $daty != ""){
			if(strlen($daty) >33) {
				$daty=substr($daty,0,33);
			}
			$xx  = new DateTime($daty);
			if($xx->getTimezone()->getName() == "Z"){
				$xx->add(new DateInterval("P1D"));
				return $xx->format("Y-m-d");
			}else{
				return $xx->format("Y-m-d");
			}
		}else{
			return null;
		}
	}
    public function index_get() {
        $id = $this->get('id');
        if ($id) {
            $data = $this->BeneficiaireManager->findById($id);
            if (!$data)
                $data = array();
        } else {
			$data=array();
			$menu = $this->BeneficiaireManager->findAll();	
            if ($menu) {
                foreach ($menu as $key => $value) {
                    $fokontany = array();
                    $type_emp = $this->FokontanyManager->findById($value->id_fokontany);
					if(count($type_emp) >0) {
						$fokontany=$type_emp;
					}	
                    $type_beneficiaire = array();
                    $type_emp = $this->TypebeneficiaireManager->findById($value->id_type_beneficiaire);
					if(count($type_emp) >0) {
						$type_beneficiaire=$type_emp;
					}	
                    $data[$key]['id'] = $value->id;
                    $data[$key]['code'] = $value->code;
                    $data[$key]['nom'] = $value->nom;
                    $data[$key]['prenom'] = $value->prenom;
                    $data[$key]['cin'] = $value->cin;
                    $data[$key]['chef_menage'] = $value->chef_menage;
                    $data[$key]['adresse'] = $value->adresse;
                    $data[$key]['date_naissance'] = $value->date_naissance;
                    $data[$key]['profession'] = $value->profession;
                    $data[$key]['situation_matrimoniale'] = $value->situation_matrimoniale;
                    $data[$key]['sexe'] = $value->sexe;
                    $data[$key]['date_inscription'] = $value->date_inscription;
                    $data[$key]['revenu_mensuel'] = $value->revenu_mensuel;
                    $data[$key]['depense_mensuel'] = $value->depense_mensuel;
                    $data[$key]['id_fokontany'] = $value->id_fokontany;
                    $data[$key]['fokontany'] = $fokontany;
                    $data[$key]['id_type_beneficiaire'] = $value->id_type_beneficiaire;
                    $data[$key]['type_beneficiaire'] = $type_beneficiaire;
                }
            }
            if (!$data)
                $data = array();
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
		$date_naissance = $this->convertDateAngular($this->post('date_naissance'));
		$date_inscription = $this->convertDateAngular($this->post('date_inscription'));
		$id_fokontany=null;
		$id_type_beneficiaire=null;
		$tmp=$this->post('id_fokontany');
		if(isset($tmp) && $tmp !="" && intval($tmp) >0) {
			$id_fokontany=$tmp;
		}
		$tmp=$this->post('id_type_beneficiaire');
		if(isset($tmp) && $tmp !="" && intval($tmp) >0) {
			$id_type_beneficiaire=$tmp;
		}
		$data = array(
			'code'                   => $this->post('code'),
			'nom'                    => $this->post('nom'),
			'prenom'                 => $this->post('prenom'),
			'cin'                    => $this->post('cin'),
			'chef_menage'            => $this->post('chef_menage'),
			'adresse'                => $this->post('adresse'),
			'date_naissance'         => $date_naissance,
			'profession'             => $this->post('profession'),
			'situation_matrimoniale' => $this->post('situation_matrimoniale'),
			'sexe'                   => $this->post('sexe'),
			'date_inscription'       => $date_inscription,
			'revenu_mensuel'         => $this->post('revenu_mensuel'),
			'depense_mensuel'        => $this->post('depense_mensuel'),
			'id_fokontany'           => $id_fokontany,
			'id_type_beneficiaire'   => $id_type_beneficiaire
		);
        if ($supprimer == 0) {
            if ($id == 0) {
                if (!$data) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $dataId = $this->BeneficiaireManager->add($data);
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
                if (!$data || !$id) {
                    $this->response([
                        'status' => FALSE,
                        'response' => 0,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_BAD_REQUEST);
                }
                $update = $this->BeneficiaireManager->update($id, $data);
                if(!is_null($update)) {
                    $this->response([
                        'status' => TRUE,
                        'response' => 1,
                        'message' => 'Update data success'
                            ], REST_Controller::HTTP_OK);
                } else  {
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No request found'
                            ], REST_Controller::HTTP_OK);
                }
            }
        } else  {
            if (!$id) {
            $this->response([
                'status' => FALSE,
                'response' => 0,
                'message' => 'No request found'
                    ], REST_Controller::HTTP_BAD_REQUEST);
            }
            $delete = $this->BeneficiaireManager->delete($id);
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
                        ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }
}
?>