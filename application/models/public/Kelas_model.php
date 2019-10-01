<?php
require APPPATH . 'libraries/Status.php';

class Kelas_model extends CI_Model {

    public function getKelas()
    {
      $getKelas = "SELECT id, class FROM student_classes";
      $queryGetKelas = $this->db->query($getKelas);
      return $queryGetKelas;
    }
}
?>
