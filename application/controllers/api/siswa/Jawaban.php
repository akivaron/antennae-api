<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;
require APPPATH . 'libraries/Status.php';
require APPPATH . 'libraries/REST_Controller.php';

class Jawaban extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['siswa/JawabanSiswaEssay_model','siswa/JawabanSiswaMultipleChoices_model']);
    }

    public function listEssay_get()
    {
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
            $data = [
                'nis' => $decode->nis
            ];

            $v = new Valitron\Validator($data);
            $v->rule('required', ['nis'])->message("{field} wajib diisi.");
            $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

            if($v->validate())
            {
              $queryGet = $this->JawabanSiswaEssay_model->get($data['nis']);
              if($queryGet)
              {
                if($queryGet->num_rows()>0)
                {
                  $message = array(
                    'status' => 'success',
                    'data' => array(
                      'code' => Status::QUERY_GET_JAWABANSISWAESSAY_BERHASIL,
                      'message' => $queryGet->result(),
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_CREATED);
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::JAWABANSISWAESSAY_KOSONG,
                      'message' => 'Data jawaban essay tidak ada',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_OK);
                }
              } else {
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::QUERY_GET_JAWABANSISWAESSAY_GAGAL,
                    'message' => 'Query get jawaban essay gagal, silahkan hubungi developer kami jika melihat ini',
                  )
                );
                $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
              }
            }
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
                'message' => 'Anda harus login terlebih dahulu untuk dapat mengakses ini',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
          }
        } catch (Exception $e) {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::INVALID_TOKEN,
              'message' => 'Token Auth Invalid, Silahkan login kembali',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        }
      }
    }


    public function listMultipleChoices_get()
    {
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
            $data = [
                'nis' => $decode->nis
            ];

            $v = new Valitron\Validator($data);
            $v->rule('required', ['nis'])->message("{field} wajib diisi.");
            $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

            if($v->validate())
            {
              $queryGet = $this->JawabanSiswaMultipleChoices_model->get($data['nis']);
              if($queryGet)
              {
                if($queryGet->num_rows()>0)
                {
                  $message = array(
                    'status' => 'success',
                    'data' => array(
                      'code' => Status::QUERY_GET_JAWABANSISWAPILGAN_BERHASIL,
                      'message' => $queryGet->result(),
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_CREATED);
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::JAWABANSISWAPILGAN_KOSONG,
                      'message' => 'Data jawaban pilihan ganda tidak ada',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_OK);
                }
              } else {
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::QUERY_GET_JAWABANSISWAPILGAN_GAGAL,
                    'message' => 'Query get jawaban pilihan ganda gagal, silahkan hubungi developer kami jika melihat ini',
                  )
                );
                $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
              }
            }
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
                'message' => 'Anda harus login terlebih dahulu untuk dapat mengakses ini',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
          }
        } catch (Exception $e) {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::INVALID_TOKEN,
              'message' => 'Token Auth Invalid, Silahkan login kembali',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        }
      }
    }

    public function updateEssay_post(){
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
              $data = [
                  'nis' => $decode->nis,
                  'answers'    => $this->post('answers'),
                  'created_at' => date("Y-m-d H:i:s"),
                  'updated_at' => date("Y-m-d H:i:s")
              ];

              $v = new Valitron\Validator($data);
              $v->rule('required', ['nis','answers'])->message("{field} wajib diisi.");
              $v->rule('numeric', 'nis')->message("Id Student / NIS harus berupa nomor.");

              if($v->validate())
              {
                $queryGet = $this->JawabanSiswaEssay_model->get($data['nis']);
                if($queryGet)
                {
                  if($queryGet->num_rows()<=0)
                  {
                    $queryUpdate = $this->JawabanSiswaEssay_model->insert($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_INSERT_JAWABANSISWAESSAY_BERHASIL:
                        $queryGet = $this->JawabanSiswaEssay_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAESSAY_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_INSERT_JAWABANSISWAESSAY_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAESSAY_GAGAL,
                            'message' => 'Query cek update jawaban gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  } else {
                    $queryUpdate = $this->JawabanSiswaEssay_model->update($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_UPDATE_JAWABANSISWAESSAY_BERHASIL:
                        $queryGet = $this->JawabanSiswaEssay_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAESSAY_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_UPDATE_JAWABANSISWAESSAY_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAESSAY_GAGAL,
                            'message' => 'Query cek update jawaban gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  }
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::QUERY_GET_JAWABANSISWAESSAY_GAGAL,
                      'message' => 'Query get jawaban gagal, silahkan hubungi developer kami jika melihat ini',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
              }
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
                'message' => 'Anda harus login terlebih dahulu untuk dapat mengakses ini',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
          }
        } catch (Exception $e) {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::INVALID_TOKEN,
              'message' => 'Token Auth Invalid, Silahkan login kembali',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::INVALID_TOKEN,
            'message' => 'Token Auth Invalid, Silahkan login kembali',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
      }
    }

    public function updateMultipleChoices_post(){
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
              $data = [
                  'nis' => $decode->nis,
                  'answers'    => $this->post('answers'),
                  'created_at' => date("Y-m-d H:i:s"),
                  'updated_at' => date("Y-m-d H:i:s")
              ];

              $v = new Valitron\Validator($data);
              $v->rule('required', ['nis','answers'])->message("{field} wajib diisi.");
              $v->rule('numeric', 'nis')->message("Id Student / NIS harus berupa nomor.");

              if($v->validate())
              {
                $queryGet = $this->JawabanSiswaMultipleChoices_model->get($data['nis']);
                if($queryGet)
                {
                  if($queryGet->num_rows()<=0)
                  {
                    $queryUpdate = $this->JawabanSiswaMultipleChoices_model->insert($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_INSERT_JAWABANSISWAPILGAN_BERHASIL:
                        $queryGet = $this->JawabanSiswaMultipleChoices_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAPILGAN_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_INSERT_JAWABANSISWAPILGAN_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAPILGAN_GAGAL,
                            'message' => 'Query cek update jawaban gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  } else {
                    $queryUpdate = $this->JawabanSiswaMultipleChoices_model->update($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_UPDATE_JAWABANSISWAPILGAN_BERHASIL:
                        $queryGet = $this->JawabanSiswaMultipleChoices_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAPILGAN_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_UPDATE_JAWABANSISWAPILGAN_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_JAWABANSISWAPILGAN_GAGAL,
                            'message' => 'Query cek update jawaban gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  }
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::QUERY_GET_JAWABANSISWAPILGAN_GAGAL,
                      'message' => 'Query get jawaban gagal, silahkan hubungi developer kami jika melihat ini',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
              }
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
                'message' => 'Anda harus login terlebih dahulu untuk dapat mengakses ini',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
          }
        } catch (Exception $e) {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::INVALID_TOKEN,
              'message' => 'Token Auth Invalid, Silahkan login kembali',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::INVALID_TOKEN,
            'message' => 'Token Auth Invalid, Silahkan login kembali',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
      }
    }
}
