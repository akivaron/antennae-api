<?php
require APPPATH . 'libraries/Status.php';

class MateriSub_model extends CI_Model {

    public function checkIdMateri($id) {
      $getList = "SELECT id FROM materi where id = ?";
      $checkIdMateri = $this->db->query($getList, array($id));
      if($checkIdMateri) {
        return $checkIdMateri;
      } else {
        return Status::QUERY_CHECK_IDMATERI_GAGAL;
      }
    }

    public function checkIdMateriSub($id)
    {
      $getList = "SELECT id FROM materi_sub where id = ?";
      $checkIdMateriSub = $this->db->query($getList, array($id));
      if($checkIdMateriSub) {
        return $checkIdMateriSub;
      } else {
        return Status::QUERY_CHECK_IDMATERI_SUB_GAGAL;
      }
    }

    public function validateId($data)
    {
      $checkIdMateri = this->checkIdMateri($data['id_materi']);
      if($checkIdMateri != Status::QUERY_CHECK_IDMATERI_GAGAL)
      {
        if($checkIdMateri->num_rows()>0)
        {
          $checkIdMateriSub = this->checkIdMateriSub($data['id']);
          if($checkIdMateriSub != Status::QUERY_CHECK_IDMATERI_SUB_GAGAL)
          {
            if($checkIdMateriSub->num_rows()>0)
            {
              return Status::QUERY_CHECK_IDMATERI_SUB_ISI;
            } else {
              return Status::QUERY_CHECK_IDMATERI_SUB_KOSONG;
            }
          } else {
            return Status::QUERY_CHECK_IDMATERI_SUB_GAGAL;
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
         'name'     => $data['name'],
         'content'  => $data['content'],
         'created_by'   => $data['created_by'],
         'date_created' => date("Y-m-d H:i:s")
       );

       $checkIdMateri = this->checkIdMateri($data['id_materi']);
       if($checkIdMateri != Status::QUERY_CHECK_IDMATERI_GAGAL)
       {
         if($checkIdMateri->num_rows()>0) {
           if($this->db->insert('materi_sub', $dataInsert))
           {
             return Status::QUERY_INSERT_MATERISUB_BERHASIL;
           } else {
             return Status::QUERY_INSERT_MATERISUB_GAGAL;
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
      if($validateId === Status::QUERY_CHECK_IDMATERI_SUB_ISI)
      {
        $dataUpdate = array(
           'id_materi' => $data['id_materi'],
           'name'      => $data['name'],
           'content'   => $data['content'],
         );

        $queryUpdate = $this->db->set($dataUpdate)
                               ->where('id',$data['id'])
                               ->update('materi_sub');

        if($queryUpdate)
        {
          return Status::QUERY_UPDATE_MATERISUB_BERHASIL;
        } else {
          return Status::QUERY_UPDATE_MATERISUB_GAGAL;
        }
      } else {
        return $validateId;
      }
    }

    public function delete($data)
    {
      $validateId = this->validateId($data);
      if($validateId === Status::QUERY_CHECK_IDMATERI_SUB_ISI)
      {
         $queryDelete = $this->db->where('id',$data['id'])
                                 ->delete('materi_sub');

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
