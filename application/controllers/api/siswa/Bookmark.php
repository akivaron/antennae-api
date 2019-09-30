<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;

require APPPATH . 'libraries/REST_Controller.php';

class Bookmark extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['siswa/Bookmark_model','Token_model']);
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
                  'id_material' => $this->post('id_material'),
                  'id_material_sub' => $this->post('id_material_sub')
              ];

              $v = new Valitron\Validator($data);
              $v->rule('required', ['nis','id_material','id_material_sub'])->message("{field} wajib diisi.");
              $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");
              $v->rule('numeric', 'id_material_sub')->message("Id sub materi harus berupa nomor.");
              $v->rule('numeric', 'id_material')->message("Id materi harus berupa nomor.");

              if($v->validate())
              {
                $queryGet = $this->Bookmark_model->get($data['nis']);
                if($queryGet)
                {
                  if($queryGet->num_rows()<=0)
                  {
                    $queryUpdate = $this->Bookmark_model->insert($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_INSERT_BOOKMARK_BERHASIL:
                        $queryGet = $this->Bookmark_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_BOOKMARK_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_INSERT_BOOKMARK_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_BOOKMARK_GAGAL,
                            'message' => 'Query cek update bookmark gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  } else {
                    $queryUpdate = $this->Bookmark_model->update($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_UPDATE_BOOKMARK_BERHASIL:
                        $queryGet = $this->Bookmark_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_BOOKMARK_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_UPDATE_BOOKMARK_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_BOOKMARK_GAGAL,
                            'message' => 'Query cek update bookmark gagal, silahkan hubungi developer kami jika melihat ini.',
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
                      'code' => Status::QUERY_GET_BOOKMARK_GAGAL,
                      'message' => 'Query get bookmark gagal, silahkan hubungi developer kami jika melihat ini',
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
              $queryGet = $this->Bookmark_model->get($data['nis']);
              if($queryGet)
              {
                if($queryGet->num_rows()>0)
                {
                  $message = array(
                    'status' => 'success',
                    'data' => array(
                      'code' => Status::QUERY_GET_BOOKMARK_BERHASIL,
                      'message' => $queryGet->result(),
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_CREATED);
                } else {
                  $message = array(
                    'status' => 'error',
                    'data' => array(
                      'code' => Status::BOOKMARK_KOSONG,
                      'message' => 'Data bookmark tidak ada',
                    )
                  );
                  $this->set_response($message, REST_Controller::HTTP_OK);
                }
              } else {
                $message = array(
                  'status' => 'error',
                  'data' => array(
                    'code' => Status::QUERY_GET_BOOKMARK_GAGAL,
                    'message' => 'Query get bookmark gagal, silahkan hubungi developer kami jika melihat ini',
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
          $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
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
