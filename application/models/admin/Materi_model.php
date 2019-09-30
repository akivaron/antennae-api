<?php
require APPPATH . 'libraries/Status.php';

class Materi_model extends CI_Model {

    public function getListMateri()
    {
       $getList = "SELECT id, name, description, created_by, date_created FROM materi ORDER BY id DESC";
       $queryGetListMateri = $this->db->query($getList);
       return $queryGetListMateri;
    }

    public function insert($data)
    {
       $dataInsert = array(
         'name'         => $data['name'],
         'description'  => $data['description'],
         'created_by'   => $data['uname'],
         'date_created' => date("Y-m-d H:i:s")
       );

       if($this->db->insert('materi', $dataInsert))
       {
         return Status::QUERY_INSERT_MATERI_BERHASIL;
       } else {
         return Status::QUERY_INSERT_MATERI_GAGAL;
       }
    }

    public function update($data)
    {
      $getId = "SELECT id FROM materi WHERE id = ?";
      $queryGetId = $this->db->query($getId, array($data['id']));

      if($queryGetId)
      {
        if($queryGetId->num_rows()>0)
        {
          foreach($queryGetId->result() as $get)
          {
             $dataUpdate = array(
               'name' => $data['name'],
               'description' => $data['description'],
             );

             $queryUpdate = $this->db->set($dataUpdate)
                                    ->where('id',$data['id'])
                                    ->update('materi');

             if($queryUpdate)
             {
               return Status::QUERY_UPDATE_MATERI_BERHASIL;
             } else {
               return Status::QUERY_UPDATE_MATERI_GAGAL;
             }
          }
        } else {
          return Status::ID_MATERI_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_MATERI_GAGAL;
      }
    }

    public function delete($data)
    {
      $getId = "SELECT id FROM materi WHERE id = ?";
      $queryGetId = $this->db->query($getId, array($data['id']));

      if($queryGetId)
      {
        if($queryGetId->num_rows()>0)
        {
          foreach($queryGetId->result() as $get)
          {

             $queryDelete = $this->db->where('id',$data['id'])
                                     ->delete('materi');

             if($queryDelete)
             {
               return Status::QUERY_DELETE_MATERI_BERHASIL;
             } else {
               return Status::QUERY_DELETE_MATERI_GAGAL;
             }
          }
        } else {
          return Status::ID_MATERI_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_MATERI_GAGAL;
      }
    }
}
?>
