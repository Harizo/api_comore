<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Export_excel_menage extends REST_Controller 
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
        //Historique transfert monetaire
    	if ($selection == 'transfert_monetaire_menage') 
    	{
    		
    		$donnee = $this->ReportingManager->find_sum($date_deb, $date_fin,$this->generer_requete_analyse($id_ile,$id_region,$id_commune,$id_village));
            if ($donnee!=null) {
                $data = $donnee;
                $data_filtre['date_fin'] =$date_fin;
                $data_filtre['date_deb'] =$date_deb;
            }
    		
    	}
    	
        //Nombre ménage par programme
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
        //Ménage par programme
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
       
        
    	if (count($data)>0)
        {
            $export=$this->export($data,$repertoire,$selection,$data_filtre);
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
    
    public function export($data,$repertoire,$selection,$data_filtre){
    	require_once 'Classes/PHPExcel.php';
        require_once 'Classes/PHPExcel/IOFactory.php';

        $nom_file='menage';
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
            )
        );
        //transfert monétaire
        if ($selection =='transfert_monetaire_menage') {
            $objPHPExcel->getActiveSheet()->getRowDimension($ligne)->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->mergeCells("A".$ligne.":G".$ligne);
            $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":G".$ligne)->applyFromArray($styleTitre);            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Historique transfert monetaire');            
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
            
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

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
                
                $CurrentDate = $value->date_suivi;
                $newDate = date("d-m-Y", strtotime($CurrentDate));
               
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $newDate);
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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Nombre ménage par programme');            
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
            
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, 'Ménage par programme');            
            $ligne++;

            $ligneFiltre= $this->insertFiltre($ile,$region,$commune,$village,$date_debut,$date_final,$styleEntete,$ligne,$objPHPExcel);
           
            $ligne=$ligneFiltre;
            
            if (isset($data_filtre['ile'])||isset($data_filtre['region'])||isset($data_filtre['commune'])||isset($data_filtre['village'])) {
                $ligne=$ligneFiltre+1;
            }

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
                $objPHPExcel->getActiveSheet()->getStyle("A".$ligne.":H".$ligne)->applyFromArray($stylecontenu);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$ligne, $value['NumeroEnregistrement']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$ligne, $value['nomchefmenage']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$ligne, $value['SexeChefMenage']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$ligne, $value['agechefdemenage']);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$ligne, $value['Addresse']);

                $CurrentDate = $value['DateInscription'];
                $newDate = date("d-m-Y", strtotime($CurrentDate));

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$ligne, $newDate);
               
                foreach ($value['tab_programme'] as $pro => $progr){
                     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$ligne, '- '.$progr);
                    
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