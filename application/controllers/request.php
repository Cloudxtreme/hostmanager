<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class Request extends CI_Controller {

	private $apartments1 = array();
	private $apartments2 = array();
    private $dataset = array();
    private $household_id = '';
    private $building_array = array();
	private $errordata = '';


    public function __construct() {
		parent::__construct();

		// Module laden
		$this->load->model('buildingModel','',TRUE);
        $this->load->model('apartmentModel','',TRUE);
        //$this->load->model('addroomModel','',TRUE);
		//$this->load->model('userModel','',TRUE);
        $this->load->model('householdModel','',TRUE);
    }


	public function index($error_msg = '') {
		$this->errordata = $error_msg;

		if ( $_SESSION['user']['loginLevel'] > 0 ) {
		    $this->household_id = $_SESSION['household']['household_id'];
		    $result_household = $this->householdModel->get_by_id($this->household_id);
            if ( $result_household->num_rows > 0 ) {
                $row_household = $result_household->row_array();
                $where = '';
                for ($i=1;$i<4;$i++) {
                    if ( $row_household['household_apartment_prio'.$i] > 0 ) {
                        if ( $where != '' )
                            $where .= ' AND ';
                        $where .= "apartment_id !='".$row_household['household_apartment_prio'.$i]."'";
                        $this->apartments1[] = $this->apartmentModel->get_by_id($row_household['household_apartment_prio'.$i])->row();
                    }
                }
            }
            if ( $where != '' )
                $where .= ' AND ';
            $where.="building_id = '302'";
            $this->apartments2 = $this->apartmentModel->get_paged_list($this->apartmentModel->count_all(),0,$where)->result();
		} else {
		    $this->check_apartment_list();
		}

        $result = $this->buildingModel->get_paged_list();
        foreach ($result->result_array() as $row) {
            $this->building_array[$row['building_object']] = $row['building_title'];
        }

		$data = array();
		$data['action'] = site_url('request/save_request');
		$data['apartments1'] = $this->apartments1;
		$data['apartments2'] = $this->apartments2;
        $data['buildingTitle'] = $this->building_array;


		$this->load->library('MyMenu');
        $menu = new MyMenu;
		$this->load->library('MyFooter');
		$footer = new MyFooter;

		$data['error_messages'] = $this->errordata;
        $data['navigation']  = $menu->show_menu();

        if($_SESSION['user']['loginLevel'] == 1) {
            $data['subnavigation']  = '<ul class="navSub"><li><a href="'.base_url().'index.php/household">Haushaltstruktur</a></li><li><a href="'.base_url().'index.php/householdsurvey">Weitere Angaben zum Haushalt</a></li><li class="selected"><a href="'.base_url().'index.php/request">Wohnungsauswahl</a></li>';
        }

        $data['mainContent'] = $this->load->view('request/request', $data, true);
        $data['homeTitle']   = 'Wohnungszuweisung';
        $data['headerTitle'] =  $_SESSION ['client']['client_name'];
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
	}

	public function save_request() {
		// Hat der Benutzer eine Wohnung ausgewaehlt?
		$this->household_id = $_SESSION['household']['household_id'];
        $this->errordata = "";
        if (!isset($_SESSION['request']['listorder1']) ) {
            $this->errordata.= '<div>Mindestens ein gew&uuml;nschtes Objekt (Wohnung) muss ausgew&auml;hlt sein.</div>';
		} else {
            if (empty($_SESSION['request']['listorder1']) )
                $this->errordata.= '<div>Mindestens ein gew&uuml;nschtes Objekt (Wohnung) muss ausgew&auml;hlt sein.</div>';
		}

		// User und AnfrageID abchecken
		if ( $_SESSION['user']['loginLevel'] > 0 && $this->household_id ) {
            if ( !$this->householdModel->check_hack($_SESSION['user']['id'],$this->household_id) ) {
                //unset($_SESSION);
				//redirect('login/','refresh');
			}
		}

		// Wenn ein Fehler existiert das Formular nochmals anzeigen
		if ( $this->errordata != '' ) {

            $this->index($this->errordata);
		} else {
            $this->save();
		}
	}

	private function save() {
	    // Wohungen reseten
        for ($i=0;$i<3;$i++)
            $this->dataset['household_apartment_prio'.($i+1)] = '';
		// Wohungen speichern
		if ( isset($_SESSION['request']['listorder1']) ) {
			for ($i=0;$i<count($_SESSION['request']['listorder1']);$i++) {
				$this->dataset['household_apartment_prio'.($i+1)] = $_SESSION['request']['listorder1'][$i];
            }
        }
        //print_r($this->dataset);
        $this->householdModel->update($this->household_id,$this->dataset);

		redirect('request/saved_success/','refresh');

	}

	public function saved_success() {

  		$this->load->library('MyMenu');
        $menu = new MyMenu;
  		$this->load->library('MyFooter');
  		$footer = new MyFooter;

		$data = array();
        $data['navigation']  = $menu->show_menu();

        if($_SESSION['user']['loginLevel'] == 1) {
            $data['subnavigation']  = '<ul class="navSub"><li><a href="'.base_url().'index.php/household">Haushaltstruktur</a></li><li><a href="'.base_url().'index.php/householdsurvey">Weitere Angaben zum Haushalt</a></li><li class="selected"><a href="'.base_url().'index.php/request">Wohnungsauswahl</a></li>';
        }

        $data['mainContent'] = $this->load->view('request/savedsuccess', $data, true);
        $data['homeTitle'] 	 = 'Wohnungszuweisung abgespeichert';
        $data['headerTitle']  =  $_SESSION ['client']['client_name'];
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
	}

	private function check_apartment_list() {

		$new_output = 1;
		if ( !isset($_SESSION['request']['listorder1']) )
		  $_SESSION['request']['listorder1'] = '';
		if ( !isset($_SESSION['request']['listorder2']) )
		  $_SESSION['request']['listorder2'] = '';

		if ( is_array($_SESSION['request']['listorder1']) )
		  if ( count($_SESSION['request']['listorder1']) )
			  $new_output = 0;
		if ( is_array($_SESSION['request']['listorder2']) )
		  if ( count($_SESSION['request']['listorder2']) )
			  $new_output = 0;


		if ( $new_output == 1 ) {
			/*
			// debug ****nur wohnungen anzeigen die noch nicht zugewiesen sind
			$query = "SELECT * FROM assignment";
			$results = $this->db->query($query)->result();
			$nlist = "'0'";
			foreach ($results as $result) {
				$nlist .= ",'".$result->apartmentid."'";
			}
			$query  = "SELECT * FROM apartments";
            $query .= " WHERE id NOT IN (".$nlist.")";
            $this->apartments2 = $this->apartmentModel->getList($query)->result();
  		    */
            $this->apartments2 = $this->apartmentModel->get_paged_list($this->apartmentModel->count_all(),0)->result();
		} else {
            $apartments1 = array();
            if ( is_array($_SESSION['request']['listorder1']) ) {
                if ( count($_SESSION['request']['listorder1']) ) {
                    foreach ( $_SESSION['request']['listorder1'] as $key => $value ) {
                        if ( $value )
                            $this->apartments1[$value] = $this->apartmentModel->get_by_id($value)->row();
				    }
				}
            }

            if ( is_array($_SESSION['request']['listorder2']) ) {
                if ( count($_SESSION['request']['listorder2']) ) {
				    foreach ( $_SESSION['request']['listorder2'] as $key => $value ) {
					   if ( $value ) {
                            $this->apartments2[$value] = $this->apartmentModel->get_by_id($value)->row();
					   }
				    }
		        }
		    }
	    }
	}

}

/* End of file newform.php */
/* Location: ./application/controllers/newform.php */