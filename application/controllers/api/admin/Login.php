<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Login extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key

        $this->load->model('admin/Login_model');
    }

    public function action_get()
    {
      $message = array(
        'message' => $this->session->userdata(),
      );
      $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // CREATED (201) being the HTTP response code
    }

    public function action_post()
    {
      if(!$this->session->userdata('uname'))
      {
        $data = [
            'uname' => $this->post('uname'),
            'password' => $this->post('password'),
        ];

        $v = new Valitron\Validator($data);
        $v->rule('required', ['uname', 'password'])->message("{field} wajib diisi.");
        $v->rule('lengthMin', ['password'], 8)->message('{field} minimal 8 karakter');
        $v->rule('alpha', 'uname')->message("Username harus berupa alphanumeric");

        if($v->validate())
        {
            $login = $this->Login_model->isExist($data['uname'],$data['password']);

            switch ($login) {
              case Status::BERHASIL_LOGIN:
                $admin = array(
                    "uname" => $data['uname']
                );

                $this->session->userdata = $admin;

                $message = array(
                  'status' => 'success',
                  'data' => array(
                    'code' => Status::BERHASIL_LOGIN,
                    'message' => 'Berhasil Login',
                  )
                );
                $this->set_response($message, REST_Controller::HTTP_CREATED);
                break;

              case Status::ADMIN_TIDAK_TERDAFTAR:
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::ADMIN_TIDAK_TERDAFTAR,
                    'message' => 'Data Admin tidak terdaftar, silahkan cek kembali Username yang anda masukan',
                  )
                );
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                break;

              case Status::UNAME_ATAU_PASSWORD_SALAH:
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::UNAME_ATAU_PASSWORD_SALAH,
                    'message' => 'Username atau password yang anda masukan salah, silahkan gunakan yang lain.',
                  )
                );
                $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
                break;

              case Status::QUERY_CEK_PASSWORD_GAGAL:
                $message = array(
                  'status' => 'error',
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
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::ERROR_VALIDASI,
              'message' => $v->errors(),
            )
          );
          $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST); // CREATED (201) being the HTTP response code
        }
      } else {
        $message = array(
          'status' => 'error',
          'data' => array(
            'code' => Status::SUDAH_LOGIN,
            'message' => 'Anda sudah login',
          )
        );
        $this->set_response($message, REST_Controller::HTTP_FORBIDDEN);
      }
    }
}
