<?php
if ( ! defined('BASEPATH')) exit('no access!');


if (  $_SESSION['user']['loginLevel'] < 3 )
  exit('no access!');

class User extends CI_Controller {

    // Anzahl fuer das Paging
    private $limit = 1000;
	private $typ_select = array(0 => 'Internetuser', 1 => 'Haushalt/Personendaten',2 => 'Redakteur',3 => 'Admin');

    function User(){

		parent::__construct();

		// Security
		if ( $_SESSION['user']['loginlevel'] < 3 ) {
		    redirect('home', 'refresh');
		}

		// Load Libraries
		$this->load->library(array('table','form_validation'));

        // load model
        $this->load->model('userModel','',TRUE);

    }

    function index($offset = 0){

        // offset
    	$data = array();
        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        // load data
        $users = $this->userModel->get_paged_list($this->limit, $offset)->result();

        // generate pagination
        $this->load->library('pagination');
        $config['base_url'] = site_url('user/index/');
        $config['total_rows'] = $this->userModel->count_all();
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        // generate table data
		$tmpl = array (
                    'table_open'          => '<table id="sortTable" class="tablesorter">',
                    'heading_row_start'   => '<tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<th>',
                    'heading_cell_end'    => '</th>',
                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>',
                    'table_close'         => '</table>'
        );
        $this->load->library('table');
		$this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Benutzername','E-Mail','Typ','edit','login','l&ouml;schen');
        $i = 0 + $offset;

        foreach ($users as $user){
            $user->typ = $this->typ_select[$user->loginLevel];
            $this->table->add_row(
            $user->username,
            $user->email,$user->typ,
            	anchor('user/update/'.$user->id,'bearbeiten',array()),
				anchor('autologin/?checkstring='.md5($user->username.$user->password),'login',array()),
				anchor('user/delete/'.$user->id,'l&ouml;schen',array('onclick'=>"return confirm('M&ouml;chten Sie diesen Benutzer wirklich l&ouml;schen?')")));
        }
		//echo 'checkstring='.md5('info@pan');

        $data['table'] = $this->table->generate();

        // load view
        $this->load->library('MyMenu');
        $menu = new MyMenu;
		$this->load->library('MyFooter');
		$footer = new MyFooter;

        $data['navigation'] = $menu->show_menu();
        $data['mainContent'] = $this->load->view('user/userlist', $data, true);
        $data['homeTitle'] = $this->config->item('app_title').' - Benutzer';
		$data['headerTitle']  =  $this->config->item('app_title');
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);

    }

    function add(){

			// Load Libraries
			$this->load->library('MyMenu');
			  $menu = new MyMenu;
			$this->load->library('MyFooter');
			$footer = new MyFooter;

			  // set common properties
			$data['id'] = '';
			$data['username']= '';
			$data['password'] = '';
			$data['password2'] = '';
			$data['typ'] = form_dropdown('loginLevel', $this->typ_select2, '1',' class="selectField"');
			$data['title'] = 'Benutzer anlegen';
			$data['homeTitle'] = $this->config->item('app_title').' - Benutzer anlegen';
            $data['headerTitle'] = $this->config->item('app_title');
			$data['action'] = site_url('user/add_user');
			$data['link_back'] = anchor('user/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
			$data['navigation'] = $menu->show_menu();
			$data['footer'] = $footer->show_footer();
			$data['mainContent'] = $this->load->view('user/useredit', $data, true);

			// Show view
			$this->load->view('main_template', $data);

    }

    function add_user(){

		// Datenvalidieren
        $this->_set_rules();

        // run validation
        if ($this->form_validation->run() == FALSE){
            $data['message'] = '';
        } else {
            // save data
            $user = array(  'username' => $this->input->post('username'),
                            'password' => $this->input->post('password'),
			                'loginLevel' => $this->input->post('loginLevel'));
            $id = $this->userModel->save($user);
            // set form input name="id"
            $this->form_validation->id = $id;
            redirect('user', 'refresh');
        }

				// Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
		$this->load->library('MyFooter');
		$footer = new MyFooter;

        // set common properties
        $data['id'] = $this->input->post('id');
        $data['username']= $this->input->post('username');
		$data['password'] = $this->input->post('password');
		$data['password2'] = $this->input->post('password2');
        $data['typ'] = form_dropdown('loginLevel', $this->typ_select, $this->input->post('loginLevel'),' class="selectField"');
        $data['title'] = 'Benutzerdaten anpassen';
        $data['homeTitle'] = $this->config->item('app_title').' - Benutzerdaten anpassen';
        $data['headerTitle'] = $this->config->item('app_title');
        $data['action'] = site_url('user/update_user');
        $data['link_back'] = anchor('user/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
		$data['footer'] = $footer->show_footer();
        $data['mainContent'] = $this->load->view('user/useredit', $data, true);

		// Show view
        $this->load->view('main_template', $data);

    }

    function update($id){

        // Load Userdate
        $user = $this->userModel->get_by_id($id)->row();

		// Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
 		$footer = new MyFooter;

        // set common properties
        $data['id'] = $id;
        $data['username']= $user->username;
		$data['password'] = $user->password;
		$data['password2'] = $user->password;
        $data['typ'] = form_dropdown('loginLevel', $this->typ_select2, $user->loginLevel,' class="selectField"');
        $data['title'] = 'Benutzerdaten anpassen';
        $data['homeTitle'] = $this->config->item('app_title').' - Benutzerdaten anpassen';
        $data['headerTitle'] = $this->config->item('app_title');
        $data['action'] = site_url('user/update_user');
        $data['link_back'] = anchor('user/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
        $data['mainContent'] = $this->load->view('user/useredit', $data, true);
		$data['footer'] = $footer->show_footer();
		// Show view
        $this->load->view('main_template', $data);

    }

    function update_user(){

        // set validation properties
        $this->_set_rules();

        // run validation
        if ($this->form_validation->run() == FALSE){
            $data['message'] = '';
        } else {
            // save data
            $id = $this->input->post('id');
            $user = array('user_username' => $this->input->post('username'),
                          'user_password' => $this->input->post('password'),
						  'user_loginlevel' => $this->input->post('loginLevel'));
            $this->userModel->update($id,$user);
            redirect('user', 'refresh');
        }

		// Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;


        // set common properties
        $data['id'] = $this->input->post('id');
        $data['username']= $this->input->post('username');
		$data['password'] = $this->input->post('password');
		$data['password2'] = $this->input->post('password2');
        $data['typ'] = form_dropdown('loginLevel', $this->typ_select, $this->input->post('loginLevel'),' class="selectField"');
        $data['title'] = 'Benutzerdaten anpassen';
        $data['homeTitle'] = $this->config->item('app_title').' - Benutzerdaten anpassen';
        $data['headerTitle'] = $this->config->item('app_title');
        $data['action'] = site_url('user/update_user');
        $data['link_back'] = anchor('user/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
		$data['footer'] = $footer->show_footer();
        $data['mainContent'] = $this->load->view('user/useredit', $data, true);

		// Show view
        $this->load->view('main_template', $data);
    }

    function delete($id){

		// delete request, rating, assignment adult, child
		$request = $this->requestModel->checkRequestByUser($id)->row(0);
		$this->requestModel->delete($request->requestid);

		// delete user
        $this->userModel->delete($id);

        // redirect to person list page
        redirect('user/index/','refresh');
    }

    // validation rules
    function _set_rules(){

		$this->form_validation->set_rules('username', 'Benutzername', 'trim|required');
		$this->form_validation->set_rules('password', 'Passwort',  'trim|required|matches[password2]');
		$this->form_validation->set_rules('password2', 'Passwort Wiederholung', 'trim|required');

    }

}
?>
