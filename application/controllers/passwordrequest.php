<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PasswordRequest extends CI_Controller {
    private $email = '';
    private $username = '';
	private $password = '';

    function __construct() {
        parent::__construct();
        $this->load->model('userModel','',TRUE);
    }

    function index() {
        $this->load->library('appMenu');
        $menu = new appMenu;
		$this->load->library('appFooter');
		$footer = new appFooter;

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
		
		$this->form_validation->set_rules('username', 'Benutzername', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|callback_check_database');

        if($this->form_validation->run() == FALSE) {
			$data = array();
	        $data['navigation'] = $menu->show_menu();
	        $data['main_content'] = $this->load->view('passwordrequest/passwordrequest', $data, true);
	        $data['home_title'] = $this->config->item('app_title').' - Passwort anfordern';
			$data['header_title']  =  $this->config->item('app_logo').$this->config->item('app_title');;
			$data['footer'] = $footer->show_footer();
	        $this->load->view('main_template', $data);
        } else {
            redirect('passwordrequest/sucess', 'refresh');			
        }
    }

    function check_database() {
        // Formular check
        $this->email = $this->input->post('email');
        $this->username = $this->input->post('username');
        $result = $this->userModel->get_by_email_username($this->email,$this->username);
        if($result) {
		    $_SESSION['passwordrequest']['username'] = $this->username;
            $_SESSION['passwordrequest']['email'] = $this->email;
			foreach($result as $row) {
				$_SESSION['passwordrequest']['password'] = $row->password;	
			}	
			return TRUE;
		} else {
        	$this->form_validation->set_message('check_database', 'Diese Emailadresse mit diesem Benutzername ist nicht bekannt.');
	    	return FALSE;			
		}
    }


    function sucess() {
        $this->email = $_SESSION['passwordrequest']['email'];
        $this->username = $_SESSION['passwordrequest']['username'];
		$this->password = $_SESSION['passwordrequest']['password'];
		
		echo 'un: '.$this->username.' pw:'.$this->password;
		
        unset($_SESSION['passwordrequest']);
        $this->send_password();

        $this->load->library('appMenu');
        $menu = new appMenu;
        $this->load->library('appFooter');
    	$footer = new appFooter;

    	$data = array();
        $data['navigation'] = $menu->show_menu();
        $data['main_content'] = $this->load->view('passwordrequest/sendsuccess', $data, true);
        $data['home_title'] = $this->config->item('app_title').' - Passwort anfordern';
    	$data['header_title']  =  $this->config->item('app_logo').$this->config->item('app_title');;
    	$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
    }

    function send_password() {
    	
		$login_url = base_url().'index.php/autologin/?checkstring='.md5($this->username.$this->password);

	  // E-Mail versenden
		$mailtext  = '';
		$mailtext  .= '---------------------------------------------------'."\n";
		$mailtext  .= 'Anforderung Ihrer Anmeldinformationen - '."\n";
		$mailtext  .= '---------------------------------------------------'."\n";
		$mailtext  .= 'Adresse: '.base_url()."\n";
		$mailtext  .= 'Benutzername: '.$this->username."\n";
		$mailtext  .= 'Passwort: '.$this->password."\n\n";
		$mailtext  .= 'Direktlogin: '.$login_url."\n\n";

		//echo nl2br($mailtext);
		mail ($this->email,'Passwordanforderung',$mailtext,'From: info@localhost.ch');
    }

}
?>
