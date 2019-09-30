<?php
require APPPATH . 'libraries/Status.php';

class Login_model extends CI_Model {

    public function isExist($uname, $password)
    {
      $key = $this->config->item('encryption_key');
      $getPass = "SELECT password FROM admin WHERE uname = ?";
      $queryGetPass = $this->db->query($getPass, array($uname));

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
              return Status::UNAME_ATAU_PASSWORD_SALAH;
            }
          }
        } else {
          return Status::ADMIN_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_PASSWORD_GAGAL;
      }
    }
}
?>
