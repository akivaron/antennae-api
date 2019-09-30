<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;
require APPPATH . 'libraries/REST_Controller.php';

class Login extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model(['siswa/Login_model','Token_model']);
    }

    public function action_post()
    {
      $data = [
          'nis' => $this->post('nis'),
          'password' => $this->post('password'),
      ];

      $v = new Valitron\Validator($data);
      $v->rule('required', ['nis', 'password'])->message("{field} wajib diisi.");
      $v->rule('lengthMin', ['password'], 8)->message('{field} minimal 8 karakter');
      $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

      if($v->validate())
      {
        $checkToken = $this->Token_model->checkToken($data['nis']);
        switch ($checkToken) {
          case Status::AUTH_HEADER_KOSONG:
            $this->_auth($data);
            break;
          case Status::INVALID_TOKEN:
            $message = array(
              'status' => 'error',
              'type' => 'account',
              'data' => array(
                'code' => Status::INVALID_TOKEN,
                'message' => 'Token Invalid.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
            break;
          case Status::TOKEN_VALID:
            $message = array(
              'status' => 'error',
              'type' => 'account',
              'data' => array(
                'code' => Status::SUDAH_LOGIN,
                'message' => 'Anda sudah login.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
            break;
          case Status::QUERY_CHECK_TOKEN_KOSONG:
            $message = array(
              'status' => 'error',
              'type' => 'account',
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
              'type' => 'account',
              'data' => array(
                'code' => Status::QUERY_CHECK_TOKEN_GAGAL,
                'message' => 'Query check token gagal, silahkan hubungi developer kami, jika melihat ini.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            break;
        }
      } else {
        $message = array(
          'status' => 'error',
          'type' => 'account',
          'currentAuthority' => 'guest',
          'data' => array(
            'code' => Status::ERROR_VALIDASI,
            'message' => $v->errors(),
          )
        );
        $this->set_response($message, REST_Controller::HTTP_OK);
      }
    }

    public function _auth($data)
    {
        $login = $this->Login_model->isExist($data['nis'],$data['password']);
        switch ($login) {
          case Status::BERHASIL_LOGIN:

            $payload['nis'] = $data['nis'];
            $payload['iat'] = time(); //waktu di buat
            $payload['exp'] = time() + 18000; //lima jam
            $output['token'] = JWT::encode($payload, $this->config->item('encryption_key'));

            $message = array(
              'status' => 'ok',
              'currentAuthority' => 'user',
              'data' => array(
                'code' => Status::BERHASIL_LOGIN,
                'message' => $output,
              )
            );
            $this->set_response($message, REST_Controller::HTTP_CREATED);
            break;

          case Status::SISWA_TIDAK_TERDAFTAR:
            $message = array(
              'status' => 'error',
              'type' => 'account',
              'data' => array(
                'code' => Status::SISWA_TIDAK_TERDAFTAR,
                'message' => 'Data siswa tidak terdaftar, silahkan cek kembali NIS.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
            break;

          case Status::NIS_ATAU_PASSWORD_SALAH:
            $message = array(
              'status' => 'error',
              'type' => 'account',
              'data' => array(
                'code' => Status::NIS_ATAU_PASSWORD_SALAH,
                'message' => 'NIS atau Password yang anda masukan salah, silahkan coba masukan yang lain.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
            break;

          case Status::QUERY_CEK_PASSWORD_GAGAL:
            $message = array(
              'status' => 'error',
              'type' => 'account',
              'data' => array(
                'code' => Status::QUERY_CEK_PASSWORD_GAGAL,
                'message' => 'Cek password gagal, silahkan hubungi developer kami jika melihat ini.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            break;
          default:

            break;
        }
    }
}
