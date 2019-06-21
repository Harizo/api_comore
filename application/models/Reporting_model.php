<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting_model extends CI_Model 
{
    protected $table_menage = 'menage';
    protected $table_suivi_menage = 'suivi_menage';

 

    public function find_sum() {

        /*$this->db->select("sum(suivi_menage.montant) as somme_montant",FALSE);

        $result =  $this->db->from('suivi_menage,menage,programme,source_financement,acteur')
                    
                    ->where('suivi_menage.id_menage = menage.id')
                    ->where('suivi_menage.id_programme = programme.id')
                    ->where('suivi_menage.id_partenaire = source_financement.id')
                    ->where('suivi_menage.id_acteur = acteur.id')
                    
                    ->group_by('id_menage,id_partenaire,id_acteur')  
                    ->get()
                    ->result();



            if($result)
            {
                return $result;
            }
            else
            {
                return null;
            }       */
            $result =  $this->db->select('*')
                        ->from($this->table_suivi_menage)
                      
                        ->get()
                        ->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }          
    }
    
}
?>