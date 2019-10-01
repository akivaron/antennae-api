<?php
require APPPATH . 'libraries/Status.php';

class JawabanBenarPilgan_model extends CI_Model {

    public function checkIdSoal($id) {
      $getList = "SELECT id FROM soal where id = ?";
      $checkIdM = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDSOAL_GAGAL;
      }
    }

    public function checkIdJawaban($id)
    {
      $getList = "SELECT id FROM jawaban where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDJAWABAN_GAGAL;
      }
    }

    public function checkIdJawabanBenarPilgan($id)
    {
      $getList = "SELECT id FROM jawaban_benar_pilgan where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDJAWABANBENARPILGAN_GAGAL;
      }
    }

    public function validateIdSoalNJawaban($data)
    {
      $checkIdSoal = this->checkIdSoal($data['id_soal']);
      if($checkIdSoal != Status::QUERY_CHECK_IDSOAL_GAGAL)
      {
        if($checkIdSoal->num_rows()>0)
        {
          $checkIdJawaban = this->checkIdJawaban($data['id']);
          if($checkIdJawaban != Status::QUERY_CHECK_IDJAWABAN_GAGAL)
          {
            if($checkIdJawaban->num_rows()>0)
            {
              return Status::QUERY_CHECK_IDJAWABAN_ISI;
            } else {
              return Status::QUERY_CHECK_IDJAWABAN_KOSONG;
            }
          } else {
            return Status::QUERY_CHECK_IDJAWABAN_GAGAL;
          }
        } else {
          return Status::QUERY_CHECK_IDSOAL_KOSONG;
        }
      } else {
         return Status::QUERY_CHECK_IDSOAL_GAGAL;
      }
    }

    public function validateId($data)
    {
      $validateIdSoalNJawaban = this->validateIdSoalNJawaban($data);
      if($validateIdSoalNJawaban == Status::QUERY_CHECK_IDJAWABAN_ISI)
      {
        $checkIdJawabanBenarPilgan = this->checkIdJawabanBenarPilgan($data['id']);
        if($checkIdJawabanBenarPilgan != Status::QUERY_CHECK_IDJAWABANBENARPILGAN_GAGAL)
        {
          if($checkIdJawabanBenarPilgan->num_rows()>0)
          {
            return Status::QUERY_CHECK_IDJAWABANBENARPILGAN_ISI;
          } else {
            return Status::QUERY_CHECK_IDJAWABANBENARPILGAN_KOSONG;
          }
        } else {
          return Status::QUERY_CHECK_IDJAWABANBENARPILGAN_GAGAL;
        }
      } else {
         return $validateIdSoalNJawaban;
      }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_materi'         => $data['id_materi'],
         'id_jawaban'     => $data['name'],
         'created_by'   => $data['created_by'],
         'date_created' => date("Y-m-d H:i:s")
       );

       $validateIdSoalNJawaban = this->validateIdSoalNJawaban($data);
       if($validateIdSoalNJawaban == Status::QUERY_CHECK_IDJAWABAN_ISI)
       {
         if($this->db->insert('jawaban_benar_pilgan', $dataInsert))
         {
           return Status::QUERY_INSERT_JAWABANBENARPILGAN_BERHASIL;
         } else {
           return Status::QUERY_INSERT_JAWABANBENARPILGAN_GAGAL;
         }
       } else {
          return $validateIdSoalNJawaban;
       }
    }

    public function update($data)
    {
      $validateId = this->validateId($data);
      if($validateId === Status::QUERY_CHECK_IDJAWABANBENARPILGAN_ISI)
      {
        $dataUpdate = array(
          'id_materi'         => $data['id_materi'],
          'id_jawaban'     => $data['name']
         );

        $queryUpdate = $this->db->set($dataUpdate)
                               ->where('id',$data['id'])
                               ->update('jawaban_benar_pilgan');

        if($queryUpdate)
        {
          return Status::QUERY_UPDATE_JAWABANBENARPILGAN_BERHASIL;
        } else {
          return Status::QUERY_UPDATE_JAWABANBENARPILGAN_GAGAL;
        }
      } else {
        return $validateId;
      }
    }

    public function delete($data)
    {
      $validateId = this->validateId($data);
      if($validateId === Status::QUERY_CHECK_IDJAWABANBENARPILGAN_ISI)
      {
         $queryDelete = $this->db->where('id',$data['id'])
                                 ->delete('jawaban_benar_pilgan');

         if($queryDelete)
         {
           return Status::QUERY_DELETE_JAWABANBENARPILGAN_BERHASIL;
         } else {
           return Status::QUERY_DELETE_JAWABANBENARPILGAN_GAGAL;
         }
       } else {
         return $validateId;
       }
    }
}
?>
