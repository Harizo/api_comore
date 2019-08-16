<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suivi_individu_model extends CI_Model
{
    protected $table = 'suivi_individu';


    public function add($suivi_individu)
    {
        $this->db->set($this->_set($suivi_individu))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $suivi_individu)
    {
        $this->db->set($this->_set($suivi_individu))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($suivi_individu)
    {
       return array(
            'id_serveur_centrale'       => $suivi_individu['id_serveur_centrale'],
            'id_individu'       => $suivi_individu['id_individu'],
            'id_partenaire'     => $suivi_individu['id_partenaire'] ,                     
            'id_acteur'         => $suivi_individu['id_acteur'],                      
            'id_programme'      => $suivi_individu['id_programme'],                      
            'id_type_transfert' => $suivi_individu['id_type_transfert'],                      
            'date_suivi'        => $suivi_individu['date_suivi'],                      
            'montant'           => $suivi_individu['montant'],                      
            'observation'       => $suivi_individu['observation'],                      
            'poids'             => $suivi_individu['poids'],                      
            'perimetre_bracial' => $suivi_individu['perimetre_bracial'],                      
            'age_mois'          => $suivi_individu['age_mois'],                      
            'taille'            => $suivi_individu['taille'],                      
            'zscore'            => $suivi_individu['zscore'],                      
            'mois_grossesse'    => $suivi_individu['mois_grossesse'],                      
            'cause_mariage'     => $suivi_individu['cause_mariage'],                      
            'age'               => $suivi_individu['age'],                      
            'infraction'        => $suivi_individu['infraction'],                      
            'lieu_infraction'   => $suivi_individu['lieu_infraction'],                      
            'id_situation_matrimoniale' => $suivi_individu['id_situation_matrimoniale'],                      
            'id_type_mariage'   => $suivi_individu['id_type_mariage'],                      
            'id_type_violence'  => $suivi_individu['id_type_violence'],                      
            'type_formation_recue' => $suivi_individu['type_formation_recue'],
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
        $result =  $this->db->select('menage.id as id_individu,
                                        suivi_individu.id as id,
                                        menage.NomInscrire as NomInscrire,
                                        menage.PersonneInscription as PersonneInscription,
                                        menage.AgeInscrire as AgeInscrire,
                                        menage.Addresse as Addresse,
                                        menage.NumeroEnregistrement as NumeroEnregistrement
                                        ')
                        ->from($this->table)
                        ->join('menage', 'menage.id = suivi_individu.id_individu')
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

    public function findAllByMenage($id_individu)
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
    public function findAllByProgrammeAndIndividu($id_programmes,$id_individu)
    {
		$requete="select sm.id,sm.id_serveur_centrale,sm.id_individu,i.Nom,i.DateNaissance,i.sexe,"
				."sm.id_programme,sm.id_acteur,sm.id_partenaire,sm.date_suivi,sm.montant,sm.id_type_transfert,sm.observation,"
				."sm.poids,sm.taille,sm.perimetre_bracial,sm.zscore,sm.age_mois,sm.mois_grossesse,sm.age,sm.infraction,"
				."sm.cause_mariage,sm.lieu_infraction,sm.id_situation_matrimoniale,sm.id_type_mariage,sm.id_type_violence,sm.type_formation_recue"
				." from suivi_individu as sm"
				." left outer join individu as i on i.id=sm.id_individu"
				." left outer join menage as m on m.id=i.menage_id"
				." left outer join see_village as v on v.id=m.village_id"
                ." where sm.id_programme=".$id_programmes
				." and sm.id_individu=".$id_individu;	
				$result = $this->db->query($requete)->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                  
    }

}
