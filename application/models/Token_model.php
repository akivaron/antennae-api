<?php
use \Firebase\JWT\JWT;

class Token_model extends CI_Model {

    public function checkToken($nis)
    {
        $jwt = $this->input->get_request_header('Authorization');
        if($jwt)
        {
          try {
              $decode = JWT::decode($jwt,$this->config->item('encryption_key'),array('HS256'));
              if($decode->nis == $nis) {
                $getId = "SELECT id FROM students WHERE nis = ?";
                $queryId = $this->db->query($getId, array($decode->nis));
                if($queryId)
                {
                  if($queryId->num_rows()>0)
                  {
                      return Status::TOKEN_VALID;
                  } else {
                    return Status::QUERY_CHECK_TOKEN_KOSONG;
                  }
                } else {
                  return Status::QUERY_CHECK_TOKEN_GAGAL;
                }
              } else {
                return Status::INVALID_TOKEN;
              }
          } catch (Exception $e) {
              return Status::INVALID_TOKEN;
          }
        } else {
          return Status::AUTH_HEADER_KOSONG;
        }
    }
}
?>
