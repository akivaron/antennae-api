<?php
require APPPATH . 'libraries/Status.php';

class Clue_model extends CI_Model {

    public function getListClue()
    {
       $getList = "SELECT id, english, bahasa FROM clue ORDER BY id DESC";
       $queryGetListClue = $this->db->query($getList);
       return $queryGetListClue;
    }

    public function insert($data)
    {
       $dataInsert = array(
         'english'      => $data['english'],
         'bahasa'       => $data['bahasa']
       );

       if($this->db->insert('clue', $dataInsert))
       {
         return Status::QUERY_INSERT_CLUE_BERHASIL;
       } else {
         return Status::QUERY_INSERT_CLUE_GAGAL;
       }
    }

    public function update($data)
    {
      $getId = "SELECT id FROM clue WHERE id = ?";
      $queryGetId = $this->db->query($getId, array($data['id']));

      if($queryGetId)
      {
        if($queryGetId->num_rows()>0)
        {
          $dataUpdate = array(
            'english' => $data['english'],
            'bahasa'  => $data['bahasa'],
          );

          $queryUpdate = $this->db->set($dataUpdate)
                                 ->where('id',$data['id'])
                                 ->update('clue');

          if($queryUpdate)
          {
            return Status::QUERY_UPDATE_CLUE_BERHASIL;
          } else {
            return Status::QUERY_UPDATE_CLUE_GAGAL;
          }
        } else {
          return Status::ID_CLUE_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_CLUE_GAGAL;
      }
    }

    public function delete($data)
    {
      $getId = "SELECT id FROM clue WHERE id = ?";
      $queryGetId = $this->db->query($getId, array($data['id']));

      if($queryGetId)
      {
        if($queryGetId->num_rows()>0)
        {
          $queryDelete = $this->db->where('id',$data['id'])
                                  ->delete('clue');

          if($queryDelete)
          {
            return Status::QUERY_DELETE_CLUE_BERHASIL;
          } else {
            return Status::QUERY_DELETE_CLUE_GAGAL;
          }
        } else {
          return Status::ID_CLUE_TIDAK_TERDAFTAR;
        }
      } else {
        return Status::QUERY_CEK_CLUE_GAGAL;
      }
    }
}
?>
