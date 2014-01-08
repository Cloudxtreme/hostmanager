<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class VerifyLogin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('userModel','',TRUE);
        $this->load->model('householdModel','',TRUE);
        $this->load->model('clientModel','',TRUE);
    }

    function index() {
        //This method will have the credentials validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

        if($this->form_validation->run() == FALSE) {

            $this->load->library('MyMenu');
            $menu = new MyMenu;
            $this->load->library('MyFooter');
            $footer = new MyFooter;

            $this->load->helper(array('form', 'url'));

            $data = array();
            $data['navigation'] = $menu->show_menu();
            $data['mainContent'] = $this->load->view('login', $data, true);
            $data['homeTitle'] = ' - Login';
            $data['footer'] = $footer->show_footer();
            $this->load->view('main_template', $data);

        } else {

            //Go to private area
            if ( $_SESSION['user']['loginLevel'] < 2  )
                redirect('home', 'refresh');
            else
                redirect('user', 'refresh');

        }

    }

    function check_database($password) {

        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');

        //query the database
        $result = $this->userModel->login($username, $password);

        if($result) {
            $sess_array = array();
            foreach($result as $row) {
                $sess_array = array ( 'id' => $row->id,'username' => $row->username,'loginLevel' => $row->loginLevel);
            }
            $_SESSION['user'] =  $sess_array;
            $res = $this->householdModel->get_by_user_id($sess_array['id']);
            if ( $res->num_rows > 0 ){
                $row = $res->row_array(1);
                $_SESSION['household']['household_id'] = $row['household_id'];
            }
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Falscher Benutzername oder falsches Passwort.');
            return false;
        }
    }

}
?>
