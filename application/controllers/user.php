<?php
if ( ! defined('BASEPATH')) exit('no access!');


if (  $_SESSION['user']['loginlevel'] < 1 ) exit('no access!');

class User extends CI_Controller {

    // Anzahl fuer das Paging
    private $limit = 1000;
	  private $typ_select = array(0 => 'Internetuser', 1 => 'Redakteur', 2 => 'Admin');

   function __construct() {
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


		$tmpl = array (
		    'table_open'          => '<table class="tablesorter">',
		    'heading_row_start'   => '<thead><tr>',
		    'heading_row_end'     => '</tr></thead><tbody>',
		    'table_close'         => '</body></table>'
	    );

        $this->load->library('table');
		$tmpl = array ( 'table_open'  => '<table id="usertable" class="tablesorter zebra-striped">' );
		$this->table->set_template($tmpl); 
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Benutzername','E-Mail','Typ','','','');
        $i = 0 + $offset;

        foreach ($users as $user){
            $this->table->add_row(
            $user->username,
            $user->email,
            $user->loginlevel,
            anchor('user/update/'.$user->user_id,'<span class="glyphicon glyphicon-edit"></span>',array('data-toggle'=>"modal", 'title'=>"edit", 'data-target'=>"#myModal")),
			anchor('autologin/?checkstring='.md5($user->username.$user->password),'<span class="glyphicon glyphicon-export"></span>',array('data-toggle'=>"tooltip", 'title'=>"login")),
			anchor('user/delete/'.$user->user_id,'<span class="glyphicon glyphicon-trash"></span>',array('onclick'=>"return confirm('M&ouml;chten Sie diesen Benutzer wirklich l&ouml;schen?')",'data-toggle'=>"tooltip",'title'=>"l&ouml;schen")));
        }
/*
		$data['table'] = '<div class="pager">
		Page: <select class="gotoPage"></select>
		<img src="../addons/pager/icons/first.png" class="first" alt="First" title="First page" />
		<img src="../addons/pager/icons/prev.png" class="prev" alt="Prev" title="Previous page" />
		<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
		<img src="../addons/pager/icons/next.png" class="next" alt="Next" title="Next page" />
		<img src="../addons/pager/icons/last.png" class="last" alt="Last" title= "Last page" />
		<select class="pagesize">
			<option selected="selected" value="10">10</option>
			<option value="20">20</option>
			<option value="30">30</option>
			<option value="40">40</option>
		</select>
	  </div>';
*/		
        $data['table'].= $this->table->generate();

        // load view
        $this->load->library('appMenu');
        $menu = new appMenu;
		$this->load->library('appFooter');
		$footer = new appFooter;

        $data['navigation'] = $menu->show_menu();
        $data['main_content'] = $this->load->view('user/userlist', $data, true);
        $data['home_title'] = $this->config->item('app_title').' - Benutzer';
		$data['header_title']  =  $this->config->item('app_logo').$this->config->item('app_title');
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);

    }

    function add(){

			// Load Libraries
			$this->load->library('appMenu');
			  $menu = new appMenu;
			$this->load->library('appFooter');
			$footer = new appFooter;

			  // set common properties
			$data['id'] = '';
			$data['username']= '';
			$data['password'] = '';
			$data['password2'] = '';
			$data['typ'] = form_dropdown('loginlevel', $this->typ_select2, '1',' class="selectField"');
			$data['title'] = 'Benutzer anlegen';
			$data['home_title'] = $this->config->item('app_title').' - Benutzer anlegen';
            $data['header_title']  =  $this->config->item('app_logo').$this->config->item('app_title');
			$data['action'] = site_url('user/add_user');
			$data['link_back'] = anchor('user/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
			$data['navigation'] = $menu->show_menu();
			$data['footer'] = $footer->show_footer();
			$data['main_content'] = $this->load->view('user/useredit', $data, true);

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
			                'loginlevel' => $this->input->post('loginlevel'));
            $id = $this->userModel->save($user);
            // set form input name="id"
            $this->form_validation->id = $id;
            redirect('user', 'refresh');
        }

				// Load Libraries
        $this->load->library('appMenu');
        $menu = new appMenu;
		$this->load->library('appFooter');
		$footer = new appFooter;

        // set common properties
        $data['id'] = $this->input->post('id');
        $data['username']= $this->input->post('username');
		$data['password'] = $this->input->post('password');
		$data['password2'] = $this->input->post('password2');
        $data['typ'] = form_dropdown('loginlevel', $this->typ_select, $this->input->post('loginlevel'),' class="selectField"');
        $data['title'] = 'Benutzerdaten anpassen';
        $data['home_title'] = $this->config->item('app_title').' - Benutzerdaten anpassen';
        $data['header_title']  =  $this->config->item('app_logo').$this->config->item('app_title');
        $data['action'] = site_url('user/update_user');
        $data['link_back'] = anchor('user/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
		$data['footer'] = $footer->show_footer();
        $data['main_content'] = $this->load->view('user/useredit', $data, true);

		// Show view
        $this->load->view('main_template', $data);

    }

    function update($id){

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		$this->form_validation->set_rules('username', 'Benutzername', 'trim|required');
		$this->form_validation->set_rules('password', 'Passwort',  'trim|required|matches[password2]');
		$this->form_validation->set_rules('password2', 'Passwort Wiederholung', 'trim|required');


        if($this->form_validation->run() == FALSE) {
	        // set common properties
	        $data['id'] = $id;
	        $data['username']= $user->username;
			$data['password'] = $user->password;
			$data['password2'] = $user->password;
	        $data['typ'] = $user->loginlevel;//form_dropdown('loginlevel', $this->typ_select2, $user->loginlevel,' class="selectField"');
	        $data['title'] = 'Benutzerdaten editieren';
	        $data['action'] = site_url('user/update/'.$id);
	        $data['main_content'] = $this->load->view('user/useredit', $data, true);
			// Show view
	        $this->load->view('user_template', $data);
        } else {
        	$data['id'] = $this->input->post('id');
        	$data['username']= $this->input->post('username');
			$data['password'] = $this->input->post('password');
			$data['password2'] = $this->input->post('password2');
			$this->userModel->update($id,$user);
            redirect('user/index', 'refresh');			
        }
    }

    function delete($id){

		// delete user
        $this->userModel->delete($id);

        // redirect to person list page
        redirect('user/index/','refresh');
    }
}
?>
