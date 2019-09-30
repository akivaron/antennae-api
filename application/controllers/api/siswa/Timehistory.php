<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;

require APPPATH . 'libraries/REST_Controller.php';

class Timehistory extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['siswa/TimeHistory_model','Token_model']);
    }

    public function list_get()
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
              $queryGet = $this->TimeHistory_model->get($data['nis']);
              if($queryGet)
              {
                if($queryGet->num_rows()>0)
                {
                  $message = array(
                    'status' => 'success',
                    'data' => array(
                      'code' => Status::QUERY_GET_TIME_BERHASIL,
                      'message' => $queryGet->result(),
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_CREATED);
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::TIME_HISTORY_KOSONG,
                      'message' => 'Data time history tidak ada',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_OK);
                }
              } else {
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::QUERY_GET_TIME_GAGAL,
                    'message' => 'Query get time history gagal, silahkan hubungi developer kami jika melihat ini',
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

    public function update_post(){
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
              $data = [
                  'nis' => $decode->nis,
                  'data' => $this->post('data')
              ];

              $v = new Valitron\Validator($data);
              $v->rule('required', ['nis','data'])->message("{field} wajib diisi.");
              $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

              if($v->validate())
              {
                $queryGet = $this->TimeHistory_model->get($data['nis']);
                if($queryGet)
                {
                  if($queryGet->num_rows()<=0)
                  {
                    $queryUpdate = $this->TimeHistory_model->insert($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_INSERT_TIME_BERHASIL:
                        $queryGet = $this->TimeHistory_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_TIME_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_INSERT_TIME_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_TIME_GAGAL,
                            'message' => 'Query cek update time gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  } else {
                    $queryUpdate = $this->TimeHistory_model->update($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_UPDATE_TIME_BERHASIL:
                        $queryGet = $this->TimeHistory_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_TIME_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_UPDATE_TIME_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_TIME_GAGAL,
                            'message' => 'Query cek update time gagal, silahkan hubungi developer kami jika melihat ini.',
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
                      'code' => Status::QUERY_GET_TIME_GAGAL,
                      'message' => 'Query get time gagal, silahkan hubungi developer kami jika melihat ini',
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
