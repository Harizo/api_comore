<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enquete_individu_traitement_model extends CI_Model
{
    protected $table = 'enquete_individu';


    public function add($enquete_individu_traitement)
    {
        $this->db->set($this->_set($enquete_individu_traitement))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $enquete_individu_traitement)
    {
        $this->db->set($this->_set($enquete_individu_traitement))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($enquete_individu_traitement)
    {
        return array(
            'id_serveur_centrale'       =>      $enquete_individu_traitement['id_serveur_centrale'],
            'id_individu'               =>      $enquete_individu_traitement['id_individu'],
            'id_lien_parente'           =>      $enquete_individu_traitement['id_lien_parente'],                       
            'situation_matrimoniale'    =>      $enquete_individu_traitement['situation_matrimoniale'],                       
            'id_handicap_visuel'        =>      $enquete_individu_traitement['id_handicap_visuel'],                       
            'id_handicap_parole'        =>      $enquete_individu_traitement['id_handicap_parole'],                       
            'id_handicap_auditif'       =>      $enquete_individu_traitement['id_handicap_auditif'],                       
            'id_handicap_mental'        =>      $enquete_individu_traitement['id_handicap_mental'],                       
            'id_handicap_moteur'        =>      $enquete_individu_traitement['id_handicap_moteur'] ,
            'vaccins'                   =>      $enquete_individu_traitement['vaccins'],           
            'poids'                     =>      $enquete_individu_traitement['poids'],           
            'perimetre_bracial'         =>      $enquete_individu_traitement['perimetre_bracial'],           
            'age_mois'                  =>      $enquete_individu_traitement['age_mois'],           
            'taille'                    =>      $enquete_individu_traitement['taille'],           
            'zscore'                    =>      $enquete_individu_traitement['zscore'] ,       
            'mois_grossesse'                    =>      $enquete_individu_traitement['mois_grossesse']        
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

    public function findAllByindividu($id_individu)
    {
        
        $this->db->where("id_individu", $id_individu);
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
