<?php

class JawabanSiswaEssay_model extends CI_Model {

    public function get($id_student)
    {
      $get = "SELECT * FROM answer_student_essays WHERE id_student = ?";
      $query = $this->db->query($get, array($id_student));
      return $query;
    }

    public function update($data)
    {
       $dataUpdate = array(
         'answers'  => $data['answers'],
         'updated_at' => date("Y-m-d H:i:s")
       );

       $this->db->where('id_student', $data['nis']);
       if($this->db->update('answer_student_essays', $dataUpdate))
       {
         return Status::QUERY_UPDATE_JAWABANSISWAESSAY_BERHASIL;
       } else {
         return Status::QUERY_UPDATE_JAWABANSISWAESSAY_GAGAL;
       }
    }

    public function insert($data)
    {
       $dataInsert = array(
         'id_student'  => $data['nis'],
         'answers'  => $data['answers'],
         'created_at' => date("Y-m-d H:i:s"),
         'updated_at' => date("Y-m-d H:i:s")
       );

       if($this->db->insert('answer_student_essays', $dataInsert))
       {
         return Status::QUERY_INSERT_JAWABANSISWAESSAY_BERHASIL;
       } else {
         return Status::QUERY_INSERT_JAWABANSISWAESSAY_GAGAL;
       }
    }
}
?>
