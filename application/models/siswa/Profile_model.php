<?php
require APPPATH . 'libraries/Status.php';

class Profile_model extends CI_Model {

    public function updateSiswa($data)
    {
       if($this->isExist($data['nis'])>0)
       {
         if($this->isEmailExist($data['email'], $data['nis'])>0) {
           return Status::EMAIL_SUDAH_TERDAFTAR;
         } else {
           $getPass = $this->getPassByNis($data['nis']);
           if($getPass)
           {
             $key = $this->config->item('encryption_key');
             foreach($getPass->result() as $get)
             {
               $cekPass = password_verify($data['password'].$key, $get->password);
               if($cekPass)
               {
                 $dataUpdate = array(
                    'nis'      => $data['nis'],
                    'name'     => $data['name'],
                    'class'    => $data['class'],
                    'email'    => $data['email'],
                    'created_at' => date("Y-m-d H:i:s")
                  );

                  $this->db->where('nis', $data['nis']);
                  if($this->db->update('students', $dataUpdate))
                  {
                    return Status::QUERY_UPDATE_SISWA_BERHASIL;
                  } else {
                    return Status::QUERY_UPDATE_SISWA_GAGAL;
                  }
               } else {
                 return Status::PASSWORD_SALAH;
               }
             }
           } else {
             return Status::QUERY_CEK_PASSWORD_GAGAL;
           }
         }
       } else {
         return Status::NIS_TIDAK_TERDAFTAR;
       }
    }

    public function getSiswaByNis($nis)
    {
      $sql = "SELECT nis,name,class,email,created_at FROM students WHERE nis = ?";
      $query = $this->db->query($sql, array($nis));
      return $query;
    }

    public function getPassByNis($nis)
    {
      $getPass = "SELECT password FROM students WHERE nis = ?";
      $queryGetPass = $this->db->query($getPass, array($nis));
      return $queryGetPass;
    }

    public function isExist($nis)
    {
      $sql = "SELECT id FROM students WHERE nis = ?";
      $query = $this->db->query($sql, array($nis));
      return $query->num_rows();
    }

    public function isEmailExist($email, $nis)
    {
      $sql = "SELECT id FROM students WHERE email = ? AND nis != ?";
      $query = $this->db->query($sql, array($email, $nis));
      return $query->num_rows();
    }
}
?>
