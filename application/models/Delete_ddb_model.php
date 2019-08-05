<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delete_ddb_model extends CI_Model {
    

    public function delete($table) {
        $resp = $this->db->empty_table($table); 
        if($resp)  
        {
            return true;
        }
        else
        {
            return null;
        }  
    }

    public function add($data, $table)  {
        $this->db->set($this->_set($data, $table))
                            ->insert($table);
        if($this->db->affected_rows() === 1)  {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }

     public function _set($type_acteur, $table) {

        if ($table == 'source_financement') 
        {
            return array(
                'id' => $type_acteur['id'],
                'nom' => $type_acteur['nom']
            );
        }
        else
        {
            return array(
                'id' => $type_acteur['id'],
                'description' => $type_acteur['description']
            );
        }
        
    }

}
?>