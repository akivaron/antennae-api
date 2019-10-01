<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;

require APPPATH . 'libraries/REST_Controller.php';

class Point extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model(['siswa/Point_model','Token_model']);
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
                  'id_material_sub' => $this->post('id_material_sub'),
                  'id_class' => $this->post('id_class'),
                  'point'  => $this->post('point'),
              ];

              $v = new Valitron\Validator($data);
              $v->rule('required', ['nis','id_material','id_material_sub','id_class','point'])->message("{field} wajib diisi.");
              $v->rule('numeric', 'nis')->message("{field} harus berupa nomor.");
              $v->rule('numeric', 'id_material_sub')->message("Id sub materi harus berupa nomor.");
              $v->rule('numeric', 'id_material')->message("Id materi harus berupa nomor.");
              $v->rule('numeric', 'id_class')->message("Id Kelas harus berupa nomor.");
              $v->rule('numeric', 'point')->message("Point harus berupa nomor.");

              if($v->validate())
              {
                $queryGet = $this->Point_model->get($data['nis']);
                if($queryGet)
                {
                  if($queryGet->num_rows()<=0)
                  {
                    $queryUpdate = $this->Point_model->insert($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_INSERT_POINT_BERHASIL:
                        $queryGet = $this->Point_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_POINT_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_INSERT_POINT_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_POINT_GAGAL,
                            'message' => 'Query cek update point gagal, silahkan hubungi developer kami jika melihat ini.',
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                    }
                  } else {
                    $queryUpdate = $this->Point_model->update($data);
                    switch ($queryUpdate) {
                      case Status::QUERY_UPDATE_POINT_BERHASIL:
                        $queryGet = $this->Point_model->get($data['nis']);
                        $message = array(
                          'status' => 'success',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_POINT_BERHASIL,
                            'message' => $queryGet->result(),
                          )
                        );
                        $this->set_response($message, REST_Controller::HTTP_CREATED);
                        break;

                      case Status::QUERY_UPDATE_POINT_GAGAL:
                        $message = array(
                          'status' => 'error',
                          'data' => array(
                            'code' => Status::QUERY_UPDATE_POINT_GAGAL,
                            'message' => 'Query cek update point gagal, silahkan hubungi developer kami jika melihat ini.',
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
                      'code' => Status::QUERY_GET_POINT_GAGAL,
                      'message' => 'Query get point gagal, silahkan hubungi developer kami jika melihat ini',
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
