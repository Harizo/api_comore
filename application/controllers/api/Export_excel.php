<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Export_excel extends REST_Controller 
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
        $selection = $this->get('selection');
        $id_programme = $this->get('id_programme');
        $id_ile = $this->get('id_ile');
        $id_region = $this->get('id_region');
        $id_commune = $this->get('id_commune');
    	$id_village = $this->get('village_id');
        $date_deb = $this->get('date_deb');
        $date_fin = $this->get('date_fin');
        $repertoire = $this->get('repertoire');
        $data = array() ;
        //$calcul = array();
    	if ($selection == 'transfert_monetaire_menage') 
    	{
    		
    		$donnee = $this->ReportingManager->find_sum($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            if ($donnee!=null) {
                $data = $donnee;
            }
    		
    	}
    	

        if ($selection == 'nbr_menage_par_programme') 
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
                if ($nbr->nbr_menage_enregistrer==0) {
                    $data[$key]['stat'] = 0;
                }else{
                   //$data[$key]['stat'] = (($data[$key]['nbr']*100)/$data[$key]['nbr_menage_enregistrer']);
                   $data[$key]['stat'] = number_format((($nbr->nbr*100)/$nbr->nbr_menage_enregistrer),2);

                }
                
            }

            $data['total'] = $total ;
            //$this->export($data,$repertoire);
        }

        if ($selection == 'nbr_individu_par_programme') 
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

        if ($selection == 'menage_par_programme') 
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

        if ($selection == 'nbr_pers_avec_andicap') 
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

        if ($selection == 'individu_par_programme') 
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

        if ($selection == 'nbr_enfant_mal_nouri') 
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

        if ($selection == "nbr_mariage_precoce") 
        {
           $data = $this->ReportingManager->nbr_mariage_precoce($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
        }

        if ($selection == "nbr_violence") 
        {
            $data = $this->ReportingManager->nbr_violence($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
        }

        if ($selection == "nbr_individu_par_formation") 
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
                    $data[$key]['nbr_femme'] = $nbr->nbr_femme;
                    $data[$key]['nbr_homme'] = $nbr->nbr_homme;
                    $total = $total + $nbr->nbr;
                }

               
            }
        }

        if ($selection == 'transfert_monetaire_individu') 
        {
            
            $data = $this->ReportingManager->find_sum_individu($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
        }
        $export=$this->export($data,$repertoire,$selection);
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
    
    public function export($data,$repertoire,$selection){
    	require_once 'Classes/PHPExcel.php';
        require_once 'Classes/PHPExcel/IOFactory.php';

        $nom_file='menage';
        $directoryName = dirname(__FILE__) ."/../../../../assets/excel/".$repertoire;
        
        //Check if the directory already exists.
        if(!is_dir($directoryName))
        {
            mkdir($directoryName, 0777,true);
        }
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Myexcel")
                    ->setLastModifiedBy("Me")
                    ->setTitle("Menage")
                    ->setSubject("Menage")
                    ->setDescription("Menage")
                    ->setKeywords("Menage")
                    ->setCategory("Menage");

        $ligne=1;            
        // Set Orientation, size and scaling
        // Set Orientation, size and scaling
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
        $objPHPExcel->getActiveSheet()->getPageMargins()->SetLeft(0.64); //***pour marge gauche
        $objPHPExcel->getActiveSheet()->getPageMargins()->SetRight(0.64); //***pour marge droite

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        
        $objPHPExcel->getActiveSheet()->setTitle("menage");

        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&R&11&B Page &P / &N');
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setEvenFooter('&R&11&B Page &P / &N');

        $styleTitre = array
        (
        'alignment' => array
            (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                
            ),
        'font' => array
            (
                //'name'  => 'Times New Roman',
                'bold'  => true,
                'size'  => 14
            ),
        );
        $stylesousTitre = array
        ('borders' => array
            (
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        'alignment' => array
            (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                
            ),
        'font' => array
            (
                //'name'  => 'Times New Roman',
                'bold'  => true,
                'size'  => 12
            ),
        );
        $stylecontenu = array
        (
            'borders' => array
            (
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
        'alignment' => array
            (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        //transfert monétaire
        if ($selection =='transfert_monetaire_menage') {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":G".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'MENAGE');
            
            $ligne++;

            //$objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel-> getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->getAlignment()->setWrapText(true);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'N° d\'enregistrement');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Chef ménage');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Date');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Partenaire');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, 'Agence de paiement');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, 'Type de transfert');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, 'Montant');
            $ligne++;
            foreach ($data as $key => $value)
            {
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value->numero);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value->chef_menage);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value->date_suivi);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value->nom_partenaire);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, $value->nom_agence_payement);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, $value->type_transfert);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, $value->somme_montant);

                $ligne++;
            }
        }
        //nbr individu par programme
        if ($selection =='nbr_menage_par_programme') {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":D".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'MENAGE');
            
            $ligne++;

            //$objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel-> getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getAlignment()->setWrapText(true);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Programme');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Nombre menage beneficiaire');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Nombre menage enregistre');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Statistique');
            $ligne++;
            foreach ($data as $key => $value)
            {
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['libelle']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['nbr']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['nbr_menage_enregistrer']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value['stat']);
                $ligne++;
            }
        }

        //menage par programme
        
        if ($selection=='menage_par_programme') {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":H".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'MENAGE');
            
            $ligne++;

            //$objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel-> getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->getAlignment()->setWrapText(true);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Numéros d\'enregistrement');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Nom du chef ménage');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Sexe chef ménage');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Age chef ménage');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, 'Adresse');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, 'Date d\'inscription');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, 'Programme');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$ligne, 'Enqueteur');
            $ligne++;
            foreach ($data as $key => $value)
            {
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['NumeroEnregistrement']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['nomchefmenage']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['SexeChefMenage']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value['agechefdemenage']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, $value['Addresse']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, $value['DateInscription']);
               
                foreach ($value['tab_programme'] as $pro => $progr){
                     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, $progr);
                    
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$ligne, $value['PersonneInscription']);
                $ligne++;
            }
        }
        //fin menage par programme
        try
        {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save(dirname(__FILE__) . "/../../../../assets/excel/menage/".$nom_file.".xlsx");
            
            $this->response([
                'status' => TRUE,
                'nom_file' => $nom_file.".xlsx",
                'message' => 'Get file success',
            ], REST_Controller::HTTP_OK);
          
        } 
        catch (PHPExcel_Writer_Exception $e)
        {
            $this->response([
                  'status' => FALSE,
                   'nom_file' => array(),
                   'message' => "Something went wrong: ". $e->getMessage(),
                ], REST_Controller::HTTP_OK);
        }
    } 
    
}
?>