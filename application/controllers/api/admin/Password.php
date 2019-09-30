<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
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

        $this->load->model('admin/Password_model');
    }

    public function action_post()
    {
      if($this->session->userdata('uname'))
      {
        $data = array(
           'uname'      => $this->session->userdata('uname'),
           'password_baru' => $this->post('password_baru'),
           'password_lama' => $this->post('password_lama')
        );

        $v = new Valitron\Validator($data);
        $v->rule('required', ['uname', 'password_baru','password_lama'])->message("{field} wajib diisi.");
        $v->rule('lengthMin', ['password_baru','password_lama'], 8)->message('{field} minimal 8 karakter.');
        $v->rule('different', 'password_baru', 'password_lama')->message('Password yang baru tidak boleh sama dengan password yang lama.');

        if($v->validate())
        {
          $query = $this->Password_model->updatePassByUname($data);
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

            case Status::ADMIN_TIDAK_TERDAFTAR:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::ADMIN_TIDAK_TERDAFTAR,
                  'message' => 'Admin tidak terdaftar, silahkan gunakan Uname yang lain.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
              break;

            case Status::UNAME_ATAU_PASSWORD_SALAH:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::UNAME_ATAU_PASSWORD_SALAH,
                  'message' => 'Password lama yang dimasukan salah, silahkan coba kembali.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
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
          $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
        }

      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::HARUS_LOGIN_TERLEBIH_DAHULU,
            'message' => 'Anda harus login terlebih dahulu sebelum mengakses halaman ini',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
      }
    }
}
