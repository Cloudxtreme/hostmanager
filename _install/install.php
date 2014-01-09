<?php
/*----------------------------------------------------------------------------------------------------------------------
    Includes
----------------------------------------------------------------------------------------------------------------------*/
$system_path = 'system';
define('BASEPATH', str_replace("\\", "/", $system_path));

// Datenbank-Verbindung herstellen
include_once(dirname(__FILE__).'/../application/config/database.php');
mysql_connect($db['default']['hostname'],$db['default']['username'],$db['default']['password']);
mysql_select_db($db['default']['database']);

/*----------------------------------------------------------------------------------------------------------------------
    Functions
----------------------------------------------------------------------------------------------------------------------*/

function get_db_fields($arr, $level=0) {
	global $query;
	if(is_array($arr)) {
		foreach($arr as $key => $item ) {
			if(is_array($item)) {
				if(array_key_exists("field",$item) && array_key_exists("datatype",$item)) {
					$query .= "`".$item["field"]."` ".$item["datatype"]." NOT NULL,";
			  }
			}
			if(is_array($arr[$key])) {
				get_db_fields($arr[$key], $level+1);
			}
		}
	}
}

function handle_table ($inputQuery,$tableName,$action='create') {
    $output01 =  '<span style="color:#00CC00; font-weight: bold;">OK</span>';
    $output02 =  '<span style="color:#CC0000; font-weight: bold;">nicht OK</span>';
    if ( $action == 'create') {
        echo '&nbsp;'."<br />\n";
        $query  = "DROP TABLE IF EXISTS `".$tableName."`";
        if ( mysql_query ($query) )
            echo 'Die Tabelle <i>'.$tableName.'</i> wurde gel&ouml;scht - '.$output01."<br />\n";
        else
            echo 'Die Tabelle <i>'.$tableName.'</i> konnte nicht gel&ouml;scht werden - '.$output02."<br />\n";
		
        if ( mysql_query ($inputQuery) )
            echo 'Die Tabelle <i>'.$tableName.'</i> wurde neu angelegt - '.$output01."<br />\n";
        else
            echo 'Die Tabelle <i>'.$tableName.'</i> konnte nicht neu angelegt - '.$output02."<br />\n";
    } else {
        if ( mysql_query ($inputQuery) )
            echo 'Die Daten in die Tabelle <i>'.$tableName.'</i> wurden aufgenommen - '.$output01."<br />\n";
        else
            echo 'Die Daten in die Tabelle <i>'.$tableName.'</i> wurden nicht aufgenommen - '.$output02."<br />\n";
    }
}

function cleanup_table($tableName) {
    $output01 =  '<span style="color:#00CC00; font-weight: bold;">OK</span>';
    $output02 =  '<span style="color:#CC0000; font-weight: bold;">nicht OK</span>';
    $query  = "DROP TABLE `".$tableName."`";
    if ( mysql_query ($query) )
        echo 'Die Tabelle <i>'.$tableName.'</i> wurde gel&ouml;scht - '.$output01."<br />\n";
}

/*----------------------------------------------------------------------------------------------------------------------
    Tables
----------------------------------------------------------------------------------------------------------------------*/

echo '------------------------------------------------------------------------------------------------------'."<br />\n";
echo '<b>Initiale Erstellung der ben&ouml;tigten Datenbank-Tabellen </b>'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";


// Table user loeschen und einfuegen

$query  = "CREATE TABLE IF NOT EXISTS `user` (";
$query .= "`user_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`user_username` varchar(255) NOT NULL,";
$query .= "`user_email` varchar(255) NOT NULL,";
$query .= "`user_password` varchar(255) NOT NULL,";
$query .= "`user_loginlevel` smallint(1) NOT NULL,";
$query .= "`user_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',";
$query .= "PRIMARY KEY (`user_id`)";
$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'user');


/*----------------------------------------------------------------------------------------------------------------------
    Table Content
----------------------------------------------------------------------------------------------------------------------*/


echo '&nbsp;'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";
echo '<b>Abf&uuml;llen der Daten </b>'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";


// Benutzer
$query  = "INSERT INTO `user` (`user_username`, `user_email`,`user_password`, `user_loginlevel`,`user_status`) VALUES ";
$query .= "('info@pan-x.ch','info@pan-x.ch', 'beetester45', 3, 'A')";
handle_table($query,'user','insert');
