<?php
require APPPATH . 'libraries/Status.php';

class Howtouse_model extends CI_Model {

    public function getHowToUseVideo()
    {
      $get = $this->getLisVideo();
      if($get->num_rows()>0)
      {
        return $get;
      } else {
        return Status::CARA_PENGGUNAAN_KOSONG;
      }
    }

    public function getLisVideo()
    {
      $get = "SELECT * FROM howtouses";
      $queryGet = $this->db->query($get);
      return $queryGet;
    }
}
?>
