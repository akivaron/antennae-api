<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Materi extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('public/Materi_model');
    }

    public function list_get()
    {
      $queryGetMateri = $this->Materi_model->getMateri();
      if($queryGetMateri)
      {
        if($queryGetMateri != Status::MATERI_KOSONG)
        {
          $message = array(
            'status' => 'success',
            'data' => array(
              'code' => Status::QUERY_GET_MATERI_BERHASIL,
              'message' => $queryGetMateri,
            )
          );
          $this->set_response($message, REST_Controller::HTTP_CREATED);
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::MATERI_KOSONG,
              'message' => 'Data materi tidak ada',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_OK);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::QUERY_GET_MATERI_GAGAL,
            'message' => 'Query get materi gagal, silahkan hubungi developer kami jika melihat ini',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
    }

    public function video_get()
    {
      $queryGetMateri = $this->Materi_model->getMateriVideo();
      if($queryGetMateri)
      {
        if($queryGetMateri != Status::MATERI_KOSONG)
        {
          $message = array(
            'status' => 'success',
            'data' => array(
              'code' => Status::QUERY_GET_MATERI_BERHASIL,
              'message' => $queryGetMateri->result(),
            )
          );
          $this->set_response($message, REST_Controller::HTTP_CREATED);
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::MATERI_KOSONG,
              'message' => 'Data materi tidak ada',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_OK);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::QUERY_GET_MATERI_GAGAL,
            'message' => 'Query get materi gagal, silahkan hubungi developer kami jika melihat ini',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
    }
}
