<?php
require APPPATH . 'libraries/Status.php';

class JawabanBenarEssay_model extends CI_Model {

    public function checkIdSoal($id) {
      $getList = "SELECT id FROM soal where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDSOAL_GAGAL;
      }
    }

    public function checkIdJawabanBenarEssay($id)
    {
      $getList = "SELECT id FROM jawaban_benar_essay where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDJAWABANBENARESSAY_GAGAL;
      }
    }

    public function validateId($data)
    {
      $checkIdSoal = this->checkIdSoal($data['id_soal']);
      if($checkIdSoal != Status::QUERY_CHECK_IDSOAL_GAGAL)
      {
        if($checkIdSoal->num_rows()>0)
        {
          $checkIdJawabanBenarEssay = this->checkIdJawabanBenarEssay($data['id']);
          if($checkIdJawabanBenarEssay != Status::QUERY_CHECK_IDJAWABANBENARESSAY_GAGAL)
          {
            if($checkIdJawabanBenarEssay->num_rows()>0)
            {
              return Status::QUERY_CHECK_IDJAWABANBENARESSAY_ISI;
            } else {
              return Status::QUERY_CHECK_IDJAWABANBENARESSAY_KOSONG;
            }
          } else {
            return Status::QUERY_CHECK_IDJAWABANBENARESSAY_GAGAL;
          }
        } else {
          return Status::QUERY_CHECK_IDSOAL_KOSONG;
        }
      } else {
         return Status::QUERY_CHECK_IDSOAL_GAGAL;
      }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_soal'      => $data['id_soal'],
         'content'      => $data['content'],
         'created_by'   => $data['created_by'],
         'date_created' => date("Y-m-d H:i:s")
       );

       $checkIdSoal = this->checkIdSoal($data['id_soal']);
       if($checkIdSoal != Status::QUERY_CHECK_IDSOAL_GAGAL)
       {
         if($checkIdSoal->num_rows()>0) {
           if($this->db->insert('jawaban_benar_essay', $dataInsert))
           {
             return Status::QUERY_INSERT_JAWABANBENARESSAY_BERHASIL;
           } else {
             return Status::QUERY_INSERT_JAWABANBENARESSAY_GAGAL;
           }
         } else {
           return Status::QUERY_CHECK_IDSOAL_KOSONG;
         }
       } else {
          return Status::QUERY_CHECK_IDSOAL_GAGAL;
       }
    }

    public function update($data)
    {
        $validateId = this->validateId($data);
        if($validateId === Status::QUERY_CHECK_IDJAWABANBENARESSAY_ISI)
        {
            $dataUpdate = array(
              'id_soal'      => $data['id_soal'],
              'content'      => $data['content'],
              'created_by'   => $data['created_by'],
              'date_created' => date("Y-m-d H:i:s")
            );

           $queryUpdate = $this->db->set($dataUpdate)
                                  ->where('id',$data['id'])
                                  ->update('jawaban_benar_essay');

           if($queryUpdate)
           {
             return Status::QUERY_UPDATE_JAWABANBENARESSAY_BERHASIL;
           } else {
             return Status::QUERY_UPDATE_JAWABANBENARESSAY_GAGAL;
           }
       } else {
         return $validateId;
       }
    }

    public function delete($data)
    {
        $validateId = this->validateId($data);
        if($validateId === Status::QUERY_CHECK_IDJAWABANBENARESSAY_ISI)
        {
           $queryDelete = $this->db->where('id',$data['id'])
                                   ->delete('jawaban_benar_essay');

           if($queryDelete)
           {
             return Status::QUERY_DELETE_JAWABANBENARESSAY_BERHASIL;
           } else {
             return Status::QUERY_DELETE_JAWABANBENARESSAY_GAGAL;
           }
        } else {
          return $validateId;
        }
    }
}
?>
