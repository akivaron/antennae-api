<?php
require APPPATH . 'libraries/Status.php';

class TimeHistory_model extends CI_Model {

    public function get($id_student)
    {
      $get = "SELECT * FROM student_time_history WHERE id_student = ?";
      $query = $this->db->query($get, array($id_student));
      return $query;
    }

    public function update($data)
    {
       $dataUpdate = array(
         'data'  => $data['data'],
         'created_at' => date("Y-m-d H:i:s"),
         'updated_at' => date("Y-m-d H:i:s")
       );

       $this->db->where('id_student', $data['nis']);
       if($this->db->update('student_time_history', $dataUpdate))
       {
         return Status::QUERY_UPDATE_TIME_BERHASIL;
       } else {
         return Status::QUERY_UPDATE_TIME_GAGAL;
       }
    }

    public function insert($data)
    {
        $dataInsert = array(
          'id_student'  => $data['nis'],
          'data'  => $data['data'],
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
        );

       if($this->db->insert('student_time_history', $dataInsert))
       {
         return Status::QUERY_INSERT_TIME_BERHASIL;
       } else {
         return Status::QUERY_INSERT_TIME_GAGAL;
       }
    }
}
?>
