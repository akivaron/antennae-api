<?php
require APPPATH . 'libraries/Status.php';

class Register_model extends CI_Model {

    public function insert($data)
    {
       $getSiswa = $this->isExist($data['email'], $data['nis']);
       if($getSiswa) {
         if(!$getSiswa->num_rows()>0)
         {
           $key   = $this->config->item('encryption_key');
           $hashed_password = password_hash($data['password'].$key, PASSWORD_BCRYPT);

           $dataInsert = array(
              'nis'      => $data['nis'],
              'name'     => $data['name'],
              'class'    => $data['class'],
              'email'    => $data['email'],
              'password' => $hashed_password,
              'created_at' => date("Y-m-d H:i:s")
            );

            if($this->db->insert('students', $dataInsert))
            {
              return Status::QUERY_INSERT_SISWA_BERHASIL;
            } else {
              return Status::QUERY_INSERT_SISWA_GAGAL;
            }
         } else {
           return Status::EMAIL_ATAU_NIS_SUDAH_TERDAFTAR;
         }
       } else {
         return Status::QUERY_CEK_SISWA_BY_EMAIL_N_NIS_GAGAL;
       }
    }

    public function isExist($email, $nis)
    {
      $sql = "SELECT id FROM students WHERE email = ? OR nis = ?";
      $query = $this->db->query($sql, array($email, $nis));
      return $query;
    }
}
?>
