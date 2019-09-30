<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Status.php';

class Logout extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function action_get()
    {
      $this->session->unset_userdata('nis');
	    $this->session->sess_destroy();
      $message = array(
        'status' => 'success',
        'data' => array(
          'code' => Status::BERHASIL_LOGOUT,
          'message' => 'Berhasil Logout',
        )
      );
      $this->set_response($message, REST_Controller::HTTP_CREATED);
    }
}
