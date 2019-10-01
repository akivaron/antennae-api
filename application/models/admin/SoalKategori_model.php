<?php
require APPPATH . 'libraries/Status.php';

class SoalKategori_model extends CI_Model {

    public function getListClue()
    {
       $getList = "SELECT id, name, description FROM soal_kategori ORDER BY id DESC";
       $queryGetList = $this->db->query($getList);
       return $queryGetList;
    }

    public function insert($data)
    {
       $dataInsert = array(
         'name'         => $data['name'],
         'description' => $data['description']
       );

       if($this->db->insert('soal_kategori', $dataInsert))
       {
         return Status::QUERY_INSERT_SOALKATEGORI_BERHASIL;
       } else {
         return Status::QUERY_INSERT_SOALKATEGORI_GAGAL;
       }
    }

    public function update($data)
    {
      $getId = "SELECT id FROM soal_kategori WHERE id = ?";
      $queryGetId = $this->db->query($getId, array($data['id']));

      if($queryGetId)
      {
        if($queryGetId->num_rows()>0)
        {
          $dataUpdate = array(
            'name' => $data['name'],
            'description'  => $data['description'],
          );

          $queryUpdate = $this->db->set($dataUpdate)
                                 ->where('id',$data['id'])
                                 ->update('soal_kategori');

          if($queryUpdate)
          {
            return Status::QUERY_UPDATE_SOALKATEGORI_BERHASIL;
          } else {
            return Status::QUERY_UPDATE_SOALKATEGORI_GAGAL;
          }
        } else {
          return Status::ID_SOALKATEGORI_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_SOALKATEGORI_GAGAL;
      }
    }

    public function delete($data)
    {
      $getId = "SELECT id FROM soal_kategori WHERE id = ?";
      $queryGetId = $this->db->query($getId, array($data['id']));

      if($queryGetId)
      {
        if($queryGetId->num_rows()>0)
        {
          $queryDelete = $this->db->where('id',$data['id'])
                                  ->delete('soal_kategori');

          if($queryDelete)
          {
            return Status::QUERY_DELETE_SOALKATEGORI_BERHASIL;
          } else {
            return Status::QUERY_DELETE_SOALKATEGORI_GAGAL;
          }
        } else {
          return Status::ID_SOALKATEGORI_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_SOALKATEGORI_GAGAL;
      }
    }
}
?>
