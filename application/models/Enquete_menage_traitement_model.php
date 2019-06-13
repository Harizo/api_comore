<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enquete_menage_traitement_model extends CI_Model
{
    protected $table = 'enquete_menage';


    public function add($enquete_menage_traitement)
    {
        $this->db->set($this->_set($enquete_menage_traitement))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $enquete_menage_traitement)
    {
        $this->db->set($this->_set($enquete_menage_traitement))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($enquete_menage_traitement)
    {
        return array(
            'id_menage'          =>      $enquete_menage_traitement['id_menage'],
            'source_eau'         =>      $enquete_menage_traitement['source_eau'],                       
            'toilette'           =>      $enquete_menage_traitement['toilette'],                       
            'bien_equipement'    =>      $enquete_menage_traitement['bien_equipement'],                       
            'revetement_sol'     =>      $enquete_menage_traitement['revetement_sol'],                       
            'revetement_toit'    =>      $enquete_menage_traitement['revetement_toit'],                       
            'revetement_mur'     =>      $enquete_menage_traitement['revetement_mur'],                       
            'type_culture'       =>      $enquete_menage_traitement['type_culture'],                       
            'type_elevage'       =>      $enquete_menage_traitement['type_elevage']                       
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

    public function findAllByMenage($id_menage)
    {
        
        $this->db->where("id_menage", $id_menage);
        $q = $this->db->get($this->table);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;  
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
