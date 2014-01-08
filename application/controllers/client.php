<?php
 /*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */
 
if ( ! defined('BASEPATH'))
    exit('no access!');

if (  $_SESSION['user']['loginLevel'] < 3 )
    redirect('home', 'refresh');

class Client extends CI_Controller {

    // Anzahl fuer das Paging
    private $limit = 1000;
    private $status_select = array('A' => 'aktiv','I' => 'inaktiv');

    function __construct() {
        parent::__construct();
        // Load Libraries
        $this->load->library(array('table','form_validation'));
        // load model
        $this->load->model('clientModel','',TRUE);
    }

    function index($offset = 0) {

        // offset
        $data = array();
        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        // load data
        $clients = $this->clientModel->get_paged_list($this->limit, $offset)->result();

        // generate pagination
        $this->load->library('pagination');
        $config['base_url'] = site_url('cleint/index/');
        $config['total_rows'] = $this->clientModel->count_all();
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
        $this->table->set_heading('Name','URL','Status','ausw&auml;hlen','edit','l&ouml;schen');
        $i = 0 + $offset;

        foreach ($clients as $client){
            $status_output = 'aktiv';
            if ( $client->client_status == 'I')
                $status_output = 'inaktiv';
            $this->table->add_row(
                $client->client_name,
                $client->client_url,
                $status_output,
                anchor('home/client/'.$client->client_url,'hier klicken',array()),
                anchor('client/update/'.$client->client_id,'bearbeiten',array()),
                anchor('client/delete/'.$client->client_id,'l&ouml;schen',array('onclick'=>"return confirm('M&ouml;chten Sie diesen Mandaten wirklich l&ouml;schen?')"))
            );
        }

        $data['table'] = $this->table->generate();

        // load view
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

        $data['navigation'] = $menu->show_menu();
        $data['mainContent'] = $this->load->view('client/clientlist', $data, true);
        $data['homeTitle'] = 'Mandanten';
		$data['headerTitle'] = 'Mieterdatensoftware';
        $data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
    }

    function add() {

        // Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

        // set common properties
        $data['client_id'] = '';
        $data['client_name']= '';
        $data['client_url']= '';
        $data['client_status'] = form_dropdown('client_status', $this->status_select, 'A',' class="selectField"');
        $data['action'] = site_url('client/addClient');
        $data['link_back'] = anchor('client/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
        $data['footer'] = $footer->show_footer();
        $data['mainContent'] = $this->load->view('client/clientedit', $data, true);

        // Show view
        $this->load->view('main_template', $data);

    }

    function addClient(){

        // Datenvalidieren
        $this->_set_rules();

        // run validation
        if ($this->form_validation->run() == FALSE){
            $data['message'] = '';
        } else {

            // save data
            $client = array(
                'client_name' => $this->input->post('client_name'),
                'client_url' => $this->input->post('client_url'),
                'client_mail' => $this->input->post('client_mail'),
                'client_status' => $this->input->post('client_status'));
            $client_id = $this->clientModel->save($client);
            $this->form_validation->client_id = $client_id;
            redirect('client', 'refresh');
        }

        // Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

        // set common properties
        $data['client_id'] = $this->input->post('client_id');
        $data['client_name'] = $this->input->post('client_name');
        $data['client_url'] = $this->input->post('client_url');
        $data['client_mail'] = $this->input->post('client_mail');
        $data['client_status'] = form_dropdown('client_status', $this->status_select, $this->input->post('client_status'),' class="selectField"');

        $data['title'] = 'Mandanten anpassen';
        $data['homeTitle'] = 'Vermietungstool - Mandanten anpassen';
        $data['headerTitle']  =  'Mieterdatensoftware';
        $data['action'] = site_url('client/updateClient');
        $data['link_back'] = anchor('client/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
        $data['footer'] = $footer->show_footer();
        $data['mainContent'] = $this->load->view('client/clientedit', $data, true);

        // Show view
        $this->load->view('main_template', $data);
    }

    function update($client_id){

        // Load Clientdate
        $client = $this->clientModel->get_by_id($client_id)->row();

        // Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

        // set common properties
        $data['client_id'] = $client->client_id;
        $data['client_name'] = $client->client_name;
        $data['client_url'] = $client->client_url;
        $data['client_mail'] = $client->client_mail;
        $data['client_status'] = form_dropdown('client_status', $this->status_select, $client->client_status,' class="selectField"');

        $data['title'] = 'Mandanten anpassen';
        $data['homeTitle'] = 'Vermietungstool - Mandaten anpassen';
        $data['headerTitle']  =  'Mieterdatensoftware';
        $data['action'] = site_url('client/updateClient');
        $data['link_back'] = anchor('client/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
        $data['mainContent'] = $this->load->view('client/clientedit', $data, true);
        $data['footer'] = $footer->show_footer();
        // Show view
        $this->load->view('main_template', $data);

    }

    function updateClient(){

        // set validation properties
        $this->_set_rules();

        // run validation
        if ($this->form_validation->run() == FALSE){
            $data['message'] = '';
        } else {
            // save data
            $data['client_name'] = $this->input->post('client_name');
            $data['client_url'] = $this->input->post('client_url');
            $data['client_mail'] = $this->input->post('client_mail');
            $data['client_status'] = $this->input->post('client_status');
            $this->clientModel->update( $this->input->post('client_id'),$data);
            redirect('client', 'refresh');
        }

        // Load Libraries
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

        $data['client_id'] = $this->input->post('client_id');
        $data['client_name'] = $this->input->post('client_name');
        $data['client_url'] = $this->input->post('client_url');
        $data['client_mail'] = $this->input->post('client_mail');
        $data['client_status'] = form_dropdown('client_status', $this->status_select, $this->input->post('client_status'),' class="selectField"');
        $data['title'] = 'Clients anpassen';
        $data['homeTitle'] = 'Vermietungstool - Mandanten anpassen';
				$data['headerTitle']  =  'Mieterdatensoftware';
        $data['action'] = site_url('client/updateClient');
        $data['link_back'] = anchor('client/index/','zur&uuml;ck zur &Uuml;bersicht',array('class'=>'back'));
        $data['navigation'] = $menu->show_menu();
        $data['footer'] = $footer->show_footer();
        $data['mainContent'] = $this->load->view('client/clientedit', $data, true);

        // Show view
        $this->load->view('main_template', $data);
    }

    function delete($client_id){

        // delete Client
        $this->clientModel->delete($client_id);

        // ToDO
        // Delete all Entries in Subtables

        // redirect to person list page
        redirect('client/index/','refresh');
    }

    // validation rules
    function _set_rules(){
        $this->form_validation->set_rules('client_name', 'Mandant', 'trim|required');
        $this->form_validation->set_rules('client_url', 'URL', 'trim|required');
        $this->form_validation->set_rules('client_mail', 'Email', 'trim|required');
    }

}
?>
