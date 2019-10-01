<?php
require APPPATH . 'libraries/Status.php';

class MateriInfo_model extends CI_Model {

    public function checkIdMateri($id) {
      $getList = "SELECT id FROM materi where id = ?";
      $checkIdMateri = $this->db->query($getList, array($id));
      if($checkIdMateri) {
        return $checkIdMateri;
      } else {
        return Status::QUERY_CHECK_IDMATERI_GAGAL;
      }
    }

    public function checkIdMateriInfo($id)
    {
      $getList = "SELECT id FROM materi_info where id = ?";
      $checkIdMateriInfo = $this->db->query($getList, array($id));
      if($checkIdMateriInfo) {
        return $checkIdMateriInfo;
      } else {
        return Status::QUERY_CHECK_IDMATERI_INFO_GAGAL;
      }
    }

    public function validateId($data)
    {
      $checkIdMateri = this->checkIdMateri($data['id_materi']);
      if($checkIdMateri != Status::QUERY_CHECK_IDMATERI_GAGAL)
      {
        if($checkIdMateri->num_rows()>0)
        {
          $checkIdMateriInfo = this->checkIdMateriInfo($data['id']);
          if($checkIdMateriInfo != Status::QUERY_CHECK_IDMATERI_INFO_GAGAL)
          {
            if($checkIdMateriInfo->num_rows()>0)
            {
              return Status::QUERY_CHECK_IDMATERI_INFO_ISI;
            } else {
              return Status::QUERY_CHECK_IDMATERI_INFO_KOSONG;
            }
          } else {
            return Status::QUERY_CHECK_IDMATERI_INFO_GAGAL;
          }
        } else {
          return Status::QUERY_CHECK_IDMATERI_KOSONG;
        }
      } else {
         return Status::QUERY_CHECK_IDMATERI_GAGAL;
      }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_materi'         => $data['id_materi'],
         'tujuan_pembelajaran'  => $data['tujuan_pembelajaran'],
         'kompetensi_dasar'  => $data['kompetensi_dasar'],
         'indikator'  => $data['indikator'],
         'created_by'   => $data['created_by'],
         'date_created' => date("Y-m-d H:i:s")
       );

       $checkIdMateri = this->checkIdMateri();
       if($checkIdMateri != Status::QUERY_CHECK_IDMATERI_GAGAL)
       {
         if($checkIdMateri->num_rows()>0) {
           if($this->db->insert('materi_info', $dataInsert))
           {
             return Status::QUERY_INSERT_MATERIINFO_BERHASIL;
           } else {
             return Status::QUERY_INSERT_MATERIINFO_GAGAL;
           }
         } else {
           return Status::QUERY_CHECK_IDMATERI_KOSONG;
         }
       } else {
          return Status::QUERY_CHECK_IDMATERI_GAGAL;
       }
    }

    public function update($data)
    {
        $validateId = this->validateId($data);
        if($validateId === Status::QUERY_CHECK_IDMATERI_INFO_ISI)
        {
            $dataUpdate = array(
              'id_materi'         => $data['id_materi'],
              'tujuan_pembelajaran'  => $data['tujuan_pembelajaran'],
              'kompetensi_dasar'  => $data['kompetensi_dasar'],
              'indikator'   => $data['indikator'],
            );

           $queryUpdate = $this->db->set($dataUpdate)
                                  ->where('id',$data['id'])
                                  ->update('materi_info');

           if($queryUpdate)
           {
             return Status::QUERY_UPDATE_MATERIINFO_BERHASIL;
           } else {
             return Status::QUERY_UPDATE_MATERIINFO_GAGAL;
           }
       } else {
         return $validateId;
       }
    }

    public function delete($data)
    {
        $validateId = this->validateId($data);
        if($validateId === Status::QUERY_CHECK_IDMATERI_INFO_ISI)
        {
           $queryDelete = $this->db->where('id',$data['id'])
                                   ->delete('materi_info');

           if($queryDelete)
           {
             return Status::QUERY_DELETE_MATERIINFO_BERHASIL;
           } else {
             return Status::QUERY_DELETE_MATERIINFO_GAGAL;
           }
        } else {
          return $validateId;
        }
    }
}
?>
