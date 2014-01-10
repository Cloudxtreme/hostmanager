<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PasswordRequest extends CI_Controller {
    private $email = '';
    private $username = '';

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

		$data = array();
        $data['navigation'] = $menu->show_menu();
        $data['mainContent'] = $this->load->view('passwordrequest/passwordrequest', $data, true);
        $data['homeTitle'] = $this->config->item('app_title').' - Passwort anfordern';
		$data['headerTitle']  =  $this->config->item('app_title');
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
    }

    function checkRequest() {
        // Formular checken
        $error_msg = '';
        $this->email = $this->input->post('email');
        $this->username = $this->input->post('username');
        $result = $this->userModel->get_by_email_username($this->email,$this->username);
        if ( $result->num_rows < 1 ) {
		     $error_msg .= '<div class="alert alert-danger">Diese Emailadresse mit diesem Benutzername ist nicht bekannt.</div>';
	    }

        if( $error_msg != '' ) {
            $this->load->library('appMenu');
            $menu = new appMenu;
            $this->load->library('appFooter');
            $footer = new appFooter;

            $this->load->helper(array('form', 'url'));

            $data = array();
            $data['error_msg'] = $error_msg;
            $data['navigation'] = $menu->show_menu();
            $data['mainContent'] = $this->load->view('passwordrequest/passwordrequest', $data, true);
            $data['homeTitle'] = $this->config->item('app_title').' - Passwort anfordern';
            $data['headerTitle']  =  $this->config->item('app_title');
            $data['footer'] = $footer->show_footer();
            $this->load->view('main_template', $data);
        } else {
            //Go to sending
            $_SESSION['passwordrequest']['username'] = $this->username;
            $_SESSION['passwordrequest']['email'] = $this->email;
            redirect('passwordrequest/sucess', 'refresh');
        }
    }


    function sucess() {
        $this->email = $_SESSION['passwordrequest']['email'];
        $this->username = $_SESSION['passwordrequest']['username'];
        unset($_SESSION['passwordrequest']);
        $this->send_password();

        $this->load->library('appMenu');
        $menu = new appMenu;
        $this->load->library('appFooter');
    	$footer = new appFooter;

    	$data = array();
        $data['navigation'] = $menu->show_menu();
        $data['mainContent'] = $this->load->view('passwordrequest/sendsuccess', $data, true);
        $data['homeTitle'] = $this->config->item('app_title').' - Passwort anfordern';
    	$data['headerTitle']  =  $this->config->item('app_title');
    	$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
    }

    function send_password() {

		$result = $this->userModel->get_by_username($this->username);
        foreach($result as $row)
            $password = $row->password;

		$login_url = base_url().'index.php/autologin/?checkstring='.md5($this->username.$password);

	  // E-Mail versenden
		$mailtext  = '';
		$mailtext  .= '---------------------------------------------------'."\n";
		$mailtext  .= 'Anforderung Ihrer Anmeldinformationen - '."\n";
		$mailtext  .= '---------------------------------------------------'."\n";
		$mailtext  .= 'Adresse: '.base_url()."\n";
		$mailtext  .= 'Benutzername: '.$this->username."\n";
		$mailtext  .= 'Passwort: '.$password."\n\n";
		$mailtext  .= 'Direktlogin: '.$login_url."\n\n";

		//echo nl2br($mailtext);
		mail ($this->email,'Passwordanforderung',$mailtext,'From: info@localhost.ch');
    }

}
?>
