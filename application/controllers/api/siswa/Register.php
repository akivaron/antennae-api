<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Register extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['siswa/Register_model','Token_model']);
    }

    public function action_post()
    {
      $data = [
          'nis' => $this->post('nis'),
          'name' => $this->post('name'),
          'class' => $this->post('class'),
          'email' => $this->post('email'),
          'password' => $this->post('password'),
          'created_at' => date("Y-m-d H:i:s")
      ];

      $v = new Valitron\Validator($data);
      $v->rule('required', ['nis', 'name','class','email','password'])->message("{field} wajib diisi.");
      $v->rule('email', 'email')->message("Email tidak valid.");
      $v->rule('lengthMin', ['name','email'], 1)->message('{field} minimal 1 karakter');
      $v->rule('lengthMin', ['password'], 8)->message('{field} minimal 8 karakter');
      $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

      if($v->validate() && $this->_checkAuth($data['nis']))
      {
          $insertData = $this->Register_model->insert($data);

          switch ($insertData) {
            case Status::QUERY_INSERT_SISWA_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_INSERT_SISWA_GAGAL,
                  'message' => 'Data siswa gagal dibuat, silahkan hubungi developer kami',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR); // CREATED (201) being the HTTP response code
              break;

            case Status::QUERY_CEK_SISWA_BY_EMAIL_N_NIS_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_CEK_SISWA_BY_EMAIL_N_NIS_GAGAL,
                  'message' => 'Query get siswa berdasarkan email dan nis gagal, silahkan hubungi developer kami jika melihat ini.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR); // CREATED (201) being the HTTP response code
              break;

            case Status::EMAIL_ATAU_NIS_SUDAH_TERDAFTAR:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::EMAIL_ATAU_NIS_SUDAH_TERDAFTAR,
                  'message' => 'Email atau NIS sudah terdaftar, silahkan gunakan yang lain.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
              break;

            case Status::QUERY_INSERT_SISWA_BERHASIL:
              $message = array(
                'status' => 'success',
                'data' => array(
                  'code' => Status::QUERY_INSERT_SISWA_BERHASIL,
                  'message' => 'Anda berhasil register, silahkan login agar bisa menikmati fitur kami.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
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
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
      }
    }

    public function _checkAuth($nis){
      $checkToken = $this->Token_model->checkToken($nis);
      switch ($checkToken) {
        case Status::AUTH_HEADER_KOSONG:
          return true;
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
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::SUDAH_LOGIN,
              'message' => 'Anda sudah login',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
          break;
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
