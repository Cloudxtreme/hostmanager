<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class Household extends CI_Controller {

    private $dataset = array();
    private $count_adult_select = array();
    private $count_child_select = array();
	private $array_adult = array('adult_title','adult_firstname','adult_name','adult_date_of_birth','adult_email');
	private $array_child = array('child_firstname','child_name','child_date_of_birth','adult_id_first','adult_id_second');
	private $array_child_long = array();
    private $drop = array();
    private $errordata = array();

    public function __construct() {
        parent::__construct();
        // dropdown adult / child
        for ( $i=1;$i<9;$i++)
            $this->count_adult_select[$i] = $i;

        for ( $i=0;$i<9;$i++)
            $this->count_child_select[$i] = $i;

		// dropdown
		$dropArray = array('DropTitleArray');
		// dropdown
		foreach ($dropArray as $value ) {
			$this->load->library('drop/'.$value);
			$foo = new $value;
			$this->drop[$value] = $foo->get_array();
		}

        $this->adult_id_first_select = array();
        $this->adult_id_second_select = array(''=>'bitte w&auml;hlen');

        // Module laden
        $this->load->model('householdModel','',TRUE);
        $this->load->model('adultModel','',TRUE);
        $this->load->model('childModel','',TRUE);
        $this->load->model('userModel','',TRUE);
    }


    public function index() {
        // load db
        if (!isset($this->dataset['sendform']) && $_SESSION['household']['household_id'] > 0 ) {
            $this->load_db($_SESSION['household']['household_id']);
        }

        // init amount of residents
        $data['household_id'] = $this->dataset['household_id'];
        $data['countAdult'] = form_dropdown('countAdultVar', $this->count_adult_select, $this->dataset['countAdultVar'],'onChange="check_residents();" id="countAdultVar" class="selectField"');
        $data['countChild'] = form_dropdown('countChildVar', $this->count_child_select, $this->dataset['countChildVar'],'onChange="check_residents();" id="countChildVar" class="selectField"');

        // init dropdown legal guardian
        for ($i=1;$i<=$this->dataset['countAdultVar'];$i++) {
            $this->adult_id_first_select[$i]  = 'Erwachsener/in '.$i;
            $this->adult_id_second_select[$i] = 'Erwachsener/in '.$i;
        }

        for ($i=1;$i<9;$i++) {
            $data['adult_title'][$i] = form_dropdown('adult_title_'.$i, $this->drop['DropTitleArray'], $this->dataset['adult_title_'.$i],' id="adult_title_'.$i.'" class="textfield"');
            $data['adult_firstname'][$i] = $this->dataset['adult_firstname_'.$i];
            $data['adult_name'][$i] = $this->dataset['adult_name_'.$i];
            $data['adult_date_of_birth'][$i] = $this->dataset['adult_date_of_birth_'.$i];
            $data['adult_email'][$i] = $this->dataset['adult_email_'.$i];
            if ( $this->dataset['household_id'] > 0 ) {
                $data['adult_delete'][$i] = '<tr><td class="tdField" colspan="3"><input type="submit" class="btn" name="adult_delete['.$i.']" value="Erwachsenen entfernen" /></td></tr>';
                $data['child_delete'][$i] = '<tr><td class="tdField" colspan="3"><input type="submit" class="btn" name="child_delete['.$i.']" value="Kind entfernen" /></td></tr>';
            }
            $data['child_firstname'][$i] = $this->dataset['child_firstname_'.$i];
            $data['child_name'][$i] = $this->dataset['child_name_'.$i];
            $data['child_date_of_birth'][$i] = $this->dataset['child_date_of_birth_'.$i];
            $data['adult_id_first'][$i] = form_dropdown('adult_id_first_'.$i, $this->adult_id_first_select, $this->dataset['adult_id_first_'.$i],' id="adult_id_first_'.$i.'" class="textfield"');
            $data['adult_id_second'][$i] = form_dropdown('adult_id_second_'.$i, $this->adult_id_second_select, $this->dataset['adult_id_second_'.$i],' id="adult_id_second_'.$i.'" class="textfield"');
        }

        if ( count($this->errordata) > 0 )
            foreach ($this->errordata as $eMsg )
                $data['error_messages'].= $eMsg."<br />";

        $data['action'] = site_url('household/save_request');
        // debug clientfähig
        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;
        $data['navigation']  = $menu->show_menu();
        if($_SESSION['user']['loginLevel'] == 1) {
                $data['subnavigation']  = '<ul class="navSub"><li class="selected"><a href="'.base_url().'index.php/household">Haushaltstruktur</a></li><li><a href="'.base_url().'index.php/householdsurvey">Weitere Angaben zum Haushalt</a></li><li><a href="'.base_url().'index.php/request">Wohnungsauswahl</a></li>';
        }


        $data['homeTitle'] 	 = 'Bewerbung Haushalt';
        $data['headerTitle']  =  $_SESSION ['client']['client_name'];
        $data['buttonTitle'] = 'Bewerbung einreichen';
        if ( $this->dataset['household_id'] > 0 ) {
            $data['homeTitle'] 	 = 'Haushaltstruktur';
            $data['buttonTitle'] = 'Haushaltstruktur speichern';
            $data['buttonDelete'] = '<input class="btn" value="Haushalt löschen" id="householdDelete" onclick="if ( confirm(\'Wollen Sie den Haushalt wirklich löschen?\')) { location.href=\'household/delete\';}" />&nbsp&nbsp';
        }
        $data['mainContent'] = $this->load->view('household/household', $data, true);
        $data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);

    }

    private function load_db($household_id) {

        $result = $this->householdModel->get_by_id($household_id);
        if ( $result->num_rows > 0 ){
            $row = $result->row_array();
            $this->dataset['household_id'] = $row['household_id'];
            $this->dataset['countAdultVar'] = $row['household_adults'];
            $this->dataset['countChildVar'] = $row['household_children'];

            $adultIdArr = array();
            $adults = $this->adultModel->get_by_household_id( $this->dataset['household_id'])->result();
            $i = 1;
            foreach ($adults as $adult){
                $this->dataset['adult_title_'.$adult->adult_order] = $adult->adult_title;
                $this->dataset['adult_firstname_'.$adult->adult_order] = $adult->adult_firstname;
                $this->dataset['adult_name_'.$adult->adult_order] = $adult->adult_name;
                $this->dataset['adult_date_of_birth_'.$adult->adult_order] = $adult->adult_date_of_birth;
                $this->dataset['adult_email_'.$adult->adult_order] = $adult->adult_email;
                $adultIdArr[$i] = $adult->adult_id;
                $i++;
            }

            $children = $this->childModel->get_by_household_id( $this->dataset['household_id'])->result();
            $i = 1;
            foreach ($children as $child){
                $this->dataset['child_firstname_'.$child->child_order] = $child->child_firstname;
                $this->dataset['child_name_'.$child->child_order] = $child->child_name;
                $this->dataset['child_date_of_birth_'.$child->child_order] = $child->child_date_of_birth;
                foreach ($adultIdArr as $key => $value){
                    if($child->adult_id_first == $value) {
                        $this->dataset['adult_id_first_'.$child->child_order] = $key;
                    }
                }
                $this->dataset['adult_id_second_'.$child->child_order] = 0;
                foreach ($adultIdArr as $key => $value){
                    if($child->adult_id_second == $value) {
                        $this->dataset['adult_id_second_'.$child->child_order] = $key;
                    }
                }
                $i++;
            }

        }
    }

    public function save_request() {
        // get hiden data
        $this->dataset['sendform']  = $this->input->post('sendform');
        $this->dataset['household_id']  = $this->input->post('household_id');
        // get count var
        $this->dataset['countAdultVar'] = $this->input->post('countAdultVar');
        $this->dataset['countChildVar'] = $this->input->post('countChildVar');

        // get adult data
        for ($i=1;$i<=$this->dataset['countAdultVar'];$i++) {
            $this->dataset['adult_title_'.$i] = $this->input->post('adult_title_'.$i);
            $this->dataset['adult_firstname_'.$i] = $this->input->post('adult_firstname_'.$i);
            $this->dataset['adult_name_'.$i] = $this->input->post('adult_name_'.$i);
            $this->dataset['adult_date_of_birth_'.$i] = $this->input->post('adult_date_of_birth_'.$i);
            $this->dataset['adult_email_'.$i] = $this->input->post('adult_email_'.$i);
        }

		// get child data
		//hack fill array_child_long with all db fields ->
	 	$children = $this->childModel->get_by_household_id( $this->dataset['household_id']);
		$i = 1;
        foreach ($children->result_array() as $child){
        	foreach ($child as $key => $value){
        		$this->dataset[$key.'_'.$i] = $value;
                if(!array_key_exists($key, $this->array_child_long)) {
                    $this->array_child_long[$key] = '';
                }
			}
			$i++;
		}

        for ($i=1;$i<=$this->dataset['countChildVar'];$i++) {
            $this->dataset['child_firstname_'.$i] = $this->input->post('child_firstname_'.$i);
            $this->dataset['child_name_'.$i] = $this->input->post('child_name_'.$i);
            $this->dataset['child_date_of_birth_'.$i] = $this->input->post('child_date_of_birth_'.$i);
            $this->dataset['adult_id_first_'.$i] = $this->input->post('adult_id_first_'.$i);
            $this->dataset['adult_id_second_'.$i] = $this->input->post('adult_id_second_'.$i);
        }

        // delete adult or child
        if ( $this->input->post('adult_delete') OR $this->input->post('child_delete') ) {
            $countAdults   = $this->dataset['countAdultVar'];
            $countChildren = $this->dataset['countChildVar'];
            $tmpArr = $this->dataset;
            // reset data array
            $this->dataset = array();
            // get basic data back
            $this->dataset['sendform'] = $this->input->post('sendform');
            $this->dataset['household_id'] = $this->input->post('household_id');
            $this->dataset['countAdultVar'] = $this->input->post('countAdultVar');
            $this->dataset['countChildVar'] = $this->input->post('countChildVar');
            if ( $this->input->post('adult_delete') )
                $this->dataset['countAdultVar'] = $this->dataset['countAdultVar']-1;
            if ( $this->input->post('child_delete') )
                $this->dataset['countChildVar'] = $this->dataset['countChildVar']-1;


            // delete adult
            $toDelete = '';
            if ($this->input->post('adult_delete')) {
                foreach ( $this->input->post('adult_delete') as $key => $value ) {
                    $toDelete =  $key;
                }
            }

            if ($this->dataset['countAdultVar'] >= 1) {
                $tmp_i = 1;
                for ($i=1;$i<=$countAdults;$i++) {
                    $this->dataset['adult_title_'.$tmp_i] = $tmpArr['adult_title_'.$i];
                    $this->dataset['adult_firstname_'.$tmp_i] = $tmpArr['adult_firstname_'.$i];
                    $this->dataset['adult_name_'.$tmp_i] = $tmpArr['adult_name_'.$i];
                    $this->dataset['adult_date_of_birth_'.$tmp_i] = $tmpArr['adult_date_of_birth_'.$i];
                    $this->dataset['adult_email_'.$tmp_i] = $tmpArr['adult_email_'.$i];
                    if ( $i == $toDelete )
                        $tmp_i = $tmp_i-1;
                    $tmp_i++;
                }
            } else {
                $this->dataset['adult_title_1'] = '';
                $this->dataset['adult_firstname_1'] =  '';
                $this->dataset['adult_name_1'] =  '';
                $this->dataset['adult_date_of_birth_1'] =  '';
                $this->dataset['adult_email_1'] =  '';
            }

            // delete child
            $toDelete = '';
            if ( $this->input->post('child_delete')) {
                 foreach ( $this->input->post('child_delete') as $key => $value ) {
                    $toDelete =  $key;
                }
            }

            if ($this->dataset['countChildVar'] >= 1 ) {
                // hack get child data
                $tmp_i = 1;
                $children = $this->childModel->get_by_household_id( $this->dataset['household_id'])->result_array();
                foreach ($children as $child){
                    foreach ($child as $key => $value){
                        $this->dataset[$key.'_'.$tmp_i] = $value;
                    }
                    if ( $i == $toDelete )
                       $tmp_i = $tmp_i-1;
                    $tmp_i++;
                }
                // overwrite with formdata
				$tmp_i = 1;
                for ($i=1;$i<=$countChildren;$i++) {
                    $this->dataset['child_firstname_'.$tmp_i] = $tmpArr['child_firstname_'.$i];
                    $this->dataset['child_name_'.$tmp_i] = $tmpArr['child_name_'.$i];
                    $this->dataset['child_date_of_birth_'.$tmp_i] = $tmpArr['child_date_of_birth_'.$i];
                    $this->dataset['adult_id_first_'.$tmp_i] = $tmpArr['adult_id_first_'.$i];
                    $this->dataset['adult_id_second_'.$tmp_i] = $tmpArr['adult_id_second_'.$i];
                    if ( $i == $toDelete )
                        $tmp_i = $tmp_i-1;
                    $tmp_i++;
                }
            }
            $this->index();
        } else { // end delete
            if ( $this->check_save_request() )
                $this->save();
            else
                $this->index();
        }

    }

    public function save() {

        $adultIdArr = array();
        $sendMailArr = array();
        // main adult id
        $householdMainAdultId = '';
        // redirect to household
        $newhousehold = array();

        // new household
        if ( $this->dataset['household_id'] < 1 ) {
            $userArr = array();
            $userArr['client_id'] = $_SESSION['client']['client_id'];
            $userArr['username'] = $this->userModel->generate_username('HH_');
            $newhousehold['username'] = $userArr['username'];
            $userArr['password'] = $this->userModel->generate_password();
            $newhousehold['password'] = $userArr['password'];
            $userArr['email'] = $this->dataset['adult_email_1'];
            $userArr['loginLevel'] = 1;
            $userArr['status'] = 'A';
            $user_id = $this->userModel->save($userArr);
            //autologin
            $userArr['autologin']= base_url().'index.php/autologin/?checkstring='.md5($userArr['username'].$userArr['password']);
            $this->dataset['household_id'] = $this->householdModel->save(array('client_id'=>$_SESSION['client']['client_id'],'user_id'=>$user_id));
            $newhousehold['household_id'] = $this->dataset['household_id'];
            $userArr['type'] = 'HH';
            $sendMailArr['HH'] = $userArr;
        }

		// clean houshold (child delete)
        $this->householdModel->cleanup_household($this->dataset['household_id']);

        // save adults
        for ($i=1;$i<=$this->dataset['countAdultVar'];$i++) {
            $result = $this->householdModel->get_household_by_email($this->dataset['adult_email_'.$i],$this->dataset['adult_name_'.$i],$this->dataset['adult_firstname_'.$i]);
            if ( $result->num_rows > 0 ){
                $row = $result->row_array();
                $adult_id = $row['adult_id'];
                if ( $row['household_id'] != $this->dataset['household_id'] )
                    $sendMailArr[$adult_id] = $row;
            } else {
                $userArr = array();
                $userArr['client_id'] = $_SESSION['client']['client_id'];
                $userArr['username'] = $this->userModel->generate_username('PD_');
                $userArr['email'] = $this->dataset['adult_email_'.$i];
                $userArr['password'] = $this->userModel->generate_password();
                $userArr['loginLevel'] = 1;
                $userArr['status'] = 'A';
                $user_id = $this->userModel->save($userArr);
                $adult_id = $this->adultModel->save(array('client_id'=>$_SESSION['client']['client_id'],'user_id'=>$user_id));
                //autologin
                $userArr['autologin']= base_url().'index.php/autologin/?checkstring='.md5($userArr['username'].$userArr['password']);
                $userArr['type'] = 'PD';
                $userArr['adult_title'] = $this->dataset['adult_title_'.$i];
                $userArr['adult_firstname'] = $this->dataset['adult_firstname_'.$i];
                $userArr['adult_name'] = $this->dataset['adult_name_'.$i];
                $userArr['adult_email'] = $this->dataset['adult_email_'.$i];
                $sendMailArr[$adult_id] = $userArr;
            }
            $adultArr = array();
            $adultArr['household_id'] = $this->dataset['household_id'];
            $adultArr['adult_order'] = $i;
            $adultArr['adult_title'] = $this->dataset['adult_title_'.$i];
            $adultArr['adult_firstname'] = $this->dataset['adult_firstname_'.$i];
            $adultArr['adult_name'] = $this->dataset['adult_name_'.$i];
            $adultArr['adult_date_of_birth'] = $this->dataset['adult_date_of_birth_'.$i];
            $adultArr['adult_email'] = $this->dataset['adult_email_'.$i];
            $this->adultModel->update($adult_id,$adultArr);
            $adultIdArr[$i] = $adult_id;
            if ( $i == 1 ) {
                $householdMainAdultId = $adult_id;
                if ( isset($sendMailArr['HH'])) {
                    $sendMailArr['HH']['adult_title'] = $adultArr['adult_title'];
                    $sendMailArr['HH']['adult_firstname'] = $adultArr['adult_firstname'];
                    $sendMailArr['HH']['adult_name'] = $adultArr['adult_name'];
                    $sendMailArr['HH']['adult_email'] = $adultArr['adult_email'];
                }
            }
        }

        // save children
        for ($i=1;$i<=$this->dataset['countChildVar'];$i++) {
            $childArr = array();

			// hack
            foreach ($this->array_child_long as $key => $value){
                if(empty($this->dataset[$key.'_'.$i])) $this->dataset[$key.'_'.$i]='';
                $childArr[$key] = $this->dataset[$key.'_'.$i];
            }

            $childArr['client_id'] = $_SESSION['client']['client_id'];
            $childArr['household_id'] = $this->dataset['household_id'];
            $childArr['child_order'] = $i;
            $childArr['child_firstname'] = $this->dataset['child_firstname_'.$i];
            $childArr['child_name'] = $this->dataset['child_name_'.$i];
            $childArr['child_date_of_birth'] = $this->dataset['child_date_of_birth_'.$i];
            $childArr['adult_id_first'] = $adultIdArr[$this->dataset['adult_id_first_'.$i]];
            $childArr['adult_id_second'] = $adultIdArr[$this->dataset['adult_id_second_'.$i]];
			if(empty($childArr['adult_id_second'])) $childArr['adult_id_second'] = 0;
            $this->childModel->save($childArr);
        }

        // update household
        $householdArr = array();
        $householdArr['adult_id'] = $householdMainAdultId;
        $householdArr['household_adults'] = $this->dataset['countAdultVar'];
        $householdArr['household_children'] = $this->dataset['countChildVar'];
        $this->householdModel->update($this->dataset['household_id'],$householdArr);

        // send emails
        if(!empty($newhousehold)) {
            if ( count($sendMailArr) > 0 )
                $this->send_mail($sendMailArr);
        } else {
            if ( count($sendMailArr) > 0 )
                $this->send_mail_reminder($sendMailArr);
        }

        // redirect household
        if ( $_SESSION['user']['loginLevel'] < 1 ) {
            $result = $this->userModel->login($newhousehold['username'],$newhousehold['password']);
            if($result) {
                $sess_array = array();
                foreach($result as $row)
                    $sess_array = array ( 'id' => $row->id,'username' => $row->username,'loginLevel' => $row->loginLevel);
                $_SESSION['user'] = $sess_array;
                $_SESSION['household']['household_id'] = $newhousehold['household_id'];
            }
        }


        $this->load->library('MyMenu');
        $menu = new MyMenu;
        $this->load->library('MyFooter');
        $footer = new MyFooter;
        $data['navigation']  = $menu->show_menu();
        if($_SESSION['user']['loginLevel'] == 1) {
            $data['subnavigation']  = '<ul class="navSub"><li class="selected"><a href="'.base_url().'index.php/household">Haushaltstruktur</a></li><li><a href="'.base_url().'index.php/householdsurvey">Weitere Angaben zum Haushalt</a></li><li><a href="'.base_url().'index.php/request">Wohnungsauswahl</a></li>';
        }
        $data['mainContent'] = $this->load->view('household/savedsuccess', $data, true);;
        $data['homeTitle'] 	 = 'Haushalt';
		$data['headerTitle']  =  $_SESSION ['client']['client_name'];
        $data['footer'] = $footer->show_footer();
        $this->load->view('main_template', $data);
    }


    private function check_save_request() {

        $this->errordata = array();

        for ($i=1;$i<=$this->dataset['countAdultVar'];$i++) {
            if ( empty($this->dataset['adult_title_'.$i]) )
                $this->errordata['adult_title_'.$i] = 'Bitte eine Anrede bei Erwachsenen '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['adult_firstname_'.$i]) )
                $this->errordata['adult_firstname_'.$i] = 'Bitte einen Vornamen bei Erwachsenen/in '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['adult_name_'.$i]) )
                $this->errordata['adult_name_'.$i] = 'Bitte einen Nachnamen bei Erwachsenen/in '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['adult_name_'.$i]) )
                $this->errordata['adult_date_of_birth_'.$i] = 'Bitte einen Geburtstag bei Erwachsenen/in '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['adult_email_'.$i]) )
                $this->errordata['adult_email_'.$i] = 'Bitte eine E-Mailadresse bei Erwachsenen/in '.$i.' w&auml;hlen.';
            if ( !(bool)preg_match('/^\w[\w.!�$%&\'*=?^_�{|}\/+-]{0,63}@[\d\p{L}.-]{2,253}\w{2}$/', $this->dataset['adult_email_'.$i]) )
                $this->errordata['adult_email_'.$i] = 'Bitte eine g&uuml;ltige E-Mailadresse bei Erwachsenen/in '.$i.' w&auml;hlen.';
            if ( !isset($this->errordata['adult_email_'.$i]) ) {
                $result = $this->householdModel->get_household_by_email($this->dataset['adult_email_'.$i],$this->dataset['adult_name_'.$i],$this->dataset['adult_firstname_'.$i]);
                if ( $result->num_rows > 0 ){
                    $row = $result->row_array(1);
                    if ( $row['household_id'] > 0 && $row['household_id'] != $this->dataset['household_id'] )
                        $this->errordata['household_id_'.$i] = 'Der/Die Erwachsene '.$i.' ist bereits in einen anderen Haushalt eingetragen.';
                }
            }
        }

        for ($i=1;$i<=$this->dataset['countChildVar'];$i++) {
            if ( empty($this->dataset['child_firstname_'.$i]) )
                $this->errordata['child_firstname_'.$i] = 'Bitte einen Vornamen f&uuml;r Kind '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['child_name_'.$i]) )
                $this->errordata['child_name_'.$i] = 'Bitte einen Nachnamen f&uuml;r Kind '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['child_date_of_birth_'.$i]) )
                $this->errordata['child_date_of_birth_'.$i] = 'Bitte einen Geburtstag f&uuml;r Kind '.$i.' w&auml;hlen.';
            if ( empty($this->dataset['adult_id_first_'.$i]) )
                $this->errordata['adult_id_first_'.$i] = 'Bitte einen Erziehungsberechtigten f&uuml;r Kind '.$i.' w&auml;hlen.';
        }

        if ( count($this->errordata) > 0 )
            return false;

        return true;

    }

    private function send_mail($senMailArr) {
        foreach ($senMailArr as $key => $value) {
            $anrede = $value['adult_title'] == 1? 'Herr':'Frau';
            $mailtext = 'Guten Tag '.$anrede.' '.$value['adult_firstname'].' '.$value['adult_name']."\n\n";
            //$mailtext .= 'Ihre Userdaten wurden einem neuen Haushalt hinzugefuegt.'."\n";
            if ( $key == 'HH')
                $mailtext .= 'Der Haushalt wurde eingerichtet. Sie sind als haushaltsverantwortliche Kontaktperson aufgefuehrt.'."\n".'
Wir benötigen allerdings noch weitere Angaben von Ihnen. Mit den untenstehenden Zugangsdaten haben Sie die Moeglichkeit, die Haushaltsstruktur anzupassen, den Haushaltsfragebogen auszufuellen und bis zu 3 Wohnungen auszuwaehlen. Der direkte Zugang nimmt Ihnen die Eingabe der Zugangsdaten ab.'."\n\n";
            else
                $mailtext .= 'Sie wurden als Haushaltsmitglied in einer Bewerbung für Wohnungen auf dem Hunziker Areal aufgeführt. Bitte füllen Sie Ihren persönlichen Fragebogen aus.'."\n".'Mit den untenstehenden Zugangsdaten koennen Sie ihre persoenlichen Daten ergaenzen und gegebenenfalls aendern. Der direkte Zugang nimmt Ihnen die Eingabe der Zugangsdaten ab.'."\n\n";

            $mailtext .= 'Benutzername: '.$value['username']."\n";
            $mailtext .= 'Passwort: '.$value['password']."\n";
            $mailtext .= 'Autologin: '.$value['autologin']."\n";

if ( $key == 'HH')
$mailtext .= "\n\n".'Neben diesem Haushaltslogin erhalten Sie in einer separaten Email einen persoenlichen Zugang zu Ihrem individuellen Personenfragebogen. Die uebrigen aufgefuehrten volljaehrigen Personen Ihrer Bewerbung erhalten ebenfalls einen persoenlichen Zugang zu ihren jeweiligen Personenfrageboegen.'."\n\n";

            $mailtext .= '----------------------------------------------'."\n";
            $mailtext .= 'Bewerbungsformular der Baugenossenschaft mehr als wohnen'."\n";
            $mailtext .= 'http://'.$_SERVER['HTTP_HOST']."\n\n";

            $header  = 'MIME-Version: 1.0' . "\r\n";
            $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $header .= 'From: =?ISO-8895-15?Q?Mieterdaten?= <info@mieterdaten.ch>' . "\r\n";

            $header = "From: info@".$_SERVER['HTTP_HOST'];

            mail($value['adult_email'],'Bewerbungsformular der Baugenossenschaft mehr als wohnen',$mailtext,$header);
		}
    }

    private function send_mail_reminder($senMailArr) {
        foreach ($senMailArr as $key => $value) {
            if ( $key != 'HH') {
                $anrede = $value['adult_title'] == 1? 'Herr':'Frau';
                $mailtext = 'Guten Tag '.$anrede.' '.$value['adult_firstname'].' '.$value['adult_name']."\n\n";

                $mailtext .= 'Die Haushaltsstruktur Ihrer Bewerbung bei mehr als wohnen wurde angepasst.'."\n\n";
                $mailtext .= '----------------------------------------------'."\n";
                $mailtext .= 'Bewerbungsformular der Baugenossenschaft mehr als wohnen'."\n";
                $mailtext .= 'http://'.$_SERVER['HTTP_HOST']."\n\n";

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $header .= 'From: =?ISO-8895-15?Q?Mieterdaten?= <info@mieterdaten.ch>' . "\r\n";

                $header = "From: info@".$_SERVER['HTTP_HOST'];

                mail($value['adult_email'],'Bewerbungsformular der Baugenossenschaft mehr als wohnen',$mailtext,$header);
            }
        }

    }

    public function delete() {
        $household_id = $_SESSION['household']['household_id'];

        $result = $this->householdModel->get_by_id($household_id);
        if ( $result->num_rows > 0 ) {
            $row = $result->row_array(1);
            $user_id = $row['user_id'];
        }
        $this->userModel->delete($user_id);
        $this->householdModel->delete($household_id);
        $this->householdModel->cleanup_household($household_id);
        if ( $_SESSION['user']['loginLevel'] < 3 )
            redirect('logout', 'refresh');
        else {
            unset($_SESSION['household']);
            redirect('home', 'refresh');
        }
    }
}

/* End of file houshold.php */
/* Location: ./application/controllers/houshold.php */
