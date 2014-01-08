<?php /* * Developer : pan-x (info@pan-x.com) * All code (c)2013 pan-x all rights reserved */if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Contact extends CI_Controller {
	/**	 * Index Page for this controller.	 *	 * Maps to the following URL	 * 		http://example.com/index.php/welcome	 *	- or -	 * 		http://example.com/index.php/welcome/index	 *	- or -	 * Since this controller is set as the default controller in	 * config/routes.php, it's displayed at http://example.com/	 *	 * So any other public methods not prefixed with an underscore will	 * map to /index.php/welcome/<method_name>	 * @see http://codeigniter.com/user_guide/general/urls.html	 */
	public function index()
	{    $this->load->library('MyMenu');    $menu = new MyMenu;		$this->load->library('MyFooter');		$footer = new MyFooter;
		$data = array();    $data['navigation'] = $menu->show_menu();    $data['mainContent'] = $this->load->view('contact', $data, true);    $data['homeTitle'] = 'Kontakt';	$data['headerTitle']  =  $_SESSION ['client']['client_name'];
	$data['footer'] = $footer->show_footer();    $this->load->view('main_template', $data);	}
}
/* End of file contact.php *//* Location: ./application/controllers/contact.php */