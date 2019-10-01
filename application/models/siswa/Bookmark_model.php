<?php
require APPPATH . 'libraries/Status.php';

class Bookmark_model extends CI_Model {

    public function get($id_student)
    {
      $get = "SELECT * FROM student_bookmarks WHERE id_student = ?";
      $query = $this->db->query($get, array($id_student));
      return $query;
    }

    public function update($data)
    {
       $dataUpdate = array(
         'id_material'  => $data['id_material'],
         'id_material_sub'  => $data['id_material_sub'],
         'updated_at' => date("Y-m-d H:i:s")
       );

       $this->db->where('id_student', $data['nis']);
       if($this->db->update('student_bookmarks', $dataUpdate))
       {
         return Status::QUERY_UPDATE_BOOKMARK_BERHASIL;
       } else {
         return Status::QUERY_UPDATE_BOOKMARK_GAGAL;
       }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_student'  => $data['nis'],
         'id_material' => $data['id_material'],
         'id_material_sub'  => $data['id_material_sub'],
         'created_at' => date("Y-m-d H:i:s"),
         'updated_at' => date("Y-m-d H:i:s")
       );

       if($this->db->insert('student_bookmarks', $dataInsert))
       {
         return Status::QUERY_INSERT_BOOKMARK_BERHASIL;
       } else {
         return Status::QUERY_INSERT_BOOKMARK_GAGAL;
       }
    }
}
?>
