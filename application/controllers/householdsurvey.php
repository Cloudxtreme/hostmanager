<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class Householdsurvey extends CI_Controller {

    private $dataset = array();
	private $array_householdsurvey = array();
    private $errordata = array();
    private $tabindex = 0;
	private $send_form = 0;

    function __construct(){
        parent::__construct();
        $this->method_call =& get_instance();

        // load model
        $this->load->model('householdsurveyModel','',TRUE);
    }


    public function index() {
        //print_r($this->errordata);
        if ( $_SESSION['household']['household_id'] > 0 ) {
			if ($this->send_form != '1'){
				$this->load_db($_SESSION['household']['household_id']);
			}
            // load libary & array
            $class_householdsurvey = ucfirst($_SESSION ['client']['client_url'].'HouseholdsurveyArray');
            $path_householdsurvey = $_SESSION ['client']['client_url'].'/'.$class_householdsurvey;
            $this->load->library($path_householdsurvey);
            $householdsurvey_array = new $class_householdsurvey;
            $this->array_householdsurvey = $householdsurvey_array->get_array();

			$data = array();
			$data['dataset'] = $this->dataset;
			$data['householdsurveyArray'] = $this->array_householdsurvey;
            $data['errorMessages'] = $this->errordata;

			// TEMPLATE OUTPUT
	        // debug mandatenfähig
            $this->load->library('MyMenu');
            $menu = new MyMenu;
			$this->load->library('MyFooter');
			$footer = new MyFooter;

			$data['action'] = site_url('householdsurvey/save_request');
            $data['homeTitle'] = 'Weitere Haushaltangeben';
            $data['headerTitle']  =  $_SESSION ['client']['client_name'];
			$data['navigation']  = $menu->show_menu();

            if($_SESSION['user']['loginLevel'] == 1) {
                $data['subnavigation']  = '<ul class="navSub"><li><a href="'.base_url().'index.php/household">Haushaltstruktur</a></li><li class="selected"><a href="'.base_url().'index.php/householdsurvey">Weitere Angaben zum Haushalt</a></li><li><a href="'.base_url().'index.php/request">Wohnungsauswahl</a></li>';
            }

			$data['mainContent'] = $this->load->view('householdsurvey/householdsurvey', $data, true);
			// debug mandatenfähig
			$data['footer'] = $footer->show_footer();

			$this->load->view('main_template', $data);

		}

	}

	private function load_db($household_id) {
		$result_householdsurvey = $this->householdsurveyModel->get_by_household_id($household_id);
		if ( $result_householdsurvey->num_rows > 0 ) {
            // householdsurvey data
            $row_householdsurvey = $result_householdsurvey->row_array();
            $this->dataset = $row_householdsurvey;
			//echo "load_db";
    	}
	}

	public function save_request() {
	    unset($this->errordata);
        $this->errordata = array();

		// load libary & array
		$class_householdsurvey = ucfirst($_SESSION ['client']['client_url'].'HouseholdsurveyArray');
		$path_householdsurvey = $_SESSION ['client']['client_url'].'/'.$class_householdsurvey;
		$this->load->library($path_householdsurvey);
		$householdsurvey_array = new $class_householdsurvey;
		$this->array_householdsurvey = $householdsurvey_array->get_array();

		$householdsurvey_db_fields = array();
		$householdsurvey_db_fields = $this->get_db_fields($this->array_householdsurvey);

		//householdsurvey daten rauslesen
		$this->send_form = $this->input->post('send_form');
		foreach ( $householdsurvey_db_fields as $key => $value ) {
			$this->dataset[$key] = trim($this->input->post($key));
            // required
            if($value == '1') {
            	//if(!isset($this->dataset[$key]) || ($this->dataset[$key] == "")) {
                if(empty($this->dataset[$key]) OR $this->dataset[$key] == '0') {
                    $this->errordata[$key] = 'required';
                }
            }
		}
		//$this->dataset['householdsurvey_date'] = date('Y-m-d H:i:s');

        // check required
        if(count($this->errordata) == 0){
            $this->save();

        } else{
            $this->index();
        }
	}

    public function save() {
        // save householdsurvey data
        $this->dataset['client_id'] = $_SESSION ['client']['client_id'];
        $this->dataset['household_id'] = $_SESSION ['household']['household_id'];
        $result_householdsurvey = $this->householdsurveyModel->get_by_household_id($this->dataset['household_id']);
        if ( $result_householdsurvey->num_rows > 0 ) {
            $row_householdsurvey = $result_householdsurvey->row_array();
            $this->householdsurveyModel->update($row_householdsurvey['householdsurvey_id'],$this->dataset);
        } else {
            $this->householdsurveyModel->save($this->dataset);
        }

		redirect('householdsurvey/saved_success/','refresh');
    }


	public function saved_success() {
		// debug mandatenfähig
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

		$data = array();
		$data['navigation']  = $menu->show_menu();

        if($_SESSION['user']['loginLevel'] == 1) {
            $data['subnavigation']  = '<ul class="navSub"><li><a href="'.base_url().'index.php/household">Haushaltstruktur</a></li><li class="selected"><a href="'.base_url().'index.php/householdsurvey">Weitere Angaben zum Haushalt</a></li><li><a href="'.base_url().'index.php/request">Wohnungsauswahl</a></li>';
        }


        $data['mainContent'] = $this->load->view('householdsurvey/savedsuccess', $data, true);
        $data['homeTitle'] = 'Haushaltangaben gespeichert';
        $data['headerTitle']  =  $_SESSION ['client']['client_name'];
        // debug mandatenfähig
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
	}

    // get all db fields
	private function get_db_fields($arr, $level=0) {
	    $output = array();
		if(is_array($arr)) {
            foreach($arr as $key => $item ) {
				if(is_array($item)) {
	      	        if(array_key_exists("field",$item)) {
	      	            $output[$item['field']] = $item['required'];
                    }
                }
                if(is_array($arr[$key])) {
                    $output = array_merge($output,$this->get_db_fields($arr[$key], $level+1));
                }
            }
        }
        return $output;
	}

    // build formular
	public function get_formular($arr, $dataset, $prefix='',$level=0) {
		$output = '';
		if(is_array($arr)) {
		  foreach($arr as $key => $item ) {
				if(is_array($item)) {
			  	if($level==0 && array_key_exists("title",$item)) {
			  	    $output.= $output == '' ? '':'</ul></div></div></div>'."\n";
                    $output.= '<div class="accordion"><div class="">'."\n";
			  		$output.= '<h3 class="level0">'.$item['title'].'</h3>'."\n".'<div><ul class="personaldata">'."\n";
					}
					if($level==1 && array_key_exists("title",$item)) {
						$output.= '<h4 class="level1">'.$item['title'].'</h4>'."\n";
					}
				}
				if(is_array($item)) {
	      	if(array_key_exists("field",$item) && array_key_exists("text",$item)) {
	      		$this->tabindex++;
				$output.= $this->get_formular_field($item,$dataset,$prefix);
	        }
	      }
	      if(is_array($arr[$key])) {
	          $output.= $this->get_formular($arr[$key],$dataset,$prefix, $level+1);
	      }
	    }
	  }
		return $output;
	}


	private function get_formular_field($arr,$dataset,$prefix) {
        // link
        $link = '';
        if(array_key_exists('link',$arr)){
            $link = $arr['link'];
        }

        // tooltip
        $tooltip = '';
        if(array_key_exists('info',$arr) OR array_key_exists('link',$arr)){
            $tooltip = ' title="';
            $tooltip.= $arr['info'];
            if(array_key_exists('link',$arr)) {
                //$tooltip.= '<a href="'.$arr['link'].'" target="_blank">link</a>';
            }
            $tooltip.= '"';
        }

        // required
        $class='';
        $required = $arr['required'] == 1 ? '*':'';
        if(array_key_exists($arr['field'].$prefix,$this->errordata)) {
            $class = "required";
        }


		switch ( $arr['type'] ) {
			// number
			case 'number':
				$output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label>';
				$output.= '<span><input type="text" class="textfield number" name="'.$arr['field'].$prefix.'" id="'.$arr['field'].$prefix.'"'.$tooltip.' value="'.$dataset[$arr['field']].'" tabindex="'.$this->tabindex.'" /></span>'.$link.'</li>'."\n";
				break;
            // text
            case 'text':
                $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label>';
                $output.= '<span><input type="text" class="textfield text" name="'.$arr['field'].$prefix.'" id="'.$arr['field'].$prefix.'"'.$tooltip.' value="'.$dataset[$arr['field']].'" tabindex="'.$this->tabindex.'" /></span>'.$link.'</li>'."\n";
                break;
            // date
            case 'date':
                $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label>';
                $output.= '<span><input type="text" class="textfield datepicker" name="'.$arr['field'].$prefix.'" id="'.$arr['field'].$prefix.'"'.$tooltip.'  value="'.$dataset[$arr['field']].'" tabindex="'.$this->tabindex.'" /></span>'.$link.'</li>'."\n";
                break;
			// checkbox
			case 'checkbox':
                $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label>';
                $checked = $dataset[$arr['field']] == 1 ? 'checked="checked"': '';
				$output.= '<span class="checkbox"><input type="checkbox" class="checkbox" name="'.$arr['field'].$prefix.'" id="'.$arr['field'].$prefix.'"'.$tooltip.' value="1" '.$checked.' tabindex="'.$this->tabindex.'" /></span>'.$link.'</li>'."\n";
				break;
            // textarea
            case 'textarea':
                $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label>';
                $output.= '<span><textarea name="'.$arr['field'].$prefix.'" class="textfield" id="'.$arr['field'].$prefix.'" tabindex="'.$this->tabindex.'" tabindex="'.$this->tabindex.'"'.$tooltip.'>'.$dataset[$arr['field']].'</textarea></span>'.$link.'</li>'."\n";
                break;
            // nation_recoded
            case 'notvisible':
                //$output.= '<span><input type="hidden" name="'.$arr['field'].$prefix.'" id="'.$arr['field'].$prefix.'" value="'.$dataset[$arr['field']].'" '.$checked.' tabindex="'.$this->tabindex.'" />'."\n";
                break;

			default:
                // dropdown
                if(stripos($arr['type'],'drop') !== false && $arr['type'] != 'drop') {
                    $this->load->library('drop/'.$arr['type']);
                    $foo = new $arr['type'];
                    $this->drop[$value] = $foo->get_array();

                    $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label><span>';
                    $output.= form_dropdown($arr['field'].$prefix, $foo->get_array(),$dataset[$arr['field']],' id="'.$arr['field'].$prefix.'" class="textfield" tabindex="'.$this->tabindex.'"'.$tooltip);
                    $output.= '</span>'.$link.'</li>'."\n"; ;
                } else {
                    $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label><span>';
                    $output.= ' nicht definiert:'.$arr['type'].'</li>'."\n"; ;
                }
                break;

		}
		return $output;
	}

}



/* End of file householdsurvey.php */
/* Location: ./application/controllers/householdsurvey.php */
