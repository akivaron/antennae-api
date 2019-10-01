<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Status.php';

class Compiler extends REST_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function cpp_post()
    {
			$data = $this->post('code');
			$output = "";
			if ( ! write_file('assets/cpp/code/main.cpp', $data))
			{
			        echo 'Unable to write the file';
			}
			else
			{
					exec("g++ ".FCPATH."assets/cpp/code/main.cpp -o ".FCPATH."assets/cpp/code/main 2>&1",$output);
					exec("cd ".FCPATH."assets/cpp/code/ && ./main 2>&1", $output);
					// exec("./main 2>&1", $output);
					echo $output[0];
					// foreach($output as $line)
					// {
					// 	echo $line;
					// }
			}
    }
}
