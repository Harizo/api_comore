<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bien_equipement_model extends CI_Model
{
    protected $table = 'bien_equipement';


    public function add($bien_equipement)
    {
        $this->db->set($this->_set($bien_equipement))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $bien_equipement)
    {
        $this->db->set($this->_set($bien_equipement))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($bien_equipement)
    {
        return array(
            'code'       =>      $bien_equipement['code'],
            'libelle'    =>      $bien_equipement['libelle']                       
        );
    }


    public function delete($id)
    {
        $this->db->where('id', (int) $id)->delete($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }  
    }

    public function findAll()
    {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('description')
                        ->get()
                        ->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                 
    }

    public function findById($id)
    {
        $this->db->where("id", $id);
        $q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

}
