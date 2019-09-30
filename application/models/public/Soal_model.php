<?php
require APPPATH . 'libraries/Status.php';

class Soal_model extends CI_Model {

    public function getSoal($id_material_subs)
    {
      $geSoal = "SELECT * FROM questions where id_material_subs = ?";
      $queryGetSoal = $this->db->query($geSoal,array($id_material_subs));
      return $queryGetSoal;
    }

    // public function getSoal($id_material_subs)
    // {
    //   $geSoal = "SELECT a.*,b.id_answer FROM questions a INNER JOIN answer_key_multiple_choices b ON a.id = b.id_question where id_material_subs = ?";
    //   $queryGetSoal = $this->db->query($geSoal,array($id_material_subs));
    //   return $queryGetSoal;
    // }
}
?>
