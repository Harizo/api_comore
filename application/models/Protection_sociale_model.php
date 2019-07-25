<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Protection_sociale_model extends CI_Model {
    protected $table = 'see_celluleprotectionsociale';

    public function add($protection_sociale)  {
        $this->db->set($this->_set($protection_sociale))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)  {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }

    public function add_down($protection_sociale, $id)  {
        $this->db->set($this->_set_down($protection_sociale, $id))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)  {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }
    public function update($id, $protection_sociale)  {
        $this->db->set($this->_set($protection_sociale))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)  {
            return true;
        }else{
            return null;
        }                      
    }
    public function _set($protection_sociale) {
        return array(
            'Code' => $protection_sociale['Code'],
            'Nom' => $protection_sociale['Nom'],
            'Contact' => $protection_sociale['Contact'],
            'Representant' => $protection_sociale['Representant'],
            'NumeroTelephone' => $protection_sociale['NumeroTelephone'],
            'programme_id' => $protection_sociale['programme_id'],
            'ile_id' => $protection_sociale['ile_id'],
            'village_id' => $protection_sociale['village_id']
        );
    }

    public function _set_down($protection_sociale,$id) {
        return array(
            'id' => $id,
            'Code' => $protection_sociale['Code'],
            'Nom' => $protection_sociale['Nom'],
            'Contact' => $protection_sociale['Contact'],
            'Representant' => $protection_sociale['Representant'],
            'NumeroTelephone' => $protection_sociale['NumeroTelephone'],
            'programme_id' => $protection_sociale['programme_id'],
            'ile_id' => $protection_sociale['ile_id'],
            'village_id' => $protection_sociale['village_id']
        );
    }

    public function delete($id) {
        $this->db->where('id', (int) $id)->delete($this->table);
        if($this->db->affected_rows() === 1)  {
            return true;
        }else{
            return null;
        }  
    }
    public function findAll() {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('Nom')
                        ->get()
                        ->result();
        if($result) {
            return $result;
        }else{
            return null;
        }                 
    }
    public function findById($id) {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->where("id", $id)
                        ->order_by('id', 'asc')
                        ->get()
                        ->result();
        if($result) {
            return $result;
        }else{
            return null;
        }                 
    }
}
?>