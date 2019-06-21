<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting_model extends CI_Model 
{

    public function find_sum($date_debut, $date_fin) {

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
    
}
?>