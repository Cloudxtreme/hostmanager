<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
   function __construct() {
        parent::__construct();
        $this->load->model('userModel','',TRUE);
    }

	public function index() {
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');


        if($this->form_validation->run() == FALSE) {

            $this->load->library('appMenu');
            $menu = new appMenu;
            $this->load->library('appFooter');
            $footer = new appFooter;

            $this->load->helper('form');

            $data = array();
            $data['navigation'] = $menu->show_menu();
            $data['mainContent'] = $this->load->view('login', $data, true);
            $data['homeTitle'] = $this->config->item('app_title').' - Login';
            $data['headerTitle']  =  $this->config->item('app_title');
            $data['footer'] = $footer->show_footer();
            $this->load->view('main_template', $data);

        } else {

            //Go to private area
            if ( $_SESSION['user']['loginlevel'] < 1  )
                redirect('home', 'refresh');
            else
                redirect('home', 'refresh');

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
                $sess_array = array ( 'id' => $row->user_id,'username' => $row->user_username,'loginlevel' => $row->user_loginlevel);
            }
            $_SESSION['user'] =  $sess_array;
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Falscher Benutzername oder falsches Passwort.');
            return FALSE;
        }
    }

}

/* End of file login.php */
/* Location: ./application/controllers/login.php */