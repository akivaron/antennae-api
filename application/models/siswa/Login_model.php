<?php
require APPPATH . 'libraries/Status.php';

class Login_model extends CI_Model {

    public function isExist($nis, $password)
    {
      $key = $this->config->item('encryption_key');
      $getPass = "SELECT password FROM students WHERE nis = ?";
      $queryGetPass = $this->db->query($getPass, array($nis));

      if($queryGetPass)
      {
        if($queryGetPass->num_rows()>0)
        {
          foreach($queryGetPass->result() as $get)
          {
            $cekPass = password_verify($password.$key, $get->password);
            if($cekPass)
            {
              return Status::BERHASIL_LOGIN;
            } else {
              return Status::NIS_ATAU_PASSWORD_SALAH;
            }
          }
        } else {
          return Status::SISWA_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_PASSWORD_GAGAL;
      }
    }
}
?>
