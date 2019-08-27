<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delete_ddb_model extends CI_Model {
    

    public function delete($table) {
        $resp = $this->db->empty_table($table); 
        if($resp) {
            return true;
        }
        else
        {
            return null;
        }  
    }

    public function add($data, $table)  {
        $this->db->set($this->_set($data, $table))
                            ->insert($table);
        if($this->db->affected_rows() === 1)  {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }

     public function _set($type_acteur, $table) {

        if ($table == 'source_financement') 
        {
            return array(
                'id' => $type_acteur['id'],
                'nom' => $type_acteur['nom']
            );
        } else if($table == 'see_village') {
            return array(
                'id' => $type_acteur['id'],
                'commune_id' => $type_acteur['commune_id'],
                'Code' => $type_acteur['Code'],
                'Village' => $type_acteur['Village'],
                'programme_id' => $type_acteur['programme_id'],
                'zone_id' => $type_acteur['zone_id'],
                'nbrpopulation' => $type_acteur['nbrpopulation'],
                'a_ete_modifie' => $type_acteur['a_ete_modifie'],
                'supprime' => $type_acteur['supprime'],
                'userid' => $type_acteur['userid'],
                'datemodification' => $type_acteur['datemodification'],
            );
			
		} else if($table == 'see_commune') {
            return array(
                'id' => $type_acteur['id'],
                'Code' => $type_acteur['Code'],
                'Commune' => $type_acteur['Commune'],
                'zone_id' => $type_acteur['zone_id'],
                'nombremenage' => $type_acteur['nombremenage'],
                'programme_id' => $type_acteur['programme_id'],
                'region_id' => $type_acteur['region_id'],
                'a_ete_modifie' => $type_acteur['a_ete_modifie'],
                'supprime' => $type_acteur['supprime'],
                'userid' => $type_acteur['userid'],
                'datemodification' => $type_acteur['datemodification'],
            );
		} else if($table == 'see_region'){
            return array(
                'id' => $type_acteur['id'],
                'ile_id' => $type_acteur['ile_id'],
                'Code' => $type_acteur['Code'],
                'Region' => $type_acteur['Region'],
                'programme_id' => $type_acteur['programme_id'],
                'a_ete_modifie' => $type_acteur['a_ete_modifie'],
                'supprime' => $type_acteur['supprime'],
                'userid' => $type_acteur['userid'],
                'datemodification' => $type_acteur['datemodification'],
            );
		} else if($table == 'see_ile') {
            return array(
                'id' => $type_acteur['id'],
                'Code' => $type_acteur['Code'],
                'Ile' => $type_acteur['Ile'],
     /*           'programme_id' => $type_acteur['programme_id'],
                'a_ete_modifie' => $type_acteur['a_ete_modifie'],
                'supprime' => $type_acteur['supprime'],
                'userid' => $type_acteur['userid'],
                'datemodification' => $type_acteur['datemodification'],*/
            );			
		}
        else
        {
            return array(
                'id' => $type_acteur['id'],
                'description' => $type_acteur['description']
            );
        }
        
    }
	public function Restaurer_index_et_foreign_key($table) {
			if($table=='see_ile') {
				// Restaurer INDEX
				$requete="CREATE INDEX IDX_69FF2D3B62BB7AEE ON see_ile (programme_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_1B36F55257037E6E ON see_region (ile_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_1B36F55262BB7AEE ON see_region (programme_id)";
				$query= $this->db->query($requete);				
				$requete="CREATE INDEX FK_utilisateur_see_ile ON utilisateur (id_ile)";
				$query= $this->db->query($requete);				
				$requete ="ALTER TABLE utilisateur ADD FOREIGN KEY FK_utilisateur_see_ile(id_ile) REFERENCES see_ile(id) ON DELETE NO ACTION ON UPDATE CASCADE";
				$query= $this->db->query($requete);
			} else if($table=='see_region') {
				// Restaurer INDEX et FOREIGN KEY
				$requete="CREATE INDEX IDX_DEF5613B98260155 ON see_commune (region_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_DEF5613B62BB7AEE ON see_commune (programme_id)";
				$query= $this->db->query($requete);		
				$requete ="ALTER TABLE see_commune ADD FOREIGN KEY FKcommune_region(region_id) REFERENCES see_region(id) ON DELETE NO ACTION ON UPDATE CASCADE";
				$query= $this->db->query($requete);
				$requete ="ALTER TABLE see_commune ADD FOREIGN KEY FKcommune_programme(programme_id) REFERENCES programme(id) ON DELETE NO ACTION ON UPDATE CASCADE";
				$query= $this->db->query($requete);
			} else if($table=='see_commune') {
				// Restaurer INDEX
				$requete="CREATE INDEX IDX_727BCF7F131A4F72 ON see_village (commune_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_727BCF7F62BB7AEE ON see_village (programme_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_727BCF7F9F2C3FAB ON see_village (zone_id)";
				$query= $this->db->query($requete);
			} else if($table=='see_village') {				
				// Restaurer INDEX
				$requete="CREATE INDEX IDX_727BCF7F131A4F72 ON see_village (commune_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_727BCF7F62BB7AEE ON see_village (programme_id)";
				$query= $this->db->query($requete);
				$requete="CREATE INDEX IDX_727BCF7F9F2C3FAB ON see_village (zone_id)";
				$query= $this->db->query($requete);
			}	
		/*if($table=='see_ile') {
			//Suppression INDEX Colonne id_ile dans utilisateur : DROP Foreign key avant INDEX et contrairement lors de la restauration : INDEX puis FOREIGN KEY
			$requete ="ALTER TABLE utilisateur DROP FOREIGN KEY FK_utilisateur_see_ile" ;				
			$query= $this->db->query($requete);	
			$requete="ALTER TABLE utilisateur DROP INDEX FK_utilisateur_see_ile";
			$query= $this->db->query($requete);			
			// Suppression INDEX Colonne programme_id dans see_ile
			$requete="ALTER TABLE see_ile DROP INDEX IDX_69FF2D3B62BB7AEE";
			$query= $this->db->query($requete);
			// Suppression INDEX Colonne ile_id dans see_region
			$requete="ALTER TABLE see_region DROP INDEX IDX_1B36F55257037E6E";
			$query= $this->db->query($requete);
			// Suppression INDEX Colonne programme_id dans see_region
			$requete="ALTER TABLE see_region DROP INDEX IDX_1B36F55262BB7AEE";
			$query= $this->db->query($requete);			
		} else if($table=='see_region') {
			// Suppression INDEX Colonne region_id dans see_commune
			$requete="ALTER TABLE see_region DROP INDEX IDX_DEF5613B98260155";
			$query= $this->db->query($requete);			
			// Suppression INDEX Colonne programme_id dans see_commune
			$requete="ALTER TABLE see_region DROP INDEX IDX_DEF5613B62BB7AEE";
			$query= $this->db->query($requete);		
			// Suppression FOREIGN KEY	
			$requete ="ALTER TABLE see_commune DROP FOREIGN KEY FKcommune_region" ;				
			$query= $this->db->query($requete);	
			$requete ="ALTER TABLE see_commune DROP FOREIGN KEY FKcommune_programme" ;				
			$query= $this->db->query($requete);	
		} else if($table=='see_commune') {
			// Suppression INDEX Colonne commune_id dans see_village
			$requete="ALTER TABLE see_region DROP INDEX IDX_727BCF7F131A4F72";
			$query= $this->db->query($requete);			
			// Suppression INDEX Colonne programme_id dans see_village
			$requete="ALTER TABLE see_region DROP INDEX IDX_727BCF7F62BB7AEE";
			$query= $this->db->query($requete);			
			// Suppression INDEX Colonne zone_id dans see_village
			$requete="ALTER TABLE see_region DROP INDEX IDX_727BCF7F9F2C3FAB";
			$query= $this->db->query($requete);			
		} else if($table=='see_village') { 			
			// Suppression INDEX Colonne commune_id dans see_village
			$requete="ALTER TABLE see_region DROP INDEX IDX_727BCF7F131A4F72";
			$query= $this->db->query($requete);			
			// Suppression INDEX Colonne programme_id dans see_village
			$requete="ALTER TABLE see_region DROP INDEX IDX_727BCF7F62BB7AEE";
			$query= $this->db->query($requete);			
			// Suppression INDEX Colonne zone_id dans see_village
			$requete="ALTER TABLE see_region DROP INDEX IDX_727BCF7F9F2C3FAB";
			$query= $this->db->query($requete);			
		} */	
		return true;	
	}

}
?>