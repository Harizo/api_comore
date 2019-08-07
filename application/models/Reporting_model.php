<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting_model extends CI_Model 
{

    public function find_sum($date_debut, $date_fin, $condition) 
    {

        $array = array('date_suivi >=' => $date_debut, 'date_suivi <=' => $date_fin);

        $this->db->select(" suivi_menage.date_suivi as date_suivi,
                            menage.nomchefmenage as chef_menage,
                            menage.NumeroEnregistrement as numero,
                            programme.libelle,
                            source_financement.nom as nom_partenaire,
                            see_village.Village as nom_village,
                            see_commune.Commune as nom_commune,
                            see_region.Region as nom_region,
                            see_ile.Ile as nom_ile,
                            see_agent.Nom as nom_agence_payement,
                            type_transfert.description as type_transfert
                            ",FALSE);
        $this->db->select("sum(montant) as somme_montant",FALSE);

        $result =  $this->db->from('suivi_menage,menage,programme,source_financement,see_agent,see_village,see_commune,see_region,see_ile,type_transfert')
                    
                    ->where('suivi_menage.id_menage = menage.id')
                    ->where('suivi_menage.id_programme = programme.id')
                    ->where('suivi_menage.id_partenaire = source_financement.id')
                    ->where('suivi_menage.id_acteur = see_agent.id')
                    ->where('suivi_menage.id_type_transfert = type_transfert.id')

                    ->where('menage.village_id = see_village.id')
                    ->where('see_commune.id = see_village.commune_id')
                    ->where('see_commune.region_id = see_region.id')
                    ->where('see_ile.id = see_region.ile_id')
                    ->where($array)
                    ->where($condition)
                    
                    ->group_by('id_menage,id_partenaire,id_acteur,date_suivi,id_programme')  
                    ->get()
                    ->result();



            if($result)
            {
                return $result;
            }
            else
            {
                return null;
            }       
   
    }

    public function nbr_menage_par_programme($id_programme,$condition)
    {
        $this->db->select(" count(*) as nbr",FALSE);

        $this->db->select("(select count(*) from menage,see_village,see_commune,see_region,see_ile 
                            where menage.village_id = see_village.id
                            and see_commune.id = see_village.commune_id
                            and see_commune.region_id = see_region.id
                            and see_ile.id = see_region.ile_id and ".$condition." ) as nbr_menage_enregistrer",FALSE);


        $q =  $this->db->from('menage_programme,menage,see_village,see_commune,see_region,see_ile')

                    ->where('menage.id = menage_programme.id_menage')
                    ->where('menage.village_id = see_village.id')
                    ->where('see_commune.id = see_village.commune_id')
                    ->where('see_commune.region_id = see_region.id')
                    ->where('see_ile.id = see_region.ile_id')
                    ->like('id_programme', $id_programme)
                    ->where($condition)
                   
                    ->get();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function nbr_individu_par_programme($id_programme,$condition)
    {
        $this->db->select(" count(*) as nbr",FALSE);

        $this->db->select("(select count(individu.id) from menage,individu,see_village,see_commune,see_region,see_ile 
                            where menage.village_id = see_village.id
                            and see_commune.id = see_village.commune_id
                            and menage.id = individu.menage_id
                            and see_commune.region_id = see_region.id
                            and see_ile.id = see_region.ile_id and ".$condition." ) as nbr_individu_enregistrer",FALSE);


        $q =  $this->db->from('individu_programme,menage,individu,see_village,see_commune,see_region,see_ile')

                    ->where('individu.id = individu_programme.id_individu')
                    ->where('menage.id = individu.menage_id')
                    ->where('menage.village_id = see_village.id')
                    ->where('see_commune.id = see_village.commune_id')
                    ->where('see_commune.region_id = see_region.id')
                    ->where('see_ile.id = see_region.ile_id')
                    ->like('id_programme', $id_programme)
                    ->where($condition)
                   
                    ->get();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }


    public function menage_par_programme($condition)
    {
        $this->db->select(" menage.id as id_menage,
                            id_programme as tab_id_programme,
                            NumeroEnregistrement,
                            DateInscription,
                            PersonneInscription,
                            Addresse,
                            nomchefmenage,
                            SexeChefMenage,
                            agechefdemenage
                          ",FALSE);




        $result =  $this->db->from('menage_programme,menage,see_village,see_commune,see_region,see_ile')

                    ->where('menage.id = menage_programme.id_menage')
                    ->where('menage.village_id = see_village.id')
                    ->where('see_commune.id = see_village.commune_id')
                    ->where('see_commune.region_id = see_region.id')
                    ->where('see_ile.id = see_region.ile_id')
                    ->where($condition)
                   
                    ->get()
                    ->result();



            if($result)
            {
                return $result;
            }
            else
            {
                return null;
            }  
    }


    public function individu_par_programme($condition)
    {
        $this->db->select(" individu.id as id_individu,
                            id_programme as tab_id_programme,
                            Nom,
                            DateNaissance,
                            Activite,
                            travailleur,
                            sexe,
                            menage.NumeroEnregistrement as NumeroEnregistrement
                          ",FALSE);




        $result =  $this->db->from('individu_programme,individu,menage,see_village,see_commune,see_region,see_ile')

                    ->where('individu.id = individu_programme.id_individu')
                    ->where('menage.id = individu.menage_id')
                    ->where('menage.village_id = see_village.id')
                    ->where('see_commune.id = see_village.commune_id')
                    ->where('see_commune.region_id = see_region.id')
                    ->where('see_ile.id = see_region.ile_id')
                    ->where($condition)
                   
                    ->get()
                    ->result();



            if($result)
            {
                return $result;
            }
            else
            {
                return null;
            }  
    }

    public function nbr_individu_handicape($condition)
    {
        $this->db->select(" count(*) as nbr_handicape",FALSE);

        $q =  $this->db->from('enquete_individu')

                    ->join('individu', 'individu.id = enquete_individu.id_individu')
                    ->join('menage', 'menage.id = individu.menage_id')
                    ->join('see_village', 'menage.village_id = see_village.id')
                    ->join('see_commune', 'see_commune.id = see_village.commune_id')
                    ->join('see_region', 'see_commune.region_id = see_region.id')
                    ->join('see_ile', 'see_ile.id = see_region.ile_id')
        
                    ->where("enquete_individu.id_handicap_visuel > '0' OR enquete_individu.id_handicap_parole > '0' OR enquete_individu.id_handicap_auditif > '0' OR enquete_individu.id_handicap_mental > '0' OR enquete_individu.id_handicap_moteur > '0' ")
                    
                    
                    ->where($condition)
                   
                    ->get();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function nbr_individu_handicape_par_type($id_type, $condition)
    {
        $this->db->select(" count(*) as nbr_handicape",FALSE);

        $q =  $this->db->from('enquete_individu')

                    ->join('individu', 'individu.id = enquete_individu.id_individu')
                    ->join('menage', 'menage.id = individu.menage_id')
                    ->join('see_village', 'menage.village_id = see_village.id')
                    ->join('see_commune', 'see_commune.id = see_village.commune_id')
                    ->join('see_region', 'see_commune.region_id = see_region.id')
                    ->join('see_ile', 'see_ile.id = see_region.ile_id')
        
                    ->where($id_type." > '0' ")
                   
                    
                    ->where($condition)
                   
                    ->get();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function nbr_enfant_mal_nouri($zscore_a, $zscore_b, $condition)
    {
        $array = array('zscore >=' => $zscore_a, 'zscore <=' => $zscore_b);
        $this->db->select(" count(*) as nbr_enfant",FALSE);

        $q =  $this->db->from('enquete_individu')

                    ->join('individu', 'individu.id = enquete_individu.id_individu')
                    ->join('menage', 'menage.id = individu.menage_id')
                    ->join('see_village', 'menage.village_id = see_village.id')
                    ->join('see_commune', 'see_commune.id = see_village.commune_id')
                    ->join('see_region', 'see_commune.region_id = see_region.id')
                    ->join('see_ile', 'see_ile.id = see_region.ile_id')
        
                
                    ->where('taille != 0')
                    ->where('poids != 0')
                    ->where($array)
                   
                    
                    ->where($condition)
                   
                    ->get();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function nbr_mariage_precoce($condition)
    {
        //$array = array('date_suivi >=' => $date_debut, 'date_suivi <=' => $date_fin);
        $this->db->select(" count(*) as nbr,
                            type_mariage.description as type_mariage",FALSE);
                           //DATE_FORMAT(suivi_individu.date_suivi,'%Y') as annee",FALSE);

        $this->db->select("(select count(*) from suivi_individu,menage,individu,see_village,see_commune,see_region,see_ile 
                            where menage.village_id = see_village.id
                            and see_commune.id = see_village.commune_id
                            and individu.id = suivi_individu.id_individu
                            and suivi_individu.id_type_mariage > 0 
                            and menage.id = individu.menage_id
                            and see_commune.region_id = see_region.id
                            and see_ile.id = see_region.ile_id and ".$condition." ) as nbr_total_mariage_precoce",FALSE);

        $q =  $this->db->from('suivi_individu')

                    ->join('individu', 'individu.id = suivi_individu.id_individu')
                    ->join('menage', 'menage.id = individu.menage_id')
                    ->join('see_village', 'menage.village_id = see_village.id')
                    ->join('see_commune', 'see_commune.id = see_village.commune_id')
                    ->join('see_region', 'see_commune.region_id = see_region.id')
                    ->join('see_ile', 'see_ile.id = see_region.ile_id')
                    ->join('type_mariage', 'type_mariage.id = suivi_individu.id_type_mariage')
        
                    ->where("suivi_individu.id_type_mariage > 0 ")
                    ->group_by("type_mariage.id")
                    
                    
                    ->where($condition)
                   
                    ->get()
                    ->result();



            if($q)
            {
                return $q;
            }
            else
            {
                return null;
            }  
    }

    public function nbr_violence($condition)
    {
        //$array = array('date_suivi >=' => $date_debut, 'date_suivi <=' => $date_fin);
        $this->db->select(" count(*) as nbr,
                            type_violence.description as type_violence",FALSE);
                           //DATE_FORMAT(suivi_individu.date_suivi,'%Y') as annee",FALSE);

        $q =  $this->db->from('suivi_individu')

                    ->join('individu', 'individu.id = suivi_individu.id_individu')
                    ->join('menage', 'menage.id = individu.menage_id')
                    ->join('see_village', 'menage.village_id = see_village.id')
                    ->join('see_commune', 'see_commune.id = see_village.commune_id')
                    ->join('see_region', 'see_commune.region_id = see_region.id')
                    ->join('see_ile', 'see_ile.id = see_region.ile_id')
                    ->join('type_violence', 'type_violence.id = suivi_individu.id_type_violence')
        
                    ->where("suivi_individu.id_type_violence > 0 ")
                    ->group_by("type_violence.id")
                    
                    
                    ->where($condition)
                   
                    ->get()
                    ->result();



            if($q)
            {
                return $q;
            }
            else
            {
                return null;
            }  
    }

    public function nbr_individu_par_formation($id_formation_recue, $condition)
    {
        $this->db->select(" count(*) as nbr",FALSE);

        $q =  $this->db->from('enquete_individu')

                    ->join('individu', 'individu.id = enquete_individu.id_individu')
                    ->join('menage', 'menage.id = individu.menage_id')
                    ->join('see_village', 'menage.village_id = see_village.id')
                    ->join('see_commune', 'see_commune.id = see_village.commune_id')
                    ->join('see_region', 'see_commune.region_id = see_region.id')
                    ->join('see_ile', 'see_ile.id = see_region.ile_id')
                    ->like('formation_recue', $id_formation_recue)
                    ->where($condition)
                   
                    ->get();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }
    
}
?>