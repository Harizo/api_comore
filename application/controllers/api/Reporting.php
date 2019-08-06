<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Reporting extends REST_Controller 
{

    public function __construct() {
        parent::__construct();
        $this->load->model('reporting_model', 'ReportingManager');
        $this->load->model('programme_model', 'ProgrammeManager');
        $this->load->model('enquete_menage_model', 'EnquetemenageManager');
    }

    public function index_get() 
    {
        set_time_limit(0);
        ini_set ('memory_limit', '1024M');
        $type_etat = $this->get('type_etat');
        $id_programme = $this->get('id_programme');
        $id_ile = $this->get('id_ile');
        $id_region = $this->get('id_region');
        $id_commune = $this->get('id_commune');
    	$id_village = $this->get('village_id');
        $date_deb = $this->get('date_deb');
        $date_fin = $this->get('date_fin');
        $data = array() ;

    	if ($type_etat == 'transfert_monetaire_menage') 
    	{
    		
    		$data = $this->ReportingManager->find_sum($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
    	}

        if ($type_etat == 'nbr_menage_par_programme') 
        {
            $all_programme = $this->ProgrammeManager->findAll();
            $total = 0 ;
            foreach ($all_programme as $key => $value) 
            {
                $id_prog = '"'.$value->id.'"' ;
                $data[$key]['id'] = $value->id ;
                $data[$key]['libelle'] = $value->libelle ;
                $nbr = $this->ReportingManager->nbr_menage_par_programme($id_prog,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
                $data[$key]['nbr'] = $nbr->nbr;
                $total = $total + $nbr->nbr;
                $data[$key]['nbr_menage_enregistrer'] = $nbr->nbr_menage_enregistrer;
            }

            $data['total'] = $total ;
        }

        if ($type_etat == 'nbr_individu_par_programme') 
        {
            $all_programme = $this->ProgrammeManager->findAll();
            $total = 0 ;
            foreach ($all_programme as $key => $value) 
            {
                $id_prog = '"'.$value->id.'"' ;
                $data[$key]['id'] = $value->id ;
                $data[$key]['libelle'] = $value->libelle ;
                $nbr = $this->ReportingManager->nbr_individu_par_programme($id_prog,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
                $data[$key]['nbr'] = $nbr->nbr;
                $total = $total + $nbr->nbr;
                $data[$key]['nbr_individu_enregistrer'] = $nbr->nbr_individu_enregistrer;
            }

            $data['total'] = $total ;
        }

        if ($type_etat == 'menage_par_programme') 
        {

            $menage_programme = $this->ReportingManager->menage_par_programme($this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));

            if ($menage_programme) 
            {
                foreach ($menage_programme as $key => $value) 
                {
                    $data[$key]['id_menage'] = $value->id_menage ;
                    $tab_id_programme = unserialize($value->tab_id_programme) ;
                    $tab_programme = array() ;
                    foreach ($tab_id_programme as $k => $val) 
                    {
                        $programme  = $this->ProgrammeManager->findById_obj($val);
                        $tab_programme[$k]  = $programme->libelle;
                    }
                    //$data[$key]['tab_id_programme'] = $tab_id_programme ;
                    $data[$key]['tab_programme'] = $tab_programme ;
                    $data[$key]['NumeroEnregistrement'] = $value->NumeroEnregistrement ;
                    $data[$key]['DateInscription'] = $value->DateInscription ;
                    $data[$key]['PersonneInscription'] = $value->PersonneInscription ;
                    $data[$key]['Addresse'] = $value->Addresse ;
                    $data[$key]['nomchefmenage'] = $value->nomchefmenage ;
                    $data[$key]['SexeChefMenage'] = $value->SexeChefMenage ;
                    $data[$key]['agechefdemenage'] = $value->agechefdemenage ;
                }
            }
        }

        if ($type_etat == 'nbr_pers_avec_andicap') 
        {
            $tab_handicap = ["id_handicap_visuel"=>"Handicap visuel","id_handicap_parole"=>"Handicap de la parole","id_handicap_auditif"=>"Handicap auditif","id_handicap_mental"=>"Handicap mental","id_handicap_moteur"=>"Handicap moteur"];
            $indice = 0 ;
            $nbr_total = 0 ;
            foreach ($tab_handicap as $key => $value) {
                $data[$indice]['libelle'] = $value ;
                $nbr = $this->ReportingManager->nbr_individu_handicape_par_type($key, $this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
                $data[$indice]['nbr'] = $nbr->nbr_handicape;
                $nbr_total = $nbr_total + $nbr->nbr_handicape;


                $indice++;
            }

            $data['total'] = $nbr_total ;
          
        }

        if ($type_etat == 'individu_par_programme') 
        {

            $individu_programme = $this->ReportingManager->individu_par_programme($this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));

            if ($individu_programme) 
            {
                foreach ($individu_programme as $key => $value) 
                {
                    $data[$key]['id_individu'] = $value->id_individu ;
                    $tab_id_programme = unserialize($value->tab_id_programme) ;
                    $tab_programme = array() ;
                    foreach ($tab_id_programme as $k => $val) 
                    {
                        $programme  = $this->ProgrammeManager->findById_obj($val);
                        $tab_programme[$k]  = $programme->libelle;
                    }
                    //$data[$key]['tab_id_programme'] = $tab_id_programme ;
                    $data[$key]['tab_programme'] = $tab_programme ;
                    $data[$key]['Nom'] = $value->Nom ;
                    $data[$key]['DateNaissance'] = $value->DateNaissance ;
                    $data[$key]['Activite'] = $value->Activite ;
                    $data[$key]['travailleur'] = $value->travailleur ;
                    $data[$key]['sexe'] = $value->sexe ;
                    $data[$key]['NumeroEnregistrement'] = $value->NumeroEnregistrement ;
                }
            }
        }

        if ($type_etat == 'nbr_enfant_mal_nouri') 
        {
            $tres_severe= $this->ReportingManager->nbr_enfant_mal_nouri("-4","-4", $this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            $severe_mas= $this->ReportingManager->nbr_enfant_mal_nouri("-3","-3", $this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            $moderee_mam= $this->ReportingManager->nbr_enfant_mal_nouri("-2","-2", $this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            $sortie_pecma= $this->ReportingManager->nbr_enfant_mal_nouri("-1.5","-1.5", $this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            $poids_median= $this->ReportingManager->nbr_enfant_mal_nouri("-1","0", $this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            $data[0]['tres_severe'] = $tres_severe->nbr_enfant;
            $data[0]['severe_mas'] = $severe_mas->nbr_enfant;
            $data[0]['moderee_mam'] = $moderee_mam->nbr_enfant;
            $data[0]['sortie_pecma'] = $sortie_pecma->nbr_enfant;
            $data[0]['poids_median'] = $poids_median->nbr_enfant;
        }

        if ($type_etat == "nbr_mariage_precoce") 
        {
            $data = $this->ReportingManager->nbr_mariage_precoce($this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
        }

        if ($type_etat == "nbr_violence") 
        {
            $data = $this->ReportingManager->nbr_violence($this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
        }

        if ($type_etat == "nbr_individu_par_formation") 
        {
            $type_formation_recues = $this->EnquetemenageManager->findAll("type_formation_recue");

            if ($type_formation_recues) 
            {
                $total = 0 ;
                foreach ($type_formation_recues as $key => $value) 
                {
                    $id_formation = '"'.$value->id.'"' ;
                    $data[$key]['id'] = $value->id ;
                    $data[$key]['description'] = $value->description ;
                    $nbr = $this->ReportingManager->nbr_individu_par_formation($id_formation,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));

                    $data[$key]['nbr'] = $nbr->nbr;
                    $total = $total + $nbr->nbr;
                }

                $data['total'] = $total ;
            }
        }

    	if (count($data)>0) {
            $this->response([
                'status' => TRUE,
                'response' => $data,
                'message' => 'Get data success',
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'response' => array(),
                'message' => 'No data were found'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village)
    {
       
        if (($id_village != 'null')&&($id_village != '*')) 
        {
            $requete = "see_village.id='".$id_village."'" ;
            return $requete ;
        }
        else
        {
            if (($id_commune!='null')&&($id_commune != '*')) 
            {
                $requete = "see_commune.id='".$id_commune."'" ;
                return $requete ;
            }
            else
            {
                if (($id_region!='null')&&($id_region != '*')) 
                {
                    $requete = "see_region.id='".$id_region."'" ;
                    return $requete ;
                }
                else
                {
                    if (($id_ile!='null')&&($id_ile != '*')) 
                    {
                        $requete = "see_ile.id=".$id_ile ;
                        return $requete ;
                    }
                    else
                    {
                        return "see_ile.id > 0";
                    }
                }
            }
            
        }
        
    }

}
?>