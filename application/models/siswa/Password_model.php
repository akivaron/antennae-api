<?php
require APPPATH . 'libraries/Status.php';

class Password_model extends CI_Model {

    public function updatePassByNis($data)
    {
      $key = $this->config->item('encryption_key');
      $getPass = "SELECT password FROM students WHERE nis = ?";
      $queryGetPass = $this->db->query($getPass, array($data['nis']));

      if($queryGetPass)
      {
        if($queryGetPass->num_rows()>0)
        {
          foreach($queryGetPass->result() as $get)
          {
            $hashed_password_lama = password_verify($data['password_lama'].$key, $get->password);
            if($hashed_password_lama)
            {
               $hashed_password_baru = password_hash($data['password_baru'].$key, PASSWORD_BCRYPT);

               $dataUpdate = array(
                 'password' => $hashed_password_baru
               );

               $queryUpdate = $this->db->set($dataUpdate)
                                      ->where('nis',$data['nis'])
                                      ->update('students');

               if($queryUpdate)
               {
                 return Status::QUERY_UPDATE_PASSWORD_BERHASIL;
               } else {
                 return Status::QUERY_UPDATE_PASSWORD_GAGAL;
               }
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
