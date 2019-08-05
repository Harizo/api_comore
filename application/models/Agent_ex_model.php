<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agent_ex_model extends CI_Model {
    protected $table = 'see_agex';

    public function add($agent_ex)  {
        $this->db->set($this->_set($agent_ex))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)  {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }

    public function add_down($agent_ex, $id)  {
        $this->db->set($this->_set_down($agent_ex, $id))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)  {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }
    public function update($id, $agent_ex)  {
        $this->db->set($this->_set($agent_ex))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)  {
            return true;
        }else{
            return null;
        }                      
    }
    public function _set($agent_ex) {
        return array(
            'Code' => $agent_ex['Code'],
            'Nom' => $agent_ex['Nom'],
            'Contact' => $agent_ex['Contact'],
            'Representant' => $agent_ex['Representant'],
            'programme_id' => $agent_ex['programme_id'],
            'ile_id' => $agent_ex['ile_id']
        );
    }

    public function _set_down($agent_ex, $id) {
        return array(
            'id' => $id,
            'Code' => $agent_ex['Code'],
            'Nom' => $agent_ex['Nom'],
            'Contact' => $agent_ex['Contact'],
            'Representant' => $agent_ex['Representant'],
            'programme_id' => $agent_ex['programme_id'],
            'ile_id' => $agent_ex['ile_id']
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
                        ->order_by('Code')
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
}
?>