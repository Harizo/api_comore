<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menage_model extends CI_Model
{
    protected $table = 'menage';


    public function add($menage)
    {
        $this->db->set($this->_set($menage))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $menage)
    {
        $this->db->set($this->_set($menage))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($menage)
    {
        return array(
            'id_serveur_centrale'       =>      $menage['id_serveur_centrale'],
            'DateInscription'       =>      $menage['DateInscription'],
            'village_id'            =>      $menage['village_id'],                       
            'NumeroEnregistrement'  =>      $menage['NumeroEnregistrement'],                       
            'nomchefmenage'           =>      $menage['nomchefmenage'],                       
            'PersonneInscription'   =>      $menage['PersonneInscription'],                       
            'agechefdemenage'           =>      $menage['agechefdemenage'],                       
            'SexeChefMenage'        =>      $menage['SexeChefMenage'],                       
            'Addresse'               =>      $menage['Addresse']                      
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

    public function findAllByVillage($village_id)
    {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('id')
                        ->where("village_id", $village_id)
                        ->get()
                        ->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                 
    }

    public function find_max_id()
    {
        $q =  $this->db->select_max('id')
                        ->from($this->table)
                        ->get();
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
