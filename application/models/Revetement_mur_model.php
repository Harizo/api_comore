<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Revetement_mur_model extends CI_Model
{
    protected $table = 'revetement_mur';


    public function add($revetement_mur)
    {
        $this->db->set($this->_set($revetement_mur))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $revetement_mur)
    {
        $this->db->set($this->_set($revetement_mur))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($revetement_mur)
    {
        return array(
            'code'       =>      $revetement_mur['code'],
            'libelle'    =>      $revetement_mur['libelle']                       
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
