<?php
require APPPATH . 'libraries/Status.php';

class Bookmark_model extends CI_Model {

    public function checkIdMateri($id) {
      $getList = "SELECT id FROM materi where id = ?";
      $checkIdMateri = $this->db->query($getList, array($id));
      if($checkIdMateri) {
        return $checkIdMateri;
      } else {
        return Status::QUERY_CHECK_IDMATERI_GAGAL;
      }
    }

    public function checkIdBookmark($id)
    {
      $getList = "SELECT id FROM bookmark_siswa where id = ?";
      $checkIdBookmark = $this->db->query($getList, array($id));
      if($checkIdBookmark) {
        return $checkIdBookmark;
      } else {
        return Status::QUERY_CHECK_IDBOOKMARK_GAGAL;
      }
    }

    public function validateId($data)
    {
      $checkIdMateri = this->checkIdMateri($data['id_materi']);
      if($checkIdMateri != Status::QUERY_CHECK_IDMATERI_GAGAL)
      {
        if($checkIdMateri->num_rows()>0)
        {
          $checkIdBookmark = this->checkIdBookmark($data['id']);
          if($checkIdBookmark != Status::QUERY_CHECK_IDBOOKMARK_GAGAL)
          {
            if($checkIdBookmark->num_rows()>0)
            {
              return Status::QUERY_CHECK_IDBOOKMARK_ISI;
            } else {
              return Status::QUERY_CHECK_IDBOOKMARK_KOSONG;
            }
          } else {
            return Status::QUERY_CHECK_IDBOOKMARK_GAGAL;
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
         'id_materi'    => $data['id_materi'],
         'created_by'   => $data['created_by'],
         'date_created' => date("Y-m-d H:i:s")
       );

       $checkIdMateri = this->checkIdMateri();
       if($checkIdMateri != Status::QUERY_CHECK_IDMATERI_GAGAL)
       {
         if($checkIdMateri->num_rows()>0) {
           if($this->db->insert('bookmark_siswa', $dataInsert))
           {
             return Status::QUERY_INSERT_BOOKMARK_BERHASIL;
           } else {
             return Status::QUERY_INSERT_BOOKMARK_GAGAL;
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
        if($validateId === Status::QUERY_CHECK_IDBOOKMARK_ISI)
        {
            $dataUpdate = array(
              'id_materi'         => $data['id_materi'],
            );

           $queryUpdate = $this->db->set($dataUpdate)
                                  ->where('id_student',$data['nis'])
                                  ->update('student_bookmarks');

           if($queryUpdate)
           {
             return Status::QUERY_UPDATE_BOOKMARK_BERHASIL;
           } else {
             return Status::QUERY_UPDATE_BOOKMARK_GAGAL;
           }
       } else {
         return $validateId;
       }
    }

    public function delete($data)
    {
        $validateId = this->validateId($data);
        if($validateId === Status::QUERY_CHECK_IDBOOKMARK_ISI)
        {
           $queryDelete = $this->db->where('id',$data['id'])
                                   ->delete('student_bookmarks');

           if($queryDelete)
           {
             return Status::QUERY_DELETE_BOOKMARK_BERHASIL;
           } else {
             return Status::QUERY_DELETE_BOOKMARK_GAGAL;
           }
        } else {
          return $validateId;
        }
    }
}
?>
