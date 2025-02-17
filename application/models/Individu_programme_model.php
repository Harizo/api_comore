<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Individu_programme_model extends CI_Model
{
    protected $table = 'individu_programme';


    public function add($individu_programme)
    {
        $this->db->set($this->_set($individu_programme))
                            ->insert($this->table);
        if($this->db->affected_rows() === 1)
        {
            return $this->db->insert_id();
        }else{
            return null;
        }                    
    }


    public function update($id, $individu_programme)
    {
        $this->db->set($this->_set($individu_programme))
                            ->where('id', (int) $id)
                            ->update($this->table);
        if($this->db->affected_rows() === 1)
        {
            return true;
        }else{
            return null;
        }                      
    }

    public function _set($individu_programme)
    {
        return array(
            'id_serveur_centrale'          =>      $individu_programme['id_serveur_centrale'],
            'id_individu'          =>      $individu_programme['id_individu'],
            'id_programme'         =>      $individu_programme['id_programme']                      
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
        $result =  $this->db->select('individu.id as id_individu,
                                        individu_programme.id as id,
                                        individu.Nom as Nom,
                                        individu.DateNaissance as DateNaissance,
                                        individu.Activite as Activite,
                                        individu.aptitude as aptitude,
                                        individu.travailleur as travailleur
                                        ')
                        ->from($this->table)
                        ->join('individu', 'individu.id = individu_programme.id_individu')
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

    public function findAllByindividu($id_individu)
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
    public function findAllByProgrammeAndVillage($id_programmes,$id_village)
    {
		$requete="select mp.id,mp.id_individu,i.Nom,i.DateNaissance,i.menage_id,i.sexe,m.NumeroEnregistrement,m.Addresse,m.nomchefmenage"
				." from individu_programme as mp"
				." left outer join individu as i on i.id=mp.id_individu"
				." left outer join menage as m on m.id=i.menage_id"
				." left outer join see_village as v on v.id=m.village_id"
                ." where mp.id_programme like ".$id_programmes
				." and v.id=".$id_village;	
				$result = $this->db->query($requete)->result();
        if($result)
        {
            return $result;
        }else{
            return null;
        }                  
    }

}
