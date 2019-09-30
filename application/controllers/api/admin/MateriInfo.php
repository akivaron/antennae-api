<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class MateriInfo extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/MateriInfo_model');
    }

    public function list_get()
    {
      if($this->session->userdata('uname'))
      {
          $query = $this->MateriInfo_model->getListMateri();
          if($query)
          {
            if($query->num_rows()>0)
            {
              $message = array(
                'status' => 'success',
                'data' => array(
                  'code' => Status::QUERY_GETLIST_MATERI_BERHASIL,
                  'message' => $query->result(),
                )
              );
              $this->set_response($message, REST_Controller::HTTP_CREATED);
            } else {
              $message = array(
                'status' => 'success',
                'data' => array(
                  'code' => Status::MATERI_KOSONG,
                  'message' => 'Tidak ada materi',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_CREATED);
            }
          } else {
            $message = array(
              'status' => 'error',
              'data' => array(
                'code' => Status::QUERY_GETLIST_MATERI_GAGAL,
                'message' => 'Query get list materi gagal, silahkan hubungi developer kami jika melihat ini.',
              )
            );
            $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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

    public function add_post()
    {
      if($this->session->userdata('uname'))
      {
        $data = array(
           'uname'       => $this->session->userdata('uname'),
           'name'        => $this->post('name'),
           'description' => $this->post('description')
        );

        $v = new Valitron\Validator($data);
        $v->rule('required', ['uname', 'name','description'])->message("{field} wajib diisi.");
        $v->rule('lengthMin', ['name','description'], 3)->message('{field} minimal 3 karakter.');

        if($v->validate())
        {
          $query = $this->Materi_model->insert($data);
          switch ($query) {
            case Status::QUERY_INSERT_MATERI_BERHASIL:
              $message = array(
                'status' => 'success',
                'data' => array(
                  'code' => Status::QUERY_INSERT_MATERI_BERHASIL,
                  'message' => 'Berhasil menambahkan materi',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_CREATED);
              break;

            case Status::QUERY_INSERT_MATERI_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_INSERT_MATERI_GAGAL,
                  'message' => 'Query insert materi gagal, silahkan hubungi developer kami jika melihat ini.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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

    public function update_post()
    {
      if($this->session->userdata('uname'))
      {
        $data = array(
           'id'        => $this->post('id'),
           'name'        => $this->post('name'),
           'description' => $this->post('description')
        );

        $v = new Valitron\Validator($data);
        $v->rule('required', ['id', 'name','description'])->message("{field} wajib diisi.");
        $v->rule('lengthMin', ['name','description'], 3)->message('{field} minimal 3 karakter.');
        $v->rule('numeric', 'id')->message("{field} harus berupa nomor.");

        if($v->validate())
        {
          $query = $this->Materi_model->update($data);
          switch ($query) {
            case Status::QUERY_UPDATE_MATERI_BERHASIL:
              $message = array(
                'status' => 'success',
                'data' => array(
                  'code' => Status::QUERY_UPDATE_MATERI_BERHASIL,
                  'message' => 'Berhasil merubah materi',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_CREATED);
              break;

            case Status::QUERY_UPDATE_MATERI_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_UPDATE_MATERI_GAGAL,
                  'message' => 'Query update materi gagal, silahkan hubungi developer kami jika melihat ini.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
              break;

            case Status::ID_MATERI_TIDAK_TERDAFTAR:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::ID_MATERI_TIDAK_TERDAFTAR,
                  'message' => 'ID materi tidak terdaftar, silahkan cek kembali id yang anda kirimkan.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
              break;

            case Status::QUERY_CEK_MATERI_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_CEK_MATERI_GAGAL,
                  'message' => 'Query cek materi gagal, silahkan hubungi developer kami jika melihat ini..',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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

    public function delete_post()
    {
      if($this->session->userdata('uname'))
      {
        $data = array(
           'id'        => $this->post('id')
        );

        $v = new Valitron\Validator($data);
        $v->rule('required', ['id'])->message("{field} wajib diisi.");
        $v->rule('numeric', 'id')->message("{field} harus berupa nomor.");

        if($v->validate())
        {
          $query = $this->Materi_model->delete($data);
          switch ($query) {
            case Status::QUERY_DELETE_MATERI_BERHASIL:
              $message = array(
                'status' => 'success',
                'data' => array(
                  'code' => Status::QUERY_DELETE_MATERI_BERHASIL,
                  'message' => 'Berhasil menghapus materi',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_CREATED);
              break;

            case Status::QUERY_DELETE_MATERI_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_DELETE_MATERI_GAGAL,
                  'message' => 'Query menghapus materi gagal, silahkan hubungi developer kami jika melihat ini.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
              break;

            case Status::ID_MATERI_TIDAK_TERDAFTAR:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::ID_MATERI_TIDAK_TERDAFTAR,
                  'message' => 'ID materi tidak terdaftar, silahkan cek kembali id yang anda kirimkan.',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_BAD_REQUEST);
              break;

            case Status::QUERY_CEK_MATERI_GAGAL:
              $message = array(
                'status' => 'error',
                'data' => array(
                  'code' => Status::QUERY_CEK_MATERI_GAGAL,
                  'message' => 'Query cek materi gagal, silahkan hubungi developer kami jika melihat ini..',
                )
              );
              $this->set_response($message, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
