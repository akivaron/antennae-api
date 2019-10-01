<?php
require APPPATH . 'libraries/Status.php';

class Soal_model extends CI_Model {

    public function checkIdMateriSub($id) {
      $getList = "SELECT id FROM materi_sub where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDMATERI_SUB_GAGAL;
      }
    }

    public function checkIdSoalKategori($id) {
      $getList = "SELECT id FROM soal_kategori where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDSOALKATEGORI_GAGAL;
      }
    }

    public function checkIdSoal($id) {
      $getList = "SELECT id FROM soal where id = ?";
      $checkId = $this->db->query($getList, array($id));
      if($checkId) {
        return $checkId;
      } else {
        return Status::QUERY_CHECK_IDSOAL_GAGAL;
      }
    }

    public function validateIdMateriSubNSoalKategori($data)
    {
      $checkIdMateriSub = this->checkIdMateriSub($data['id_materi_sub']);
      if($checkIdMateriSub != Status::QUERY_CHECK_IDMATERI_SUB_GAGAL)
      {
        if($checkIdMateriSub->num_rows()>0)
        {
          $checkIdSoalKategori = this->checkIdSoalKategori($data['id_soal_kategori']);
          if($checkIdSoalKategori != Status::QUERY_CHECK_IDSOALKATEGORI_GAGAL)
          {
            if($checkIdSoalKategori->num_rows()>0)
            {
              return Status::QUERY_CHECK_IDSOALKATEGORI_ISI;
            } else {
              return Status::QUERY_CHECK_IDSOALKATEGORI_KOSONG;
            }
          } else {
            return Status::QUERY_CHECK_IDSOALKATEGORI_GAGAL;
          }
        } else {
          return Status::QUERY_CHECK_IDMATERI_SUB_KOSONG;
        }
      } else {
         return Status::QUERY_CHECK_IDMATERI_SUB_GAGAL;
      }
    }

    public function validateId($data)
    {
      if($validateIdMateriSubNSoalKategori === Status::QUERY_CHECK_IDSOALKATEGORI_ISI)
      {
        $checkIdSoal = this->checkIdSoal($data['id_soal']);
        if($checkIdSoal != Status::QUERY_CHECK_IDSOAL_GAGAL)
        {
          if($checkIdSoal->num_rows()>0)
          {
            return Status::QUERY_CHECK_IDSOAL_ISI;
          } else {
            return Status::QUERY_CHECK_IDSOAL_KOSONG;
          }
        } else {
          return Status::QUERY_CHECK_IDSOAL_GAGAL;
        }
      } else {
         return $validateIdMateriSubNSoalKategori;
      }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_materi_sub'        => $data['id_materi_sub'],
         'id_soal_kategori'     => $data['id_soal_kategori'],
         'name'                 => $data['name'],
         'time'                 => $data['time'],
         'content'              => $data['content'],
         'created_by'           => $data['created_by'],
         'date_created'         => date("Y-m-d H:i:s")
       );

       $validateIdMateriSubNSoalKategori = this->validateIdMateriSubNSoalKategori($data);
       if($validateIdMateriSubNSoalKategori === Status::QUERY_CHECK_IDSOALKATEGORI_ISI)
       {
         if($this->db->insert('soal', $dataInsert))
         {
           return Status::QUERY_INSERT_SOAL_BERHASIL;
         } else {
           return Status::QUERY_INSERT_SOAL_GAGAL;
         }
       } else {
          return $validateIdMateriSubNSoalKategori;
       }
    }

    public function update($data)
    {
      $validateId = this->validateId($data);
      if($validateId === Status::QUERY_CHECK_IDSOAL_ISI)
      {
        $dataUpdate = array(
          'id_materi_sub'        => $data['id_materi_sub'],
          'id_soal_kategori'     => $data['id_soal_kategori'],
          'name'                 => $data['name'],
          'time'                 => $data['time'],
          'content'              => $data['content']
         );

        $queryUpdate = $this->db->set($dataUpdate)
                               ->where('id',$data['id'])
                               ->update('soal');

        if($queryUpdate)
        {
          return Status::QUERY_UPDATE_SOAL_BERHASIL;
        } else {
          return Status::QUERY_UPDATE_SOAL_GAGAL;
        }
      } else {
        return $validateId;
      }
    }

    public function delete($data)
    {
      $validateId = this->validateId($data);
      if($validateId === Status::QUERY_CHECK_IDSOAL_ISI)
      {
         $queryDelete = $this->db->where('id',$data['id'])
                                 ->delete('soal');

         if($queryDelete)
         {
           return Status::QUERY_DELETE_SOAL_BERHASIL;
         } else {
           return Status::QUERY_DELETE_SOAL_GAGAL;
         }
       } else {
         return $validateId;
       }
    }
}
?>
