<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/Status.php';

require APPPATH . 'libraries/REST_Controller.php';

class Jawaban extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('public/Jawaban_model');
    }

    public function pilgan_post()
    {
      $data = [
          'id_question' => $this->post('id_question'),
          'id_answer' => $this->post('id_answer'),
      ];

      $v = new Valitron\Validator($data);
      $v->rule('required', ['id_question','id_answer'])->message("{field} wajib diisi.");
      $v->rule('numeric', ['id_question','id_answer'])->message("{field} harus berupa nomor.");

      if($v->validate())
      {
        $queryCheckJawaban = $this->Jawaban_model->checkJawabanPilgan($data['id_question'], $data['id_answer']);
        if($queryCheckJawaban)
        {
          if($queryCheckJawaban->num_rows()>0)
          {
            $message = array(
              'status' => 'success',
              'data' => array(
                'code' => Status::QUERY_CHECK_JAWABAN_BERHASIL,
                'message' => 'Jawaban anda benar',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_CREATED);
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::JAWABAN_KOSONG,
                'message' => 'Jawaban yang anda masukan salah',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_NOT_FOUND);
          }
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::QUERY_CHECK_JAWABAN_GAGAL,
              'message' => 'Query check jawaban gagal, silahkan hubungi developer kami jika melihat ini',
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
