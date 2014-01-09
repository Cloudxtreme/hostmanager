<?php

class appMenu{

    function show_menu(){

        if ( !isset($_SESSION['user']['loginlevel']) )
            $_SESSION['user']['loginlevel'] = 0;

        /* $navigation_array ('ID','Name,'Geschuetzt (
            0=beides,
            1=,
            2=,
            3=redakteur,
            4=Admin)')
        */
        $navigation_array    = array();
        $navigation_array[] = array('home','Home',0);
        //$navigation_array[] = array('contact','Kontakt',0);
        $navigation_array[] = array('user','Benutzerverwaltung',4);

        $obj =& get_instance();
        $obj->load->helper('url');

	    // get the current controller
        $controller = $obj->router->fetch_class();

        $menu  = "\n        <ul class='nav navbar-nav'>\n";
        foreach ( $navigation_array as $key => $value ) {
            $class = ( $controller == strtolower($value[1]) ? 'active' : '' );
            if ( $value[2] == 0 ||
               ( $value[2] == 1 && $_SESSION['user']['loginlevel'] < 1 ) ||
               ( $value[2] == 2 && $_SESSION['user']['loginlevel'] > 0 ) ||
               ( $value[2] == 3 && $_SESSION['user']['loginlevel'] > 1 )||
               ( $value[2] == 4 && $_SESSION['user']['loginlevel'] > 2 )) {

                   if ( $value[2] >= 3 && $_SESSION['user']['loginlevel'] == 3) {
                            $menu .= "<li>";
                            $menu .= anchor($value[0],$value[1],"class='$class'");
                            $menu .= "</li>"."\n";
                    }

            }

        }
        $menu .= "        </ul>\n      ";

        // login
        $menu .= "\n        <ul class='nav navbar-nav navbar-right'>\n";
        if ( $_SESSION['user']['loginlevel'] < 1 ) {
            $class = ( $controller == strtolower('login') ? 'active' : '' );
            $menu .= "          <li>";
            $menu .= anchor('login','Login',"class='$class'");
            $menu .= "</li>"."\n";
        } else {
            $class = ( $controller == strtolower('logout') ? 'active' : '' );
            $menu .= "          <li>";
            $menu .= anchor('logout','Logout',"class='$class'");
            $menu .= "</li>"."\n";
        }
        $menu .= "        </ul>\n      ";

        return $menu;

    }
}
?>
