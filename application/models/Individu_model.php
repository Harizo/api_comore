<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Individu_model extends CI_Model
{
    protected $table = 'individu';


    public function add($individu)
    {
        $this->db->set($this->_set($individu))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $individu)
    {
        $this->db->set($this->_set($individu))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($individu)
    {
        return array(
            'id_serveur_centrale'         =>      $individu['id_serveur_centrale'],
            'menage_id'         =>      $individu['menage_id'],
            'Nom'               =>      $individu['Nom'],                      
            'DateNaissance'     =>      $individu['DateNaissance'],                      
            'Activite'          =>      $individu['Activite'],                      
            'travailleur'       =>      $individu['travailleur'],                      
            'sexe'              =>      $individu['sexe']                      
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

    public function findAllByMenage($menage_id)
    {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('id')
                        ->where("menage_id", $menage_id)
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
