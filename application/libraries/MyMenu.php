<?php

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

class MyMenu{

    function show_menu(){

        if ( !isset($_SESSION['user']['loginLevel']) )
            $_SESSION['user']['loginLevel'] = 0;

        /* $navigation_array ('ID','Name,'Geschuetzt (
            0=beides,
            1=haushalt,
            2=personendaten,
            3=redakteur,
            4=Admins)')
        */
        $navigation_array    = array();
        $navigation_array[] = array('home','Einf&uuml;hrung',0);
        $navigation_array[] = array('newhousehold','Neue Bewerbung',1);
        $navigation_array[] = array('household','Haushalt',2);
		$navigation_array[] = array('personaldata','Personendaten',2);
        //$navigation_array[] = array('contact','Kontakt',0);
        $navigation_array[] = array('apartment','Wohnungen',3);
        $navigation_array[] = array('user','Benutzer',4);
        $navigation_array[] = array('client','Mandanten',4);

        $obj =& get_instance();
        $obj->load->helper('url');

	    // get the current controller
        $controller = $obj->router->fetch_class();

        $menu  = "\n        <ul class='navMain'>\n";
        foreach ( $navigation_array as $key => $value ) {
            $class = ( $controller == strtolower($value[1]) ? 'active' : 'inactive' );
            if ( $value[2] == 0 ||
               ( $value[2] == 1 && $_SESSION['user']['loginLevel'] < 1 ) ||
               ( $value[2] == 2 && $_SESSION['user']['loginLevel'] > 0 ) ||
               ( $value[2] == 3 && $_SESSION['user']['loginLevel'] > 1 )||
               ( $value[2] == 4 && $_SESSION['user']['loginLevel'] > 2 )) {

                    if ($value[2] <= 1 && $_SESSION['user']['loginLevel'] == 0) {
                        if ( $value[0] == 'newhousehold' ) {
                            $value[0] = 'household';
                        }
                        $menu .= "<li>";
                        $menu .= anchor($value[0],$value[1],"class='$class'");
                        $menu .= "</li>"."\n";
                    } else if (isset($_SESSION['household']['household_id']) && $_SESSION['user']['loginLevel'] == 1) {
                        if ( $value[0] == 'newhousehold' ) {
                            $value[0] = 'household';
                        }
                        if ( $value[0] != 'personaldata') {
                            $menu .= "<li>";
                            $menu .= anchor($value[0],$value[1],"class='$class'");
                            $menu .= "</li>"."\n";
                        }
                    } else if ( $value[2] <= 2 && $_SESSION['user']['loginLevel'] == 1) {
                        if ( $value[0] != 'household') {
                            $menu .= "<li>";
                            $menu .= anchor($value[0],$value[1],"class='$class'");
                            $menu .= "</li>"."\n";
                        }
                    } else {
                        if ( $value[2] >= 3 && $_SESSION['user']['loginLevel'] == 3) {
                            $menu .= "<li>";
                            $menu .= anchor($value[0],$value[1],"class='$class'");
                            $menu .= "</li>"."\n";
                        }
                    }

            }

        }
        $menu .= "        </ul>\n      ";

        // login
        $menu .= "\n        <ul class='navLogin'>\n";
        if ( $_SESSION['user']['loginLevel'] < 1 ) {
            $class = ( $controller == strtolower('login') ? 'active' : 'inactive' );
            $menu .= "          <li>";
            $menu .= anchor('login','Login',"class='$class'");
            $menu .= "</li>"."\n";
        } else {
            $class = ( $controller == strtolower('logout') ? 'active' : 'inactive' );
            $menu .= "          <li>";
            $menu .= anchor('logout','Logout',"class='$class'");
            $menu .= "</li>"."\n";
        }
        $menu .= "        </ul>\n      ";

        return $menu;

    }
}
?>
