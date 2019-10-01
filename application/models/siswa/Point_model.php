<?php
require APPPATH . 'libraries/Status.php';

class Point_model extends CI_Model {

    public function get($id_student)
    {
      $get = "SELECT * FROM student_points WHERE nis = ?";
      $query = $this->db->query($get, array($id_student));
      return $query;
    }

    public function update($data)
    {
       $dataUpdate = array(
         'id_material'  => $data['id_material'],
         'nis'  => $data['nis'],
         'id_class'  => $data['id_class'],
         'point'  => $data['point'],
         'updated_at' => date("Y-m-d H:i:s")
       );

       $this->db->where('nis', $data['nis']);
       if($this->db->update('student_points', $dataUpdate))
       {
         return Status::QUERY_UPDATE_POINT_BERHASIL;
       } else {
         return Status::QUERY_UPDATE_POINT_GAGAL;
       }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_material'  => $data['id_material'],
         'nis'  => $data['nis'],
         'id_class'  => $data['id_class'],
         'point'  => $data['point'],
         'created_at' => date("Y-m-d H:i:s"),
         'updated_at' => date("Y-m-d H:i:s")
       );

       if($this->db->insert('student_points', $dataInsert))
       {
         return Status::QUERY_INSERT_POINT_BERHASIL;
       } else {
         return Status::QUERY_INSERT_POINT_GAGAL;
       }
    }
}
?>
