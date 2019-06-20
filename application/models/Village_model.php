<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Village_model extends CI_Model
{
    protected $table = 'see_village';


    public function add($village)
    {
        $this->db->set($this->_set($village))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $village)
    {
        $this->db->set($this->_set($village))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($village)
    {
        return array(
            'Code'          =>  $village['Code'],
            'Village'       =>  $village['Village'],
            'commune_id'    =>  $village['commune_id'],
            'programme_id'  =>  $village['programme_id']                       
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

    public function findAllByCommune($commune_id)
    {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('Village')
                        ->where("commune_id", $commune_id)
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
