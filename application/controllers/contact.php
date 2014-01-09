<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller {

	public function index()

	{
    $this->load->library('appMenu');
    $menu = new appMenu;
		$this->load->library('appFooter');
		$footer = new appFooter;

		$data = array();
    $data['navigation'] = $menu->show_menu();
    $data['mainContent'] = $this->load->view('contact', $data, true);
    $data['homeTitle'] = 'Kontakt';
	$data['headerTitle']  =  '';
	$data['footer'] = $footer->show_footer();
    $this->load->view('main_template', $data);
	}

}

/* End of file contact.php */
/* Location: ./application/controllers/contact.php */

