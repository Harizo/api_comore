<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suivi_menage_model extends CI_Model
{
    protected $table = 'suivi_menage';


    public function add($suivi_menage)
    {
        $this->db->set($this->_set($suivi_menage))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $suivi_menage)
    {
        $this->db->set($this->_set($suivi_menage))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($suivi_menage)
    {
        return array(
            'id_menage'         => $suivi_menage['id_menage'],
            'id_partenaire'     => $suivi_menage['id_partenaire'] ,                     
            'id_acteur'         => $suivi_menage['id_acteur'],                      
            'id_programme'      => $suivi_menage['id_programme'],                      
            'id_type_transfert' => $suivi_menage['id_type_transfert'],                      
            'date_suivi'        => $suivi_menage['date_suivi'],                      
            'montant'           => $suivi_menage['montant'],                      
            'observation'       => $suivi_menage['observation'],                      
            'poids'             => $suivi_menage['poids'],                      
            'perimetre_bracial' => $suivi_menage['perimetre_bracial'],                      
            'age_mois'          => $suivi_menage['age_mois'],                      
            'taille'            => $suivi_menage['taille'],                      
            'zscore'            => $suivi_menage['zscore'],                      
            'mois_grossesse'    => $suivi_menage['mois_grossesse'],                      
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

    public function findAllByProgramme($id_programmes)
    {
        $result =  $this->db->select('menage.id as id_menage,
                                        suivi_menage.id as id,
                                        menage.NomInscrire as NomInscrire,
                                        menage.PersonneInscription as PersonneInscription,
                                        menage.AgeInscrire as AgeInscrire,
                                        menage.Addresse as Addresse,
                                        menage.NumeroEnregistrement as NumeroEnregistrement
                                        ')
                        ->from($this->table)
                        ->join('menage', 'menage.id = suivi_menage.id_menage')
                    //    ->order_by('id')
                        ->like('id_programme', $id_programmes)
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
    public function findAllByProgrammeAndMenage($id_programmes,$id_menage)
    {
		$requete="select sm.id,sm.id_menage,m.nomchefmenage,m.PersonneInscription,m.AgeInscrire,m.Addresse,m.NumeroEnregistrement,"
				."sm.id_programme,sm.id_acteur,sm.id_partenaire,sm.date_suivi,sm.montant,sm.id_type_transfert,sm.observation,"
				."sm.poids,sm.taille,sm.perimetre_bracial,sm.zscore,sm.age_mois,sm.mois_grossesse"
				." from suivi_menage as sm"
				." left outer join menage as m on m.id=sm.id_menage"
				." left outer join see_village as v on v.id=m.village_id"
                ." where sm.id_programme=".$id_programmes
				." and sm.id_menage=".$id_menage;	
				$result = $this->db->query($requete)->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                  
    }

}
