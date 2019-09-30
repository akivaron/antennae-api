<?php
class Jawaban_model extends CI_Model {

    public function checkJawabanPilgan($id_question, $id_answer)
    {
      $checkJawaban = "SELECT * FROM answer_key_multiple_choices where id_question = ? and id_answer = ?";
      $queryCheckJawaban = $this->db->query($checkJawaban,array($id_question,$id_answer));
      return $queryCheckJawaban;
    }

    public function getAnswer($id_question)
    {
      $getAnswer = "SELECT * FROM answers  where id_question = ?";
      $queryGetAnswer = $this->db->query($getAnswer,array($id_question));
      return $queryGetAnswer;
    }

    public function getAnswerKeyMultipleChoices($id_question)
    {
      $checkJawaban = "SELECT id_answer FROM answer_key_multiple_choices where id_question = ?";
      $queryCheckJawaban = $this->db->query($checkJawaban,array($id_question));
      return $queryCheckJawaban;
    }
}
?>
