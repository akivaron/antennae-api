<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Kelas extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('public/Kelas_model');
    }

    public function list_get()
    {
      $queryGetKelas = $this->Kelas_model->getKelas();
      if($queryGetKelas)
      {
        if($queryGetKelas->num_rows()>0)
        {
          $message = array(
            'status' => 'success',
            'data' => array(
              'code' => Status::QUERY_GET_KELAS_BERHASIL,
              'message' => $queryGetKelas->result(),
            )
          );
          $this->set_response($message, REST_Controller::HTTP_CREATED);
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::KELAS_KOSONG,
              'message' => 'Data kelas tidak ada',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::QUERY_GET_KELAS_GAGAL,
            'message' => 'Query get kelas gagal, silahkan hubungi developer kami jika melihat ini',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
    }
}
