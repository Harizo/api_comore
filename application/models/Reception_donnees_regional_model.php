<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reception_donnees_regional_model extends CI_Model 
{
    protected $table = 'reception_donnees_regional';
    protected $table_menage = 'menage';
    protected $table_individu = 'individu';
    protected $table_enquete_menage = 'enquete_menage';
    protected $table_enquete_individu = 'enquete_individu';
    protected $table_suivi_menage = 'suivi_menage';
    protected $table_suivi_individu = 'suivi_individu';

  	public function update_menage($id)
    {
        $this->db->set($this->_set($id))
                            ->where('id', (int) $id)
                            ->update($this->table_suivi_menage);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($id)
    {
        return array(
            'id_serveur_centrale'       =>      $id                     
        );
    }

    public function SauvegarderTout($data) 
    {
        $tmp = array();
        $reception_donnees_regional = array();
        $reception_donnees_regional = json_decode($data['reception_donnees_regional']);
        
        $tmp ['code_unique']           = $reception_donnees_regional->code_unique;                
        $tmp ['date']                  = $reception_donnees_regional->date;    
        $tmp ['id_region']             = $reception_donnees_regional->region_id;
        $tmp ['id_district']           = $reception_donnees_regional->district_id;
        $tmp ['id_site_embarquement']  = $reception_donnees_regional->site_embarquement_id;
        $tmp ['id_enqueteur']          = $reception_donnees_regional->enqueteur_id;                
        $tmp ['latitude']              = $reception_donnees_regional->latitude;    
        $tmp ['longitude']             = $reception_donnees_regional->longitude;
        $tmp ['altitude']              = $reception_donnees_regional->altitude;
        $tmp ['id_user']               = $data['user_id'];
        $this->db->set($tmp)
                ->set('date_creation', 'NOW()', false)
                ->set('date_modification', 'NOW()', false)
                ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }
        else
        {
            return null;
        }
    }


}
