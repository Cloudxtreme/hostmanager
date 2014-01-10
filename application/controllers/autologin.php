<?php
 /*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class Autologin extends CI_Controller {

    function Autologin(){
	   parent::__construct();
	   $this->load->model('userModel','',TRUE);
    }

    function index(){
        //Field validation succeeded.  Validate against database
        $checkstring = $this->input->get('checkstring');

        //query the database
        $result_user = $this->userModel->autologin($checkstring);

        if($result_user) {
            foreach($result_user as $row_user) {
                $_SESSION['user'] =  array('loginlevel' => $row_user->loginlevel,'id' => $row_user->user_id, 'username' => $row_user->username);
            }
			redirect('home', 'refresh');
        } else
		    redirect('login', 'refresh');
		}
}
