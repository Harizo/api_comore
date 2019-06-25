<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Region_model extends CI_Model
{
    protected $table = 'see_region';


    public function add($region)
    {
        $this->db->set($this->_set($region))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $region)
    {
        $this->db->set($this->_set($region))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($region)
    {
        return array(
            'Code'         => $region['Code'],
            'Region'       => $region['Region'],
            'ile_id'       => $region['ile_id'],
            'programme_id' => $region['programme_id']                       
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
                        ->order_by('Code')
                        ->get()
                        ->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                 
    }

    public function findAllByIle($ile_id)
    {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->order_by('Region')
                        ->where("ile_id", $ile_id)
                        ->get()
                        ->result();
        if($result)
        {
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
    public function findByIdArray($id)  {
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
    public function findPrefectureByIle($ile_id)  {
        $result =  $this->db->select('*')
                        ->from($this->table)
                        ->where("ile_id", $ile_id)
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
