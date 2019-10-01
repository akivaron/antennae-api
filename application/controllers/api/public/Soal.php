<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Soal extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['public/Soal_model','public/Jawaban_model']);
    }

    public function list_post()
    {
      $data = [
          'id_material_subs' => $this->post('id_material_subs'),
      ];

      $v = new Valitron\Validator($data);
      $v->rule('required', ['id_material_subs'])->message("{field} wajib diisi.");
      $v->rule('numeric', 'id_material_subs')->message("{field} harus berupa nomor.");

      if($v->validate())
      {
        $queryGetSoal = $this->Soal_model->getSoal($data['id_material_subs']);
        if($queryGetSoal)
        {
          if($queryGetSoal->num_rows()>0)
          {
            $listAnswer = [];
            $ListQuestion = [];
            foreach ($queryGetSoal->result() as $key) {
              $queryGetKeyMC  = $this->Jawaban_model->getAnswerKeyMultipleChoices($key->id);
              $queryGetAnswer = $this->Jawaban_model->getAnswer($key->id);
              if($queryGetAnswer->num_rows()>0){
                $listAnswer[] = $queryGetAnswer->result();
              } else {
                $listAnswer[] = [];
              }

              if($queryGetKeyMC->num_rows()>0){
                foreach ($queryGetKeyMC->result() as $keyMC) {
                  $ListQuestion[] = array(
                      'id' => $key->id,
                      'id_material_subs' => $key->id_material_subs,
                      'id_question_category' => $key->id_question_category,
                      'name' => $key->name,
                      'stdin' => $key->stdin,
                      'expected_output' => $key->expected_output,
                      'content' => $key->content,
                      'clue_essay' => $key->clue_essay,
                      'created_at' => $key->created_at,
                      'updated_at' => $key->updated_at,
                      'id_answer' => $keyMC->id_answer
                  );
                }
              } else {
                $ListQuestion[] = array(
                    'id' => $key->id,
                    'id_material_subs' => $key->id_material_subs,
                    'id_question_category' => $key->id_question_category,
                    'name' => $key->name,
                    'stdin' => $key->stdin,
                    'expected_output' => $key->expected_output,
                    'content' => $key->content,
                    'clue_essay' => $key->clue_essay,
                    'created_at' => $key->created_at,
                    'updated_at' => $key->updated_at,
                    'id_answer' => false
                );
              }

            }


            $message = array(
              'status' => 'success',
              'data' => array(
                'code' => Status::QUERY_GET_SOAL_BERHASIL,
                'message' => array(
                  'questions' => $ListQuestion,
                  'multiple_choices' => $listAnswer
                )
              )
            );
            $this->set_response($message, REST_Controller::HTTP_CREATED);
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::SOAL_KOSONG,
                'message' => 'Data soal tidak ada',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_NOT_FOUND);
          }
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::QUERY_GET_SOAL_GAGAL,
              'message' => 'Query get soal gagal, silahkan hubungi developer kami jika melihat ini',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::ERROR_VALIDASI,
            'message' => $v->errors(),
          )
        );
        $this->set_response($message, REST_Controller::HTTP_OK);
      }
    }
}
