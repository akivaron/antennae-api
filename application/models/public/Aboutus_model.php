<?php
require APPPATH . 'libraries/Status.php';

class Aboutus_model extends CI_Model {

    public function getAbout()
    {
      $get = "SELECT id, content FROM abouts limit 1";
      $queryGet = $this->db->query($get);
      return $queryGet;
    }
}
?>
