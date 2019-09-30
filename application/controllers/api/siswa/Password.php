<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
use \Firebase\JWT\JWT;
require APPPATH . 'libraries/REST_Controller.php';

class Password extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

        $this->load->model(['siswa/Password_model'],['Token_model']);
    }

    public function action_post()
    {
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
              $data = array(
                 'nis'      => $decode->nis,
                 'password_baru' => $this->post('password_baru'),
                 'password_lama' => $this->post('password_lama')
              );

              $v = new Valitron\Validator($data);
              $v->rule('required', ['nis', 'password_baru','password_lama'])->message("{field} wajib diisi.");
              $v->rule('lengthMin', ['password_baru','password_lama'], 8)->message('{field} minimal 8 karakter.');
              $v->rule('different', 'password_baru', 'password_lama')->message('Password yang baru tidak boleh sama dengan password yang lama.');
              $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

              if($v->validate())
              {
                $query = $this->Password_model->updatePassByNis($data);
                switch ($query) {
                  case Status::QUERY_UPDATE_PASSWORD_BERHASIL:
                    $message = array(
                      'status' => 'success',
                      'data' => array(
                        'code' => Status::QUERY_UPDATE_PASSWORD_BERHASIL,
                        'message' => 'Berhasil mengganti password',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_CREATED);
                    break;

                  case Status::QUERY_UPDATE_PASSWORD_GAGAL:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::QUERY_UPDATE_PASSWORD_GAGAL,
                        'message' => 'Query update password gagal, silahkan hubungi developer kami jika melihat ini.',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    break;

                  case Status::QUERY_CEK_PASSWORD_GAGAL:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::QUERY_CEK_PASSWORD_GAGAL,
                        'message' => 'Query cek update password gagal, silahkan hubungi developer kami jika melihat ini.',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                    break;

                  case Status::SISWA_TIDAK_TERDAFTAR:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::SISWA_TIDAK_TERDAFTAR,
                        'message' => 'Siswa tidak terdaftar, silahkan gunakan NIS yang lain.',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_OK);
                    break;

                  case Status::NIS_ATAU_PASSWORD_SALAH:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::NIS_ATAU_PASSWORD_SALAH,
                        'message' => 'Nis atau Password lama yang dimasukan salah, silahkan coba kembali.',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_OK);
                    break;
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
            'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
            'message' => 'Token auth tidak valid atau expired, Anda harus login terlebih dahulu sebelum mengakses halaman ini',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
      }
    }

    public function _checkAuth($nis){
      $checkToken = $this->Token_model->checkToken($nis);
      switch ($checkToken) {
        case Status::AUTH_HEADER_KOSONG:
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
              'message' => 'Anda harus login terlebih dahulu sebelum mengakses halaman ini',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
          break;
        case Status::INVALID_TOKEN:
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::INVALID_TOKEN,
              'message' => 'Token Auth Invalid',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
          break;
        case Status::TOKEN_VALID:
          return true;
        case Status::QUERY_CHECK_TOKEN_KOSONG:
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::QUERY_CHECK_TOKEN_KOSONG,
              'message' => 'Data siswa dengan token tersebut tidak ada.',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_OK);
          break;
        case Status::QUERY_CHECK_TOKEN_GAGAL:
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::QUERY_CHECK_TOKEN_GAGAL,
              'message' => 'Query check token gagal, silahkan hubungi developer kami, jika melihat ini.',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
          break;
      }
    }
}
