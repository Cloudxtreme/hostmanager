<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    function Home(){
        parent::__construct();
        // load model
        $this->load->model('userModel','',TRUE);
    }


    public function index() {
        $this->load->library('appMenu');
        $menu = new appMenu;
        $this->load->library('appFooter');
        $footer = new appFooter;
        $data = array();
        $data['navigation'] = $menu->show_menu();
        $data['homeTitle'] = $this->config->item('app_title').' - Home';
		$data['headerTitle']  =  $this->config->item('app_title');
        $data['footer'] = $footer->show_footer();

        $data['mainContent'] = $this->load->view('home', $data, true);

        $this->load->view('main_template', $data);
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */