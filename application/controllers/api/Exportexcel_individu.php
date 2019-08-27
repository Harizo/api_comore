<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Exportexcel_individu extends REST_Controller 
{

    public function __construct() {
        parent::__construct();
        $this->load->model('reporting_model', 'ReportingManager');
        $this->load->model('programme_model', 'ProgrammeManager');
        $this->load->model('enquete_menage_model', 'EnquetemenageManager');
        $this->load->model('ile_model', 'ileManager');
        $this->load->model('region_model', 'RegionManager');
        $this->load->model('commune_model', 'CommuneManager');
        $this->load->model('village_model', 'VillageManager');

    }

    public function index_get() 
    {  
        set_time_limit(0);
        ini_set ('memory_limit', '1024M');
        $menu = $this->get('menu');
        $type_etat = $this->get('type_etat');
        $id_programme = $this->get('id_programme');
        $id_ile = $this->get('id_ile');
        $id_region = $this->get('id_region');
        $id_commune = $this->get('id_commune');
        $id_village = $this->get('village_id');
        $date_deb = $this->get('date_deb');
        $date_fin = $this->get('date_fin');
        $repertoire = $this->get('repertoire');
        $data = array();
        $data_filtre = array();
        if (($id_ile!='*')&&($id_ile!=null))
        {
           $tmp = $this->ileManager->findById($id_ile);
           if ($tmp!=null)
           {
              $data_filtre['ile'] =$tmp->Ile;
           }
        }
        if (($id_region!='*')&&($id_region!=null))
        {
           $tmp = $this->RegionManager->findById($id_region);
           if ($tmp!=null)
           {
              $data_filtre['region'] =$tmp->Region;
           }
        }
        if (($id_commune!='*')&&($id_commune!=null))
        {
           $tmp = $this->CommuneManager->findById($id_commune);
           if ($tmp!=null)
           {
              foreach ($tmp as $key => $value)
              {
                $data_filtre['commune'] =$value->Commune;
              }
              
           }
        }
        if (($id_village!='*')&&($id_village!=null))
        {
           $tmp = $this->VillageManager->findById($id_village);
           if ($tmp!=null)
           {
              $data_filtre['village'] =$tmp->Village;
           }
        }
        

        if($menu=="exportexcel_individu")
        {
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

               //$data['total'] = $nbr_total ;

              
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
                $mariage = $this->ReportingManager->nbr_mariage_precoce($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
                if($mariage!=null)
                {
                    $data=$mariage;

                    $data_filtre['date_fin'] =$date_fin;
                    $data_filtre['date_deb'] =$date_deb;
                    
                }
            }

            if ($type_etat == "nbr_violence") 
            {
                $violence = $this->ReportingManager->nbr_violence($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
                if($violence!=null)
                {
                    $data=$violence;
                    $data_filtre['date_fin'] =$date_fin;
                    $data_filtre['date_deb'] =$date_deb;
                }
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
                        $data[$key]['nbr_femme'] = $nbr->nbr_femme;
                        $data[$key]['nbr_homme'] = $nbr->nbr_homme;
                        $total = $total + $nbr->nbr;
                    }

                   
                }
            }

            if ($type_etat == 'transfert_monetaire_individu') 
            {
                
                $transfer = $this->ReportingManager->find_sum_individu($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
                if($transfer!=null)
                {
                    $data=$transfer;
                    $data_filtre['date_fin'] =$date_fin;
                    $data_filtre['date_deb'] =$date_deb;
                }

            }
            
            if (count($data)>0)
            {
                $excel=$this->exportexcel($repertoire,$type_etat,$data,$data_filtre);
            } else {
                $this->response([
                    'status' => FALSE,
                    'response' => array(),
                    'message' => 'No data were found'
                ], REST_Controller::HTTP_OK);
            }
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
    public function exportexcel($repertoire,$type_etat,$data,$data_filtre)
    {
        require_once 'Classes/PHPExcel.php';
        require_once 'Classes/PHPExcel/IOFactory.php';

        $nom_file='excel_individu';
        $ile ='';
        $region ='';
        $commune ='';
        $village ='';
        $date_debut ='';
        $date_final ='';
        
        if (isset($data_filtre['ile']))
        {
           $ile = $data_filtre['ile']; 
        }
        if (isset($data_filtre['region']))
        {
           $region = $data_filtre['region']; 
        }
        if (isset($data_filtre['commune']))
        {
           $commune = $data_filtre['commune']; 
        }
        if (isset($data_filtre['village']))
        {
           $village = $data_filtre['village']; 
        }
        if (isset($data_filtre['date_deb']))
        {
           $date_debut = $data_filtre['date_deb']; 
        }

        if (isset($data_filtre['date_fin']))
        {
           $date_final = $data_filtre['date_fin']; 
        } 
        $directoryName = dirname(__FILE__) ."/../../../../assets/excel/".$repertoire;;
        
        if(!is_dir($directoryName))
        {
            mkdir($directoryName, 0777,true);
        }
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Myexcel")
                    ->setLastModifiedBy("Me")
                    ->setTitle("Rapport individu")
                    ->setSubject("Rapport individu")
                    ->setDescription("Rapport individu")
                    ->setKeywords("Rapport individu")
                    ->setCategory("Rapport individu");

        $ligne=1;
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
        $objPHPExcel->getActiveSheet()->getPageMargins()->SetLeft(0.64); //***pour marge gauche
        $objPHPExcel->getActiveSheet()->getPageMargins()->SetRight(0.64);        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->setTitle("Rapport ndividu");
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&R&11&B Page &P / &N');
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->setEvenFooter('&R&11&B Page &P / &N');
        //$objPHPExcel->getActiveSheet()->setShowGridlines(false);
        $styleTitre = array
        (
        'alignment' => array
            (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                
            ),
        'font' => array
            (
                'name'  => 'Calibri',
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
                'name'  => 'Calibri',
                'bold'  => true,
                'size'  => 11
            ),
        );
        $styleEntete = array
        (
            'alignment' => array
            (
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                
            ),
            
            'font' => array
            (
                'name'  => 'Calibri',
                'bold'  => true,
                'size'  => 11
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
            ),
        'font' => array
            (
                'name'  => 'Calibri',
                'size'  => 11
            )
        );
        if($type_etat=='nbr_pers_avec_andicap')
        {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":D".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'NOMBRE DE PERSONNE VIVANT AVEC HANDICAP ENREGISTREES');
        
            $ligne++;
            
            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
            
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
               $objPHPExcel->getActiveSheet()->mergeCells("C".$ligne.":D".$ligne);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'HANDICAP');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'NOMBRE');
            
            foreach ($data as $key => $value)
            {   
                $ligne++;
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
              $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
               $objPHPExcel->getActiveSheet()->mergeCells("C".$ligne.":D".$ligne);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['libelle']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['nbr']);
                 
            }
        }
        if($type_etat == 'individu_par_programme')
        {   
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":G".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'INDIVIDU PAR PROGRAMME');
        
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;           
           
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Numero d\'enregistrement');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Nom');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Date de naissance');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Actvité');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, 'Travailleur');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, 'Sexe');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, 'Programme');
            
            foreach ($data as $key => $value)
            {   
                $ligne++;
                $sexe='';
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['NumeroEnregistrement']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['Nom']);

                $CurrentDate = $value['DateNaissance'];
                $newDate = date("d-m-Y", strtotime($CurrentDate));

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $newDate);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value['Activite']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, $value['travailleur']);
                switch ($value['sexe'])
                {
                    case '0':
                        $sexe='Femme';
                        break;
                    case '1':
                        $sexe='Homme';
                        break;
                    default:
                        $sexe='la BDD n\'est pas à jour';
                        break;
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, $sexe);
                foreach ($value['tab_programme'] as $keyprogramme => $valueprogramme)
                {
                   $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, '-'.$valueprogramme);
                }  
            }
            
        } 
        if($type_etat == 'nbr_enfant_mal_nouri')
        {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":E".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":E".$ligne)->applyFromArray($styleTitre);
            //$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'NOMBRE DE CAS DE MAL NUTRITION');
        
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
           
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":E".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":E".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":E".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Mal nutrition très sévère');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Mal nutrition sévère');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Mal nutrition modérée');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Sortie PECMA');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, 'Poids median');
            foreach ($data as $key => $value)
            {
                $ligne++;
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":E".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['tres_severe']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['severe_mas']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['moderee_mam']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value['sortie_pecma']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, $value['poids_median']);
            }
            
        } 
        if($type_etat == 'nbr_individu_par_programme')
        {
           $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":D".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'NOMBRE INDIVIDU PAR PROGRAMME');
        
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
           
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Programme');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Nombre individu bénéficiaire');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Nombre individu enregistré');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Statistique');
            foreach ($data as $key => $value)
            {
                $ligne++;
                $statistique='0';
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['libelle']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['nbr']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['nbr_individu_enregistrer']);
                if($value['nbr_individu_enregistrer'])
                {
                  $statistique=number_format(($value['nbr']*100)/$value['nbr_individu_enregistrer'],2,",",".");
                    
                }
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $statistique.'%');
            } 
        } 
        
        if($type_etat == "nbr_individu_par_formation")
        {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":D".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'NOMBRE INDIVIDU PAR FORMATION');
        
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
           
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Type de formation');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Nombre d\'homme');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Nombre femme');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Nombre total');
            foreach ($data as $key => $value)
            {
                $ligne++;
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['description']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['nbr_homme']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['nbr_femme']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value['nbr']);
            } 
        } 
        if($type_etat == "nbr_mariage_precoce")
        {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":D".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'NOMBRE DE MARIAGE PRECOCE');
        
            $ligne++;
            $insert= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
           $ligne= $insert;

            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])||isset($data_filtre['date_deb'])) {
                $ligne=$insert+1;
            }
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
            $objPHPExcel->getActiveSheet()->mergeCells("C".$ligne.":D".$ligne);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Type de mariage');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Nombre');;

             foreach ($data as $key => $value)
            {
                $ligne++;
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
                $objPHPExcel->getActiveSheet()->mergeCells("C".$ligne.":D".$ligne);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value->type_mariage);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value->nbr);
            }
               
        } 
        if($type_etat == "nbr_violence")
        {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":D".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'NOMBRE DE VIOLENCE');
        
            $ligne++;
           $insert= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
           $ligne= $insert;
           
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])||isset($data_filtre['date_deb'])) {
                $ligne=$insert+1;
            }

            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
            $objPHPExcel->getActiveSheet()->mergeCells("C".$ligne.":D".$ligne);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Type de violence');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Nombre');;

            foreach ($data as $key => $value)
            {
                $ligne++;
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":D".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
                $objPHPExcel->getActiveSheet()->mergeCells("C".$ligne.":D".$ligne);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value->type_violence);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value->nbr);
            }
              
        } 
        if($type_etat == 'transfert_monetaire_individu')
        {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":H".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->applyFromArray($styleTitre);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'HISTORIQUE DE TRANSFERT MONETAIRE');
        
            $ligne++;
            $insert= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
           $ligne= $insert;
           
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])||isset($data_filtre['date_deb'])) {
                $ligne=$insert+1;
            }
            
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->applyFromArray($stylesousTitre);
            $objPHPExcel -> getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->getNumberFormat()->setFormatCode('00');
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->getAlignment()->setWrapText(true);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Numero d\'enregistrement');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, 'Chef Ménage');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, 'Individu');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, 'Date');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, 'Partenaire');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, 'Agence de paiement');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, 'Type de transfert');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$ligne, 'Montant (KMF)');
            
            foreach ($data as $key => $value)
            {   
                $ligne++;
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value->numero);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value->chef_menage);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value->nom_individu);

                $CurrentDate = $value->date_suivi;
                $newDate = date("d-m-Y", strtotime($CurrentDate));
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $newDate);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, $value->nom_partenaire);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, $value->nom_agence_payement);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, $value->type_transfert);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$ligne, $value->somme_montant);
  
            }
        }

        try
        {

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save(dirname(__FILE__) . "/../../../../assets/excel/individu/".$nom_file.".xlsx");

            $this->response([
                'status' => TRUE,
                'nom_file' => $nom_file.".xlsx",
                'message' => 'file writed in server',
                'filtre' => $data_filtre,
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

   public function insertFiltre($ile,$region,$commune,$village,$date_deb,$date_fin,$style,$ligne,$objPHPExcel)
    {
        if ($ile)
            {   
               $objPHPExcel->getActiveSheet()->getStyle("A".$ligne)->applyFromArray($style);
               $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
               
               $objRichText = new PHPExcel_RichText();

               $titre = $objRichText->createTextRun('ILE                     : ');
               $titre->getFont()->applyFromArray(array( "bold" => true, "size" => 11, "name" => "Calibri"));

               $contenu = $objRichText->createTextRun($ile);
               $contenu->getFont()->applyFromArray(array("size" => 11, "name" => "Calibri"));
               $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne,$objRichText);
               $ligne++; 
            }

            if ($region)
            {   
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);

                $objRichText = new PHPExcel_RichText();

                $titre = $objRichText->createTextRun('PREFECTURE : ');
                $titre->getFont()->applyFromArray(array( "bold" => true, "size" => 11, "name" => "Calibri"));

               $contenu = $objRichText->createTextRun($region);
               $contenu->getFont()->applyFromArray(array("size" => 11, "name" => "Calibri"));
               
               $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne,$objRichText);
               $ligne++; 
            }

            if ($commune)
            {
               $objPHPExcel->getActiveSheet()->getStyle("A".$ligne)->applyFromArray($style);
               $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);
               $objRichText = new PHPExcel_RichText();

                $titre = $objRichText->createTextRun('COMMUNE   : ');
                $titre->getFont()->applyFromArray(array( "bold" => true, "size" => 11, "name" => "Calibri"));

               $contenu = $objRichText->createTextRun($commune);
               $contenu->getFont()->applyFromArray(array("size" => 11, "name" => "Calibri"));
               
               $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne,$objRichText);
               $ligne++; 
            }

            if ($village)
            {
               $objPHPExcel->getActiveSheet()->getStyle("A".$ligne)->applyFromArray($style);
               $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);

               $objRichText = new PHPExcel_RichText();

                $titre = $objRichText->createTextRun('VILLAGE          : ');
                $titre->getFont()->applyFromArray(array( "bold" => true, "size" => 11, "name" => "Calibri"));

               $contenu = $objRichText->createTextRun($village);
               $contenu->getFont()->applyFromArray(array("size" => 11, "name" => "Calibri"));
               
               $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne,$objRichText);
               $ligne++; 
            }
            if ($date_deb && $date_fin)
            {   $ligne++;
               $objPHPExcel->getActiveSheet()->getStyle("A".$ligne)->applyFromArray($style);
               $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":B".$ligne);

               $FiltreDateDeb  = date("d-m-Y", strtotime($date_deb));
               $FiltreDateFin  = date("d-m-Y", strtotime($date_fin));

               $objRichText = new PHPExcel_RichText();

                $titre = $objRichText->createTextRun('Date du           : ');
                $titre->getFont()->applyFromArray(array( "bold" => true, "size" => 11, "name" => "Calibri"));

               $contenu = $objRichText->createTextRun($FiltreDateDeb.' au '.$FiltreDateFin);
               $contenu->getFont()->applyFromArray(array("size" => 11, "name" => "Calibri"));
               
               $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne,$objRichText);
               $ligne++; 
            }
            return $ligne;
    }
}
?>