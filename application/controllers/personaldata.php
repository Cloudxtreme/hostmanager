<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class Personaldata extends CI_Controller {

    private $dataset_adult = array();
    private $dataset_child = array();
	private $array_adult = array();
    private $array_child = array();
	private $number_child = 0;
    private $household_id = 0;
    private $errordata = array();
    private $tabindex = 0;
	private $send_form = 0;

    function __construct(){
        parent::__construct();
        $this->method_call =& get_instance();

        // load model
        $this->load->model('childModel','',TRUE);
        $this->load->model('adultModel','',TRUE);

        // household username
        $this->load->model('householdModel','',TRUE);
        $this->load->model('userModel','',TRUE);
    }


    public function index() {
        //print_r($this->errordata);
        $data = array();
        if ( $_SESSION['user']['loginLevel'] > 0 ) {
			if ($this->send_form != '1'){
				$this->load_db($_SESSION['user']['id']);
			}

			$data = array();
			$data['adultDataset'] = $this->dataset_adult;
			$data['adultArray'] = $this->array_adult;
			$data['numberChild'] = $this->number_child;
			$data['childDataset'] = $this->dataset_child;
			$data['childArray'] = $this->array_child;
            $data['householdId'] = $this->household_id;
            $data['errorMessages'] = $this->errordata;
		}

        // TEMPLATE OUTPUT
        // debug mandatenfähig
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

        $data['action'] = site_url('personaldata/save_request');
        $data['homeTitle'] = 'Personendaten';
        $data['headerTitle']  =  $_SESSION ['client']['client_name'];
        $data['navigation']  = $menu->show_menu();
        $data['mainContent'] = $this->load->view('personaldata/personaldata', $data, true);
        // debug mandatenfähig
        $data['footer'] = $footer->show_footer();

        $this->load->view('main_template', $data);
	}

	private function load_db($user_id) {
		$result_adult = $this->adultModel->get_by_user_id($user_id);
		if ( $result_adult->num_rows > 0 ) {

			// load libary & array
			$class_adult = ucfirst($_SESSION ['client']['client_url'].'AdultArray');
			$path_adult = $_SESSION ['client']['client_url'].'/'.$class_adult;
			$this->load->library($path_adult);
			$adult_array = new $class_adult;

            // adult data
            $row_adult = $result_adult->row_array();
            $this->household_id = $row_adult['household_id'];
            $this->dataset_adult = $row_adult;
            $this->array_adult = $adult_array->get_array();

            // child data
            $result_child = $this->childModel->get_by_adult_id($row_adult['adult_id']);
			$this->number_child = $result_child->num_rows;
            if ( $this->number_child > 0 ) {
                $class_child = ucfirst($_SESSION ['client']['client_url'].'ChildArray');
                $path_child = $_SESSION ['client']['client_url'].'/'.$class_child;
                $this->load->library($path_child);
                $child_array = new $class_child;
                $this->array_child = $child_array->get_array();

				$i=0;
            	foreach ($result_child->result_array() as $row_child) {
                    $this->dataset_child[$i] = $row_child;
					$i++;
                }
            }
			//echo "load_db";
    	}
	}

	public function save_request() {
	    unset($this->errordata);
        $this->errordata = array();

        $this->send_form = $this->input->post('send_form');
        $this->household_id = $this->input->post('household');
		// delete_household auslesen

		// load libary & array
		$class_adult = ucfirst($_SESSION ['client']['client_url'].'AdultArray');
		$path_adult = $_SESSION ['client']['client_url'].'/'.$class_adult;
		$this->load->library($path_adult);
		$adult_array = new $class_adult;
		$this->array_adult = $adult_array->get_array();

		$adult_db_fields = array();
		$adult_db_fields = $this->get_db_fields($this->array_adult);

		//adult daten rauslesen
		foreach ( $adult_db_fields as $key => $value ) {
			$this->dataset_adult[$key] = trim($this->input->post($key));
            // check is required
            if($value == '1') {
                // is empty and not '0'
                //if(!isset($this->dataset_adult[$key]) || ($this->dataset_adult[$key] == "")) {
                if(empty($this->dataset_adult[$key]) OR $this->dataset_adult[$key] == '0') {
                    $this->errordata[$key] = 'required';
                }
            }
		}
		//$this->dataset_adult['adult_date'] = date('Y-m-d H:i:s');
		// child
		$result_adult = $this->adultModel->get_by_user_id($_SESSION['user']['id']);
		$row_adult = $result_adult->row_array();
		$result_child = $this->childModel->get_by_adult_id($row_adult['adult_id']);
		$this->number_child = $result_child->num_rows;
		if ( $this->number_child > 0 ) {
		    $class_child = ucfirst($_SESSION ['client']['client_url'].'ChildArray');
			$path_child = $_SESSION ['client']['client_url'].'/'.$class_child;
			$this->load->library($path_child);
			$child_array = new $class_child;
			$this->array_child = $child_array->get_array();

			$child_db_fields = array();
			$child_db_fields = $this->get_db_fields($this->array_child);
			for($i=0; $i<$this->number_child; $i++) {
			    $this->dataset_child[$i] = array();
				foreach ( $child_db_fields as $key => $value ) {
				   $this->dataset_child[$i][$key] = trim($this->input->post($key.'_'.$i));
                   // required
                   if($value == '1') {
                       if(empty($this->dataset_child[$i][$key])) {
                            $this->errordata[$key.'_'.$i] = 'required';
                       }
                   }
				}
		    }
		}

        // check required
        if(empty($this->errordata)){
            $this->save();

        } else{
            $this->index();
        }
	}

    public function save() {
        // delete_household entkoppeln
        // childveknüpfung und adult aus household löschen. wenn household owner, dann nächster als owner setzten

        // save adult data
        $result_adult = $this->adultModel->get_by_user_id($_SESSION['user']['id']);
        if ( $result_adult->num_rows > 0 ) {
            $row_adult = $result_adult->row_array();
            $this->adultModel->update($row_adult['adult_id'],$this->dataset_adult);
        }

        // save child data
        $result_child = $this->childModel->get_by_adult_id($row_adult['adult_id']);
        if ( $result_child->num_rows > 0 ) {
            $i=0;
            foreach ($result_child->result_array() as $row_child) {
                $this->childModel->update($row_child['child_id'],$this->dataset_child[$i]);
				$i++;
            }
        }
		redirect('personaldata/saved_success/','refresh');
    }


	public function saved_success() {
		// debug mandatenfähig
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;

		$data = array();
		$data['navigation']  = $menu->show_menu();
        $data['mainContent'] = $this->load->view('personaldata/savedsuccess', $data, true);
        $data['homeTitle'] = 'Personendaten gespeichert';
        $data['headerTitle']  =  $_SESSION ['client']['client_name'];
        // debug mandatenfähig
		$data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
	}

    // get houshold username by houshold id
    private function get_houshold_username($household_id) {
        // debug

        return $houshold_username;
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
			// number
			case 'number_plz':
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

            // occupation
            case 'autocomplete':
                $output= '<li class="'.$class.'"><label for="'.$arr['field'].$prefix.'">'.$arr['text'].$required.'</label>';
                $output.= '<span><input type="text" class="textfield occupation_tag" name="'.$arr['field'].$prefix.'" id="'.$arr['field'].$prefix.'"'.$tooltip.' value="'.$dataset[$arr['field']].'" tabindex="'.$this->tabindex.'" /></span>'.$link.'</li>'."\n";
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



/* End of file personaldata.php */
/* Location: ./application/controllers/personaldata.php */
