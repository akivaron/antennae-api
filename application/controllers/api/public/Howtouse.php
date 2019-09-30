<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Howtouse extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('public/Howtouse_model');
    }

    public function video_get()
    {
      $queryGet = $this->Howtouse_model->getHowToUseVideo();
      if($queryGet)
      {
        if($queryGet != Status::CARA_PENGGUNAAN_KOSONG)
        {
          $message = array(
            'status' => 'success',
            'data' => array(
              'code' => Status::QUERY_GET_CARA_PENGGUNAAN_BERHASIL,
              'message' => $queryGet->result(),
            )
          );
          $this->set_response($message, REST_Controller::HTTP_CREATED);
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::CARA_PENGGUNAAN_KOSONG,
              'message' => 'Data video cara penggunaan tidak ada',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_OK);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::QUERY_GET_CARA_PENGGUNAAN_GAGAL,
            'message' => 'Query get cara penggunaan gagal, silahkan hubungi developer kami jika melihat ini',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
    }
}
