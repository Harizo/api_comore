<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Beneficiaire_model extends CI_Model {
    protected $table = 'beneficiaire';

    public function add($beneficiaire) {
        $this->db->set($this->_set($beneficiaire))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }
    public function update($id, $beneficiaire) {
        $this->db->set($this->_set($beneficiaire))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1) {
            return true;
        }else{
            return null;
        }                      
    }
    public function _set($beneficiaire) {
        return array(
			'code'                   => $beneficiaire['code'],
			'nom'                    => $beneficiaire['nom'],
			'prenom'                 => $beneficiaire['prenom'],
			'cin'                    => $beneficiaire['cin'],
			'chef_menage'            => $beneficiaire['chef_menage'],
			'adresse'                => $beneficiaire['adresse'],
			'date_naissance'         => $beneficiaire['date_naissance'],
			'profession'             => $beneficiaire['profession'],
			'situation_matrimoniale' => $beneficiaire['situation_matrimoniale'],
			'sexe'                   => $beneficiaire['sexe'],
			'date_inscription'       => $beneficiaire['date_inscription'],
			'revenu_mensuel'         => $beneficiaire['revenu_mensuel'],
			'depense_mensuel'        => $beneficiaire['depense_mensuel'],
			'id_fokontany'           => $beneficiaire['id_fokontany'],
			'id_type_beneficiaire'   => $beneficiaire['id_type_beneficiaire']
        );
    }
    public function delete($id) {
        $this->db->where('id', (int) $id)->delete($this->table);
        if($this->db->affected_rows() === 1) {
            return true;
        }else{
            return null;
        }  
    }
    public function findAll() {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('id')
                        ->get()
                        ->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                 
    }
    public function findByIdFokontany($id_fokontany) {
        $result =  $this->db->select('*')
                        ->from($this->table)
						->where("id_fokontany", $id_fokontany)
                        ->order_by('nom')
                        ->order_by('prenom')
                        ->get()
                        ->result();
        if($result) {
            return $result;
        }else{
            return null;
        }                 
    }
    public function findById($id)  {
        $this->db->where("id", $id);
        $q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }
}
?>
