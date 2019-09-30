<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

use \Firebase\JWT\JWT;

class Profile extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['siswa/Profile_model','Token_model']);
    }

    public function detail_get()
    {
      $data = [
          'nis' => $this->get('nis')
      ];
      $v = new Valitron\Validator($data);
      $v->rule('required', ['nis'])->message("{field} wajib diisi.");
      $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

      if($v->validate())
      {
        $siswa = $this->Profile_model->getSiswaByNis($data['nis']);
        if($siswa)
        {
          if($siswa->num_rows()>0)
          {
            $message = array(
              'status' => 'success',
              'data' => array(
                'code' => Status::QUERY_GET_SISWA_BY_NAME_SUCCESS,
                'message' => $siswa->result(),
              )
            );
            $this->set_response($message, REST_Controller::HTTP_OK);
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::NIS_TIDAK_TERDAFTAR,
                'message' => 'NIS Tidak Terdaftar',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_NOT_FOUND);
          }
        } else {
          $message = array(
            'status' => 'error',
            'data' => array(
              'code' => Status::QUERY_GET_SISWA_BY_NAME_GAGAL,
              'message' => 'Detail profile gagal, silahkan hubungi developer kami',
            )
          );
          $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
    }

    public function current_get()
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
              $siswa = $this->Profile_model->getSiswaByNis($data['nis']);
              if($siswa)
              {
                if($siswa->num_rows()>0)
                {
                  $message = array(
                    'status' => 'success',
                    'data' => array(
                      'code' => Status::QUERY_GET_SISWA_BY_NAME_SUCCESS,
                      'message' => $siswa->result(),
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_OK);
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::NIS_TIDAK_TERDAFTAR,
                      'message' => 'NIS Tidak Terdaftar',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_NOT_FOUND);
                }
              } else {
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::QUERY_GET_SISWA_BY_NAME_GAGAL,
                    'message' => 'Detail profile gagal, silahkan hubungi developer kami',
                  )
                );
                $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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

    public function update_post()
    {
      $jwt = $this->input->get_request_header('Authorization');
      if($jwt)
      {
        try {
          $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
          if($decode){
            $data = [
                'nis' => $decode->nis,
                'name' => $this->post('name'),
                'class' => $this->post('class'),
                'email' => $this->post('email'),
                'password' => $this->post('password'),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $v = new Valitron\Validator($data);
            $v->rule('required', ['nis', 'name','class','email','password'])->message("{field} wajib diisi.");
            $v->rule('email', 'email')->message("Email tidak valid.");
            $v->rule('lengthMin', ['name','email'], 1)->message('{field} minimal 1 karakter');
            $v->rule('lengthMin', ['password'], 8)->message('{field} minimal 8 karakter');
            $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");

            if($v->validate() && $this->_checkAuth($data['nis']))
            {
                $insertData = $this->Profile_model->updateSiswa($data);

                switch ($insertData) {
                  case Status::QUERY_UPDATE_SISWA_GAGAL:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::QUERY_UPDATE_SISWA_GAGAL,
                        'message' => 'Profile gagal di update, silahkan hubungi developer kami',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR); // CREATED (201) being the HTTP response code
                    break;

                  case Status::QUERY_UPDATE_SISWA_BERHASIL:
                    $message = array(
                      'status' => 'success',
                      'data' => array(
                        'code' => Status::QUERY_UPDATE_SISWA_BERHASIL,
                        'message' => 'Profile berhasil di update!',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
                    break;

                  case Status::PASSWORD_SALAH:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::PASSWORD_SALAH,
                        'message' => 'Password yang anda masukan salah, silahkan coba kembali',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
                    break;

                  case Status::QUERY_CEK_PASSWORD_GAGAL:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::QUERY_CEK_PASSWORD_GAGAL,
                        'message' => 'Cek password gagal, silahkan hubungi developer kami',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR); // CREATED (201) being the HTTP response code
                    break;

                  case Status::EMAIL_SUDAH_TERDAFTAR:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::EMAIL_SUDAH_TERDAFTAR,
                        'message' => 'Email sudah terdaftar silahkan gunakan yang lain',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
                    break;

                  case Status::NIS_TIDAK_TERDAFTAR:
                    $message = array(
                      'status' => 'error',
                      'data' => array(
                        'code' => Status::NIS_TIDAK_TERDAFTAR,
                        'message' => 'Nis tidak terdaftar silahkan gunakan yang lain',
                      )
                    );
                    $this->set_response($message, REST_Controller::HTTP_FORBIDDEN); // CREATED (201) being the HTTP response code
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
