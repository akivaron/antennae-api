<?php
require APPPATH . 'libraries/Status.php';

class Jawaban_model extends CI_Model {

    public function checkIdSoal($id) {
      $getList = "SELECT id FROM soal where id = ?";
      $checkId = $this->db->query($getList, array($id));
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

    public function validateId($data)
    {
      $checkIdSoal = this->checkIdMateri($data['id_soal']);
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

    public function insert($data)
    {
       $dataInsert = array(
         'id_soal'      => $data['id_soal'],
         'content'      => $data['content'],
         'created_by'   => $data['created_by'],
         'date_created' => date("Y-m-d H:i:s")
       );

       $checkIdSoal = this->checkIdSoal();
       if($checkIdSoal != Status::QUERY_CHECK_IDSOAL_GAGAL)
       {
         if($checkIdSoal->num_rows()>0) {
           if($this->db->insert('jawaban', $dataInsert))
           {
             return Status::QUERY_INSERT_JAWABAN_BERHASIL;
           } else {
             return Status::QUERY_INSERT_JAWABAN_GAGAL;
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
        if($validateId === Status::QUERY_CHECK_IDJAWABAN_ISI)
        {
            $dataUpdate = array(
              'id_soal'      => $data['id_soal'],
              'content'      => $data['content'],
              'created_by'   => $data['created_by'],
              'date_created' => date("Y-m-d H:i:s")
            );

           $queryUpdate = $this->db->set($dataUpdate)
                                  ->where('id',$data['id'])
                                  ->update('jawaban');

           if($queryUpdate)
           {
             return Status::QUERY_UPDATE_JAWABAN_BERHASIL;
           } else {
             return Status::QUERY_UPDATE_JAWABAN_GAGAL;
           }
       } else {
         return $validateId;
       }
    }

    public function delete($data)
    {
        $validateId = this->validateId($data);
        if($validateId === Status::QUERY_CHECK_IDJAWABAN_ISI)
        {
           $queryDelete = $this->db->where('id',$data['id'])
                                   ->delete('jawaban');

           if($queryDelete)
           {
             return Status::QUERY_DELETE_JAWABAN_BERHASIL;
           } else {
             return Status::QUERY_DELETE_JAWABAN_GAGAL;
           }
        } else {
          return $validateId;
        }
    }
}
?>
