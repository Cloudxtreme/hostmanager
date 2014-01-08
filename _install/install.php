<?php

 /*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */
 
/*----------------------------------------------------------------------------------------------------------------------
    Includes
----------------------------------------------------------------------------------------------------------------------*/

//*******************  USER_ID ANPASSEN *********************************

$client = array();
$client[0]['client_name']= "Mieterdatensoftware";
$client[0]['client_url']= "panx";
$client[0]['client_email']= "info@pan-x.ch";
$client[0]['generic_module'] = array();

$client[1]['client_name']= "mehr als wohnen";
$client[1]['client_url']= "maw";
$client[1]['client_email']= "info@mehralswohnen.ch";
$client[1]['generic_module'] = array('adult','child','householdsurvey');

$client[2]['client_name']= "Kraftwerk 1";
$client[2]['client_url']= "kw1";
$client[2]['client_email']= "info@kraftwerk1.ch";
$client[2]['generic_module'] = array('adult','child','householdsurvey','survey1');

// load libraries (config array)
foreach($client as $client_key => $client_item ) {
    if(!empty($client_item['generic_module'])){
        foreach($client_item['generic_module'] as $module_key => $module_item ) {
            include_once( dirname(__FILE__).'/../application/libraries/'.$client_item['client_url'].'/'.ucfirst($client_item['client_url']).ucfirst($module_item).'Array.php');
        }
    }
}

/*----------------------------------------------------------------------------------------------------------------------
    Settings
----------------------------------------------------------------------------------------------------------------------*/

$system_path = 'system';
define('BASEPATH', str_replace("\\", "/", $system_path));

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

// Datenbank-Verbindung herstellen
include_once(dirname(__FILE__).'/../application/config/database.php');
mysql_connect($db['default']['hostname'],$db['default']['username'],$db['default']['password']);
mysql_select_db($db['default']['database']);


// Table client loeschen und einfuegen
echo '&nbsp;'."<br />\n";
$query  = "CREATE TABLE IF NOT EXISTS `client` (";
$query .= "`client_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`client_name` varchar(255) NOT NULL,";
$query .= "`client_url` varchar(255) NOT NULL,";
$query .= "`client_mail` varchar(255) NOT NULL,";
$query .= "`client_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',";
$query .= "PRIMARY KEY (`client_id`)";
$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'client');


// building
$query = "CREATE TABLE `building` (
  `building_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `building_object` int(11) NOT NULL,
  `building_title` varchar(255) NOT NULL DEFAULT '',
  `building_adress` varchar(255) NOT NULL,
  `building_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',
  PRIMARY KEY (`building_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'building');


// apartment
$query = "CREATE TABLE IF NOT EXISTS `apartment` (
  `apartment_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `apartment_object` int(11) NOT NULL,
  `apartment_title` varchar(255) NOT NULL DEFAULT '',
  `apartment_floor` varchar(255) NOT NULL,
  `apartment_type` varchar(255) NOT NULL DEFAULT '',
  `apartment_rooms` float NOT NULL,
  `apartment_area` int(4) NOT NULL,
  `apartment_balcony` int(4) NOT NULL,
  `apartment_rentalgross` int(5) NOT NULL,
  `apartment_participation` int(7) NOT NULL,
  `apartment_subvention` smallint(1) NOT NULL,
  `apartment_reserved` smallint(1) NOT NULL,
  `apartment_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',
  PRIMARY KEY (`apartment_id`),
  KEY `client_id` (`client_id`),
  KEY `building_id` (`building_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'apartment');


// addroom
$query  = "CREATE TABLE `addroom` (
  `addroom_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `addroom_object` int(11) NOT NULL,
  `addroom_title` varchar(255) NOT NULL DEFAULT '',
  `addroom_floor` varchar(255) NOT NULL,
  `addroom_type` varchar(255) NOT NULL DEFAULT '',
  `addroom_rooms` float NOT NULL,
  `addroom_area` int(4) NOT NULL,
  `addroom_balcony` int(4) NOT NULL,
  `addroom_rentalgross` int(5) NOT NULL,
  `addroom_participation` int(7) NOT NULL,
  `addroom_subvention` smallint(1) NOT NULL,
  `addroom_reserved` smallint(1) NOT NULL,
  `addroom_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',
  PRIMARY KEY (`addroom_id`),
  KEY `client_id` (`client_id`),
  KEY `building_id` (`building_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'addroom');

// Table household loeschen und einfuegen
$query  = "CREATE TABLE IF NOT EXISTS `household` (";
$query .= "`household_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`client_id` int(11) NOT NULL,";
$query .= "`apartment_id` int(11) NOT NULL,";
$query .= "`user_id` int(11) NOT NULL,";
$query .= "`adult_id` int(11) NOT NULL,";
$query .= "`household_adults` smallint(2) NOT NULL,";
$query .= "`household_children` smallint(2) NOT NULL,";
$query .= "`household_apartment_prio1` int(11) NOT NULL,";
$query .= "`household_apartment_prio2` int(11) NOT NULL,";
$query .= "`household_apartment_prio3` int(11) NOT NULL,";
$query .= "`household_addroom_prio1` int(11) NOT NULL,";
$query .= "`household_addroom_prio2` int(11) NOT NULL,";
$query .= "`household_addroom_prio3` int(11) NOT NULL,";
$query .= "`household_addroom` int(11) NOT NULL,";
$query .= "`household_apartment` int(11) NOT NULL,";
$query .= "`household_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',";
$query .= "PRIMARY KEY (`household_id`),";
$query .= "KEY `client_id` (`client_id`),";
$query .= "KEY `apartment_id` (`apartment_id`),";
$query .= "KEY `user_id` (`user_id`),";
$query .= "KEY `adult_id` (`adult_id`)";
$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'household');

// Table user loeschen und einfuegen
// to do: db name users -> user / field with prefix user_
$query  = "CREATE TABLE IF NOT EXISTS `user` (";
$query .= "`user_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`client_id` int(11) NOT NULL,";
$query .= "`user_username` varchar(255) NOT NULL,";
$query .= "`user_email` varchar(255) NOT NULL,";
$query .= "`user_password` varchar(255) NOT NULL,";
$query .= "`user_loginLevel` smallint(1) NOT NULL,";
$query .= "`user_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A - aktiv, I - inaktiv',";
$query .= "PRIMARY KEY (`user_id`),";
$query .= "KEY `client_id` (`client_id`)";
$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'user');


//*****************************************************************************
// create client db's

foreach($client as $client_key => $client_item ) {
    if(!empty($client_item['generic_module'])){
        foreach($client_item['generic_module'] as $module_key => $module_item ) {
            switch ( $module_item ) {
                // adult
                case 'adult':
                    $query  = "CREATE TABLE IF NOT EXISTS `".$client_item['client_url']."_adult` (";
                    $query .= "`adult_id` int(11) NOT NULL AUTO_INCREMENT,";
                    $query .= "`client_id` int(11) NOT NULL,";
                    $query .= "`user_id` int(11) NOT NULL,";
                    $query .= "`household_id` int(11) NOT NULL,";
                    $class_name = ucfirst($client_item['client_url'].'AdultArray');
                    $a = new $class_name;
                    get_db_fields($a->get_array());
                    $query .= "`adult_order` smallint(2) NOT NULL,";
                    $query .= "`adult_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A -aktiv, I- inaktiv',";
                    $query .= "PRIMARY KEY (`adult_id`),";
                    $query .= "KEY `client_id` (`client_id`),";
                    $query .= "KEY `user_id` (`user_id`),";
                    $query .= "KEY `household_id` (`household_id`)";
                    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
                    handle_table($query,$client_item['client_url'].'_adult');
                    break;

                // child
                case 'child':
                    $query  = "CREATE TABLE IF NOT EXISTS `".$client_item['client_url']."_child` (";
                    $query .= "`child_id` int(11) NOT NULL AUTO_INCREMENT,";
                    $query .= "`client_id` int(11) NOT NULL,";
                    $query .= "`household_id` int(11) NOT NULL,";
                    $class_name = ucfirst($client_item['client_url'].'ChildArray');
                    $a = new $class_name;
                    get_db_fields($a->get_array());
                    $query .= "`adult_id_first` int(11) NOT NULL,";
                    $query .= "`adult_id_second` int(11) NOT NULL,";
                    $query .= "`child_order` smallint(2) NOT NULL,";
                    $query .= "`child_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A -aktiv, I - inaktiv',";
                    $query .= "PRIMARY KEY (`child_id`),";
                    $query .= "KEY `client_id` (`client_id`),";
                    $query .= "KEY `adult_id_first` (`adult_id_first`),";
                    $query .= "KEY `adult_id_second` (`adult_id_second`),";
                    $query .= "KEY `household_id` (`household_id`)";
                    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
                    handle_table($query,$client_item['client_url'].'_child');
                    break;

                //householdsurvey
                case 'householdsurvey':
                    $query  = "CREATE TABLE IF NOT EXISTS `".$client_item['client_url']."_householdsurvey` (";
                    $query .= "`householdsurvey_id` int(11) NOT NULL AUTO_INCREMENT,";
                    $query .= "`client_id` int(11) NOT NULL,";
                    $query .= "`household_id` int(11) NOT NULL,";
                    $class_name = ucfirst($client_item['client_url'].'HouseholdsurveyArray');
                    $a = new $class_name;
                    get_db_fields($a->get_array());
                    $query .= "`householdsurvey_order` smallint(2) NOT NULL,";
                    $query .= "`householdsurvey_status` enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A -aktiv, I - inaktiv',";
                    $query .= "PRIMARY KEY (`householdsurvey_id`),";
                    $query .= "KEY `client_id` (`client_id`),";
                    $query .= "KEY `householdsurvey_id` (`householdsurvey_id`)";
                    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
                    handle_table($query,$client_item['client_url'].'_householdsurvey');
                    break;

                case 'survey1':
                    //survey1
                    $query  = "CREATE TABLE IF NOT EXISTS `".$client_item['client_url']."_survey1` (";
                    $query .= "`survey1_id` int(11) NOT NULL AUTO_INCREMENT,";
                    $class_name = ucfirst($client_item['client_url'].'Survey1Array');
                    $a = new $class_name;
                    get_db_fields($a->get_array());
                    $query .= "`survey1_date` datetime NOT NULL,";
                    $query .= "PRIMARY KEY (`survey1_id`)";
                    $query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
                    handle_table($query,$client_item['client_url'].'survey1');
                    break;

                default:
                    break;
            }
        }
    }
}


/*----------------------------------------------------------------------------------------------------------------------
    Table Content
----------------------------------------------------------------------------------------------------------------------*/


echo '&nbsp;'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";
echo '<b>Abf&uuml;llen der Daten </b>'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";

// Mandaten
$query  = "INSERT INTO  `client` (`client_id` ,`client_name` ,`client_url`, `client_mail` ,`client_status`) VALUES";
foreach($client as $client_key => $client_item ) {
    $query .= "(NULL , '".$client_item['client_name']."', '".$client_item['client_url']."', '".$client_item['client_mail']."',  'A'),";
}
handle_table($query,'client','insert');

// Benutzer
$query  = "INSERT INTO `user` (`client_id`,`user_username`, `user_email`,`user_password`, `user_loginlevel`,`user_status`) VALUES ";
$query .= "('', 'corinna.heye@raumdaten.ch', 'corinna.heye@raumdaten.ch', 'beetester45', 3, 'A'),";
$query .= "('','info@pan-x.ch','info@pan-x.ch', 'beetester45', 3, 'A'),";
$query .= "('','swiederh','sw@regeo.ch', 'solala', 3, 'A');";
handle_table($query,'user','insert');

//building
$query  = "INSERT INTO `building` (`client_id`, `building_object`, `building_title`, `building_adress`)
VALUES
    ('3',104,'Hagenholzstrasse 104','Hagenholzstrasse 104a'),
    ('3',106,'Hagenholzstrasse 106','Hagenholzstrasse 106a'),
    ('3',108,'Hagenholzstrasse 108','Hagenholzstrasse 108a'),
    ('3',205,'Genossenschaftsstrasse 5','Genossenschaftsstrasse 5'),
    ('3',211,'Genossenschaftsstrasse 11','Genossenschaftsstrasse 11'),
    ('3',213,'Genossenschaftsstrasse 13','Genossenschaftsstrasse 13'),
    ('3',216,'Genossenschaftsstrasse 16','Genossenschaftsstrasse 16'),
    ('3',218,'Genossenschaftsstrasse 18','Genossenschaftsstrasse 18'),
    ('3',302,'Dialogweg 2','Dialogweg 2'),
    ('3',303,'Dialogweg 3','Dialogweg 3'),
    ('3',306,'Dialogweg 6','Dialogweg 6'),
    ('3',307,'Dialogweg 7','Dialogweg 7'),
    ('3',311,'Dialogweg 11','Dialogweg 11')";
handle_table($query,'building','insert');

// apartment
$query  = "INSERT INTO `apartment` (`apartment_id`, `client_id`, `building_id`, `apartment_object`, `apartment_title`, `apartment_floor`, `apartment_type`, `apartment_rooms`, `apartment_area`, `apartment_balcony`, `apartment_rentalgross`, `apartment_participation`, `apartment_subvention`, `apartment_reserved`, `apartment_status`) VALUES
(1, 3, 104, 1201, '104-1201', '2', 'Wohnung', 4.5, 109, 18, 2042, 27500, 1, 0, 'A'),
(2, 3, 104, 1202, '104-1202', '2', 'Wohnung', 3.5, 78, 10, 1511, 19500, 1, 0, 'A'),
(3, 3, 104, 1203, '104-1203', '2', 'Wohnung', 6.5, 141, 18, 2407, 35500, 1, 0, 'A'),
(4, 3, 104, 1204, '104-1204', '2', 'Wohnung', 3.5, 82, 21, 1638, 20500, 0, 0, 'A'),
(5, 3, 104, 1205, '104-1205', '2', 'Wohnung', 4.5, 94, 20, 1832, 23500, 0, 0, 'A'),
(6, 3, 104, 1206, '104-1206', '2', 'Wohnung', 4.5, 105, 10, 1930, 26500, 1, 0, 'A'),
(7, 3, 104, 1301, '104-1301', '3', 'Wohnung', 4.5, 109, 15, 2064, 27500, 0, 0, 'A'),
(8, 3, 104, 1302, '104-1302', '3', 'Wohnung', 3.5, 78, 11, 1543, 19500, 0, 0, 'A'),
(9, 3, 104, 1303, '104-1303', '3', 'Wohnung', 6.5, 141, 17, 2451, 35500, 1, 0, 'A'),
(10, 3, 104, 1304, '104-1304', '3', 'Wohnung', 3.5, 82, 20, 1663, 20500, 0, 0, 'A'),
(11, 3, 104, 1305, '104-1305', '3', 'Wohnung', 4.5, 94, 23, 1879, 23500, 0, 0, 'A'),
(12, 3, 104, 1306, '104-1306', '3', 'Wohnung', 4.5, 105, 10, 1966, 26500, 1, 0, 'A'),
(13, 3, 104, 1401, '104-1401', '4', 'Wohnung', 4.5, 109, 15, 2102, 27500, 0, 0, 'A'),
(14, 3, 104, 1402, '104-1402', '4', 'Wohnung', 3.5, 78, 10, 1565, 19500, 0, 0, 'A'),
(15, 3, 104, 1403, '104-1403', '4', 'Wohnung', 6.5, 141, 14, 2483, 35500, 1, 0, 'A'),
(16, 3, 104, 1404, '104-1404', '4', 'Wohnung', 3.5, 82, 23, 1711, 20500, 0, 0, 'A'),
(17, 3, 104, 1405, '104-1405', '4', 'Wohnung', 4.5, 94, 19, 1895, 23500, 0, 0, 'A'),
(18, 3, 104, 1406, '104-1406', '4', 'Wohnung', 4.5, 105, 10, 2002, 26500, 1, 0, 'A'),
(19, 3, 104, 1501, '104-1501', '5', 'Wohnung', 4.5, 109, 18, 2157, 27500, 0, 0, 'A'),
(20, 3, 104, 1502, '104-1502', '5', 'Wohnung', 3.5, 78, 10, 1592, 19500, 0, 0, 'A'),
(21, 3, 104, 1503, '104-1503', '5', 'Wohnung', 6.5, 141, 18, 2552, 35500, 1, 0, 'A'),
(22, 3, 104, 1504, '104-1504', '5', 'Wohnung', 3.5, 82, 21, 1726, 20500, 0, 0, 'A'),
(23, 3, 104, 1505, '104-1505', '5', 'Wohnung', 4.5, 94, 20, 1932, 23500, 0, 0, 'A'),
(24, 3, 104, 1506, '104-1506', '5', 'Wohnung', 4.5, 105, 10, 2039, 26500, 0, 0, 'A'),
(25, 3, 104, 1601, '104-1601', '6', 'Wohnung', 4.5, 109, 15, 2177, 27500, 0, 0, 'A'),
(26, 3, 104, 1602, '104-1602', '6', 'Wohnung', 3.5, 78, 11, 1624, 19500, 0, 0, 'A'),
(27, 3, 104, 1603, '104-1603', '6', 'Wohnung', 6.5, 141, 17, 2597, 35500, 1, 0, 'A'),
(28, 3, 104, 1604, '104-1604', '6', 'Wohnung', 3.5, 82, 20, 1750, 20500, 0, 0, 'A'),
(29, 3, 104, 1605, '104-1605', '6', 'Wohnung', 4.5, 94, 23, 1979, 23500, 0, 0, 'A'),
(30, 3, 104, 1606, '104-1606', '6', 'Wohnung', 4.5, 105, 10, 2075, 26500, 0, 0, 'A'),
(31, 3, 106, 1101, '106-1101', '1', 'Wohnung', 3.5, 86, 38, 1777, 21500, 0, 0, 'A'),
(32, 3, 106, 1102, '106-1102', '1', 'Wohnung', 4.5, 101, 38, 1958, 25500, 1, 0, 'A'),
(33, 3, 106, 1103, '106-1103', '1', 'Wohnung', 5.5, 118, 38, 2084, 29500, 0, 0, 'A'),
(34, 3, 106, 1104, '106-1104', '1', 'Wohnung', 7.5, 178, 38, 3315, 44500, 0, 0, 'A'),
(35, 3, 106, 1201, '106-1201', '2', 'Wohnung', 3.5, 86, 15, 1677, 21500, 0, 0, 'A'),
(36, 3, 106, 1202, '106-1202', '2', 'Wohnung', 4.5, 101, 18, 1886, 25500, 0, 0, 'A'),
(37, 3, 106, 1203, '106-1203', '2', 'Wohnung', 5.5, 118, 9, 1983, 29500, 1, 0, 'A'),
(38, 3, 106, 1204, '106-1204', '2', 'Wohnung', 2.5, 57, 12, 1172, 14500, 0, 0, 'A'),
(39, 3, 106, 1205, '106-1205', '2', 'Wohnung', 4.5, 105, 12, 1937, 26500, 0, 0, 'A'),
(40, 3, 106, 1301, '106-1301', '3', 'Wohnung', 3.5, 86, 13, 1699, 21500, 0, 0, 'A'),
(41, 3, 106, 1302, '106-1302', '3', 'Wohnung', 4.5, 101, 16, 1913, 25500, 0, 0, 'A'),
(42, 3, 106, 1303, '106-1303', '3', 'Wohnung', 5.5, 118, 9, 2023, 29500, 1, 0, 'A'),
(43, 3, 106, 1304, '106-1304', '3', 'Wohnung', 2.5, 57, 11, 1183, 14500, 0, 0, 'A'),
(44, 3, 106, 1305, '106-1305', '3', 'Wohnung', 4.5, 105, 12, 1973, 26500, 0, 0, 'A'),
(45, 3, 106, 1401, '106-1401', '4', 'Wohnung', 3.5, 86, 15, 1692, 21500, 0, 0, 'A'),
(46, 3, 106, 1402, '106-1402', '4', 'Wohnung', 4.5, 101, 18, 1956, 25500, 0, 0, 'A'),
(47, 3, 106, 1403, '106-1403', '4', 'Wohnung', 5.5, 118, 9, 2063, 29500, 1, 0, 'A'),
(48, 3, 106, 1404, '106-1404', '4', 'Wohnung', 2.5, 57, 12, 1212, 14500, 0, 0, 'A'),
(49, 3, 106, 1405, '106-1405', '4', 'Wohnung', 4.5, 105, 12, 2009, 26500, 0, 0, 'A'),
(50, 3, 106, 1501, '106-1501', '5', 'Wohnung', 3.5, 86, 13, 1758, 21500, 0, 0, 'A'),
(51, 3, 106, 1502, '106-1502', '5', 'Wohnung', 4.5, 101, 16, 1983, 25500, 0, 0, 'A'),
(52, 3, 106, 1503, '106-1503', '5', 'Wohnung', 5.5, 118, 9, 2104, 29500, 1, 0, 'A'),
(53, 3, 106, 1504, '106-1504', '5', 'Wohnung', 2.5, 57, 11, 1223, 14500, 0, 0, 'A'),
(54, 3, 106, 1505, '106-1505', '5', 'Wohnung', 4.5, 105, 12, 2045, 26500, 0, 0, 'A'),
(55, 3, 106, 1601, '106-1601', '6', 'Wohnung', 3.5, 86, 15, 1797, 21500, 0, 0, 'A'),
(56, 3, 106, 1602, '106-1602', '6', 'Wohnung', 4.5, 101, 18, 2027, 25500, 0, 0, 'A'),
(57, 3, 106, 1603, '106-1603', '6', 'Wohnung', 5.5, 118, 9, 2144, 29500, 1, 0, 'A'),
(58, 3, 106, 1604, '106-1604', '6', 'Wohnung', 2.5, 57, 12, 1252, 14500, 0, 0, 'A'),
(59, 3, 106, 1605, '106-1605', '6', 'Wohnung', 4.5, 105, 12, 2082, 26500, 0, 0, 'A'),
(60, 3, 108, 1001, '108-1001', 'P', 'Wohnung', 2.5, 58, 18, 1262, 14500, 0, 0, 'A'),
(61, 3, 108, 1002, '108-1002', 'P', 'Wohnung', 2.5, 47, 17, 1023, 12000, 0, 0, 'A'),
(62, 3, 108, 1003, '108-1003', 'P', 'Wohnung', 2.5, 57, 17, 1223, 14500, 0, 0, 'A'),
(63, 3, 108, 1004, '108-1004', 'P', 'Wohnung', 2.5, 48, 17, 1049, 12000, 0, 0, 'A'),
(64, 3, 108, 1005, '108-1005', 'P', 'Wohnatelier', 4.5, 98, 32, 2014, 24500, 0, 0, 'A'),
(65, 3, 108, 1101, '108-1101', '1', 'Wohnung', 2.5, 58, 18, 1282, 14500, 0, 0, 'A'),
(66, 3, 108, 1102, '108-1102', '1', 'Wohnung', 2.5, 47, 17, 1040, 12000, 0, 0, 'A'),
(67, 3, 108, 1103, '108-1103', '1', 'Wohnung', 2.5, 57, 17, 1243, 14500, 0, 0, 'A'),
(68, 3, 108, 1104, '108-1104', '1', 'Wohnung', 2.5, 48, 17, 1066, 12000, 0, 0, 'A'),
(69, 3, 108, 1105, '108-1105', '1', 'Wohnatelier', 4.5, 98, 31, 2049, 24500, 0, 0, 'A'),
(70, 3, 108, 1201, '108-1201', '2', 'Wohnung', 3.5, 78, 10, 1506, 19500, 0, 0, 'A'),
(71, 3, 108, 1202, '108-1202', '2', 'Wohnung', 4.5, 96, 10, 1723, 24000, 1, 0, 'A'),
(72, 3, 108, 1203, '108-1203', '2', 'Wohnung', 3.5, 75, 28, 1547, 19000, 0, 0, 'A'),
(73, 3, 108, 1204, '108-1204', '2', 'Wohnung', 2.5, 58, 19, 1243, 14500, 1, 0, 'A'),
(74, 3, 108, 1205, '108-1205', '2', 'Wohnung', 4.5, 96, 19, 1820, 24000, 0, 0, 'A'),
(75, 3, 108, 1206, '108-1206', '2', 'Wohnung', 4.5, 97, 19, 1830, 24500, 0, 0, 'A'),
(76, 3, 108, 1207, '108-1207', '2', 'Wohnung', 2.5, 60, 24, 1312, 15000, 1, 0, 'A'),
(77, 3, 108, 1301, '108-1301', '3', 'Wohnung', 3.5, 78, 10, 1539, 19500, 0, 0, 'A'),
(78, 3, 108, 1302, '108-1302', '3', 'Wohnung', 4.5, 97, 11, 1763, 24500, 1, 0, 'A'),
(79, 3, 108, 1303, '108-1303', '3', 'Wohnung', 3.5, 75, 9, 1467, 19000, 0, 0, 'A'),
(80, 3, 108, 1304, '108-1304', '3', 'Wohnung', 2.5, 58, 9, 1206, 14500, 1, 0, 'A'),
(81, 3, 108, 1305, '108-1305', '3', 'Wohnung', 4.5, 97, 9, 1804, 24500, 0, 0, 'A'),
(82, 3, 108, 1306, '108-1306', '3', 'Wohnung', 4.5, 97, 9, 1812, 24500, 1, 0, 'A'),
(83, 3, 108, 1307, '108-1307', '3', 'Wohnung', 2.5, 60, 9, 1250, 15000, 1, 0, 'A'),
(84, 3, 108, 1401, '108-1401', '4', 'Wohnung', 3.5, 78, 10, 1566, 19500, 0, 0, 'A'),
(85, 3, 108, 1402, '108-1402', '4', 'Wohnung', 4.5, 97, 11, 1797, 24500, 0, 0, 'A'),
(86, 3, 108, 1403, '108-1403', '4', 'Wohnung', 3.5, 75, 9, 1493, 19000, 0, 0, 'A'),
(87, 3, 108, 1404, '108-1404', '4', 'Wohnung', 2.5, 58, 9, 1227, 14500, 0, 0, 'A'),
(88, 3, 108, 1405, '108-1405', '4', 'Wohnung', 4.5, 97, 9, 1838, 24500, 0, 0, 'A'),
(89, 3, 108, 1406, '108-1406', '4', 'Wohnung', 4.5, 97, 9, 1845, 24500, 0, 0, 'A'),
(90, 3, 108, 1407, '108-1407', '4', 'Wohnung', 2.5, 60, 9, 1271, 15000, 0, 0, 'A'),
(91, 3, 108, 1501, '108-1501', '5', 'Wohnung', 3.5, 78, 10, 1593, 19500, 0, 0, 'A'),
(92, 3, 108, 1502, '108-1502', '5', 'Wohnung', 4.5, 97, 11, 1830, 24500, 0, 0, 'A'),
(93, 3, 108, 1503, '108-1503', '5', 'Wohnung', 3.5, 75, 9, 1519, 19000, 0, 0, 'A'),
(94, 3, 108, 1504, '108-1504', '5', 'Wohnung', 2.5, 58, 9, 1247, 14500, 0, 0, 'A'),
(95, 3, 108, 1505, '108-1505', '5', 'Wohnung', 4.5, 97, 9, 1871, 24500, 0, 0, 'A'),
(96, 3, 108, 1506, '108-1506', '5', 'Wohnung', 4.5, 97, 9, 1878, 24500, 0, 0, 'A'),
(97, 3, 108, 1507, '108-1507', '5', 'Wohnung', 2.5, 60, 9, 1292, 15000, 0, 0, 'A'),
(98, 3, 108, 1601, '108-1601', '6', 'Wohnung', 3.5, 78, 10, 1620, 19500, 0, 0, 'A'),
(99, 3, 108, 1602, '108-1602', '6', 'Wohnung', 4.5, 97, 11, 1863, 24500, 0, 0, 'A'),
(100, 3, 108, 1603, '108-1603', '6', 'Wohnung', 3.5, 75, 9, 1545, 19000, 0, 0, 'A'),
(101, 3, 108, 1604, '108-1604', '6', 'Wohnung', 2.5, 58, 9, 1267, 14500, 0, 0, 'A'),
(102, 3, 108, 1605, '108-1605', '6', 'Wohnung', 4.5, 97, 9, 1904, 24500, 0, 0, 'A'),
(103, 3, 108, 1606, '108-1606', '6', 'Wohnung', 4.5, 97, 9, 1911, 24500, 0, 0, 'A'),
(104, 3, 108, 1607, '108-1607', '6', 'Wohnung', 2.5, 60, 9, 1313, 15000, 0, 0, 'A'),
(105, 3, 205, 1101, '205-1101', '1', 'Wohnung', 4.5, 94, 9, 1695, 23500, 1, 0, 'A'),
(106, 3, 205, 1102, '205-1102', '1', 'Wohnung', 4.5, 106, 11, 1916, 26500, 1, 0, 'A'),
(107, 3, 205, 1103, '205-1103', '1', 'Wohnung', 4.5, 108, 10, 1940, 27000, 0, 0, 'A'),
(108, 3, 205, 1104, '205-1104', '1', 'Wohnung', 3.5, 90, 9, 1747, 22500, 0, 0, 'A'),
(109, 3, 205, 1105, '205-1105', '1', 'Wohnung', 3.5, 81, 9, 1538, 20500, 1, 0, 'A'),
(110, 3, 205, 1107, '205-1107', '1', 'Wohnung', 3.5, 81, 10, 1487, 20500, 1, 0, 'A'),
(111, 3, 205, 1201, '205-1201', '2', 'Wohnung', 4.5, 94, 9, 1728, 23500, 1, 0, 'A'),
(112, 3, 205, 1202, '205-1202', '2', 'Wohnung', 4.5, 106, 11, 1952, 26500, 1, 0, 'A'),
(113, 3, 205, 1203, '205-1203', '2', 'Wohnung', 4.5, 108, 10, 1977, 27000, 0, 0, 'A'),
(114, 3, 205, 1204, '205-1204', '2', 'Wohnung', 3.5, 90, 9, 1778, 22500, 0, 0, 'A'),
(115, 3, 205, 1205, '205-1205', '2', 'Wohnung', 3.5, 81, 9, 1566, 20500, 0, 0, 'A'),
(116, 3, 205, 1207, '205-1207', '2', 'Wohnung', 3.5, 81, 10, 1517, 20500, 0, 0, 'A'),
(117, 3, 205, 1301, '205-1301', '3', 'Wohnung', 4.5, 94, 9, 1760, 23500, 0, 0, 'A'),
(118, 3, 205, 1302, '205-1302', '3', 'Wohnung', 4.5, 106, 11, 1989, 26500, 1, 0, 'A'),
(119, 3, 205, 1303, '205-1303', '3', 'Wohnung', 4.5, 108, 10, 2014, 27000, 0, 0, 'A'),
(120, 3, 205, 1304, '205-1304', '3', 'Wohnung', 3.5, 90, 9, 1810, 22500, 0, 0, 'A'),
(121, 3, 205, 1305, '205-1305', '3', 'Wohnung', 3.5, 81, 9, 1594, 20500, 0, 0, 'A'),
(122, 3, 205, 1307, '205-1307', '3', 'Wohnung', 3.5, 81, 10, 1543, 20500, 0, 0, 'A'),
(123, 3, 205, 1401, '205-1401', '4', 'Wohnung', 4.5, 94, 9, 1792, 23500, 0, 0, 'A'),
(124, 3, 205, 1402, '205-1402', '4', 'Wohnung', 4.5, 106, 11, 2025, 26500, 0, 0, 'A'),
(125, 3, 205, 1403, '205-1403', '4', 'Wohnung', 4.5, 108, 10, 2051, 27000, 0, 0, 'A'),
(126, 3, 205, 1404, '205-1404', '4', 'Wohnung', 3.5, 90, 9, 1841, 22500, 0, 0, 'A'),
(127, 3, 205, 1405, '205-1405', '4', 'Wohnung', 3.5, 81, 9, 1625, 20500, 0, 0, 'A'),
(128, 3, 205, 1407, '205-1407', '4', 'Wohnung', 3.5, 81, 10, 1572, 20500, 0, 0, 'A'),
(129, 3, 205, 1501, '205-1501', '5', 'Wohnung', 4.5, 94, 10, 1828, 23500, 0, 0, 'A'),
(130, 3, 205, 1502, '205-1502', '5', 'Wohnung', 4.5, 106, 11, 2062, 26500, 0, 0, 'A'),
(131, 3, 205, 1503, '205-1503', '5', 'Wohnung', 4.5, 108, 10, 2088, 27000, 0, 0, 'A'),
(132, 3, 205, 1504, '205-1504', '5', 'Wohnung', 3.5, 90, 9, 1871, 22500, 0, 0, 'A'),
(133, 3, 205, 1505, '205-1505', '5', 'Wohnung', 3.5, 81, 9, 1653, 20500, 0, 0, 'A'),
(134, 3, 205, 1507, '205-1507', '5', 'Wohnung', 3.5, 81, 10, 1598, 20500, 0, 0, 'A'),
(135, 3, 211, 1001, '211-1001', 'P', 'Studio', 1, 32, 0, 660, 8000, 0, 0, 'A'),
(136, 3, 211, 1002, '211-1002', 'P', 'Studio', 1, 37, 0, 767, 9500, 0, 0, 'A'),
(137, 3, 211, 1101, '211-1101', '1', 'Wohnung', 3.5, 81, 0, 1441, 20500, 1, 0, 'A'),
(138, 3, 211, 1102, '211-1102', '1', 'Wohnung', 3.5, 81, 0, 1437, 20500, 0, 0, 'A'),
(139, 3, 211, 1103, '211-1103', '1', 'Wohnung', 4.5, 96, 0, 1673, 24000, 0, 0, 'A'),
(140, 3, 211, 1104, '211-1104', '1', 'Wohnung', 4.5, 100, 0, 1745, 25000, 0, 0, 'A'),
(141, 3, 211, 1105, '211-1105', '1', 'Wohnung', 5.5, 116, 0, 1928, 29000, 1, 0, 'A'),
(142, 3, 211, 1106, '211-1106', '1', 'Wohnung', 4.5, 99, 0, 1683, 25000, 0, 0, 'A'),
(143, 3, 211, 1201, '211-1201', '2', 'Wohnung', 3.5, 81, 0, 1468, 20500, 0, 0, 'A'),
(144, 3, 211, 1202, '211-1202', '2', 'Wohnung', 3.5, 81, 0, 1464, 20500, 0, 0, 'A'),
(145, 3, 211, 1203, '211-1203', '2', 'Wohnung', 4.5, 96, 0, 1705, 24000, 0, 0, 'A'),
(146, 3, 211, 1204, '211-1204', '2', 'Wohnung', 4.5, 100, 0, 1778, 25000, 0, 0, 'A'),
(147, 3, 211, 1205, '211-1205', '2', 'Wohnung', 5.5, 116, 0, 1967, 29000, 1, 0, 'A'),
(148, 3, 211, 1206, '211-1206', '2', 'Wohnung', 4.5, 99, 0, 1716, 25000, 0, 0, 'A'),
(149, 3, 211, 1301, '211-1301', '3', 'Wohnung', 3.5, 81, 0, 1495, 20500, 1, 0, 'A'),
(150, 3, 211, 1302, '211-1302', '3', 'Wohnung', 3.5, 81, 0, 1493, 20500, 0, 0, 'A'),
(151, 3, 211, 1303, '211-1303', '3', 'Wohnung', 4.5, 96, 0, 1737, 24000, 0, 0, 'A'),
(152, 3, 211, 1304, '211-1304', '3', 'Wohnung', 5.5, 116, 0, 2005, 29000, 0, 0, 'A'),
(153, 3, 211, 1305, '211-1305', '3', 'Wohnung', 4.5, 99, 0, 1749, 25000, 0, 0, 'A'),
(154, 3, 211, 1401, '211-1401', '4', 'Wohnung', 3.5, 81, 0, 1522, 20500, 0, 0, 'A'),
(155, 3, 211, 1402, '211-1402', '4', 'Wohnung', 3.5, 81, 0, 1520, 20500, 0, 0, 'A'),
(156, 3, 211, 1403, '211-1403', '4', 'Wohnung', 4.5, 96, 0, 1769, 24000, 0, 0, 'A'),
(157, 3, 211, 1404, '211-1404', '4', 'Wohnung', 5.5, 116, 0, 2044, 29000, 0, 0, 'A'),
(158, 3, 211, 1405, '211-1405', '4', 'Wohnung', 4.5, 99, 0, 1782, 25000, 0, 0, 'A'),
(159, 3, 213, 1101, '213-1101', '1', 'Wohnung', 6.5, 135, 5, 2283, 34000, 0, 1, 'A'),
(160, 3, 213, 1102, '213-1102', '1', 'Wohnung', 6.5, 132, 4, 2282, 33000, 0, 1, 'A'),
(161, 3, 213, 1103, '213-1103', '1', 'Wohnung', 5.5, 125, 4, 2175, 31500, 0, 1, 'A'),
(162, 3, 213, 1104, '213-1104', '1', 'Wohnung', 5.5, 132, 4, 2361, 33000, 0, 1, 'A'),
(163, 3, 213, 1105, '213-1105', '1', 'WG (Maisonette)', 12.5, 317, 6, 5571, 79500, 0, 1, 'A'),
(164, 3, 213, 1201, '213-1201', '2', 'Wohnung', 4.5, 112, 4, 2015, 28000, 1, 0, 'A'),
(165, 3, 213, 1203, '213-1203', '2', 'Wohnung', 4.5, 112, 3, 2069, 28000, 1, 0, 'A'),
(166, 3, 213, 1204, '213-1204', '2', 'Wohnung', 4.5, 116, 3, 2142, 29000, 0, 0, 'A'),
(167, 3, 213, 1205, '213-1205', '2', 'Wohnung', 4.5, 122, 3, 2306, 30500, 0, 0, 'A'),
(168, 3, 213, 1301, '213-1301', '3', 'Wohnung', 4.5, 112, 4, 2047, 28000, 1, 0, 'A'),
(169, 3, 213, 1303, '213-1303', '3', 'Wohnung', 4.5, 113, 3, 2123, 28500, 1, 0, 'A'),
(170, 3, 213, 1304, '213-1304', '3', 'Wohnung', 4.5, 116, 3, 2188, 29000, 0, 0, 'A'),
(171, 3, 213, 1305, '213-1305', '3', 'Wohnung', 4.5, 122, 3, 2362, 30500, 0, 0, 'A'),
(172, 3, 213, 1306, '213-1306', '3', 'WG (Maisonette)', 12.5, 304, 6, 5551, 76000, 0, 0, 'A'),
(173, 3, 213, 1401, '213-1401', '4', 'Wohnung', 4.5, 113, 4, 2103, 28500, 0, 0, 'A'),
(174, 3, 213, 1403, '213-1403', '4', 'Wohnung', 4.5, 114, 3, 2190, 28500, 1, 0, 'A'),
(175, 3, 213, 1404, '213-1404', '4', 'Wohnung', 4.5, 116, 3, 2286, 29000, 0, 0, 'A'),
(176, 3, 213, 1405, '213-1405', '4', 'Wohnung', 4.5, 122, 3, 2342, 30500, 0, 0, 'A'),
(177, 3, 213, 1501, '213-1501', '5', 'Wohnung', 5.5, 125, 4, 2260, 31500, 0, 0, 'A'),
(178, 3, 213, 1502, '213-1502', '5', 'Wohnung', 5.5, 122, 3, 2273, 30500, 0, 0, 'A'),
(179, 3, 213, 1503, '213-1503', '5', 'Wohnung', 4.5, 116, 3, 2267, 29000, 0, 0, 'A'),
(180, 3, 213, 1504, '213-1504', '5', 'Wohnung', 4.5, 122, 3, 2444, 30500, 0, 0, 'A'),
(181, 3, 213, 1505, '213-1505', '5', 'WG (Maisonette)', 12.5, 304, 6, 5907, 76000, 0, 0, 'A'),
(182, 3, 213, 1601, '213-1601', '6', 'Wohnung', 5.5, 126, 4, 2321, 31500, 0, 0, 'A'),
(183, 3, 213, 1602, '213-1602', '6', 'Wohnung', 5.5, 122, 3, 2313, 30500, 1, 0, 'A'),
(184, 3, 213, 1603, '213-1603', '6', 'Wohnung', 4.5, 116, 3, 2306, 29000, 0, 0, 'A'),
(185, 3, 213, 1604, '213-1604', '6', 'Wohnung', 4.5, 122, 3, 2485, 30500, 0, 0, 'A'),
(186, 3, 216, 1101, '216-1101', '1', 'Wohnung', 6.5, 152, 20, 2557, 38000, 1, 0, 'A'),
(187, 3, 216, 1102, '216-1102', '1', 'Wohnung', 4.5, 110, 21, 2033, 27500, 0, 0, 'A'),
(188, 3, 216, 1103, '216-1103', '1', 'Wohnung', 3.5, 82, 9, 1546, 20500, 1, 0, 'A'),
(189, 3, 216, 1104, '216-1104', '1', 'Wohnung', 4.5, 111, 19, 2089, 28000, 1, 0, 'A'),
(190, 3, 216, 1105, '216-1105', '1', 'Wohnung', 5.5, 135, 16, 2333, 34000, 0, 0, 'A'),
(191, 3, 216, 1201, '216-1201', '2', 'Wohnung', 6.5, 152, 20, 2610, 38000, 1, 0, 'A'),
(192, 3, 216, 1202, '216-1202', '2', 'Wohnung', 4.5, 110, 21, 2072, 27500, 0, 0, 'A'),
(193, 3, 216, 1203, '216-1203', '2', 'Wohnung', 3.5, 82, 9, 1574, 20500, 1, 0, 'A'),
(194, 3, 216, 1204, '216-1204', '2', 'Wohnung', 4.5, 111, 19, 2128, 28000, 1, 0, 'A'),
(195, 3, 216, 1205, '216-1205', '2', 'Wohnung', 5.5, 135, 19, 2393, 34000, 0, 0, 'A'),
(196, 3, 216, 1206, '216-1206', '2', 'Studio', 1, 36, 0, 750, 9000, 0, 0, 'A'),
(197, 3, 216, 1301, '216-1301', '3', 'Wohnung', 6.5, 152, 20, 2663, 38000, 1, 0, 'A'),
(198, 3, 216, 1302, '216-1302', '3', 'Wohnung', 4.5, 110, 21, 2111, 27500, 0, 0, 'A'),
(199, 3, 216, 1303, '216-1303', '3', 'Wohnung', 3.5, 82, 9, 1602, 20500, 0, 0, 'A'),
(200, 3, 216, 1304, '216-1304', '3', 'Wohnung', 4.5, 111, 19, 2167, 28000, 0, 0, 'A'),
(201, 3, 216, 1305, '216-1305', '3', 'Wohnung', 5.5, 135, 19, 2440, 34000, 0, 0, 'A'),
(202, 3, 216, 1306, '216-1306', '3', 'Studio', 1, 36, 0, 762, 9000, 0, 0, 'A'),
(203, 3, 216, 1401, '216-1401', '4', 'Wohnung', 6.5, 152, 20, 2715, 38000, 1, 0, 'A'),
(204, 3, 216, 1402, '216-1402', '4', 'Wohnung', 4.5, 110, 21, 2149, 27500, 0, 0, 'A'),
(205, 3, 216, 1403, '216-1403', '4', 'Wohnung', 3.5, 82, 9, 1630, 20500, 0, 0, 'A'),
(206, 3, 216, 1404, '216-1404', '4', 'Wohnung', 4.5, 111, 19, 2206, 28000, 0, 0, 'A'),
(207, 3, 216, 1405, '216-1405', '4', 'Wohnung', 5.5, 135, 19, 2487, 34000, 0, 0, 'A'),
(208, 3, 216, 1406, '216-1406', '4', 'Studio', 1, 36, 0, 774, 9000, 0, 0, 'A'),
(209, 3, 216, 1501, '216-1501', '5', 'Wohnung', 6.5, 152, 20, 2768, 38000, 0, 0, 'A'),
(210, 3, 216, 1502, '216-1502', '5', 'Wohnung', 4.5, 110, 21, 2188, 27500, 0, 0, 'A'),
(211, 3, 216, 1503, '216-1503', '5', 'Wohnung', 3.5, 82, 9, 1658, 20500, 0, 0, 'A'),
(212, 3, 216, 1504, '216-1504', '5', 'Wohnung', 4.5, 111, 19, 2244, 28000, 0, 0, 'A'),
(213, 3, 216, 1505, '216-1505', '5', 'Wohnung', 5.5, 135, 19, 2534, 34000, 0, 0, 'A'),
(214, 3, 216, 1506, '216-1506', '5', 'Studio', 1, 36, 0, 787, 9000, 0, 0, 'A'),
(215, 3, 218, 1101, '218-1101', '1', 'Wohnung', 5.5, 125, 11, 2199, 31500, 0, 1, 'A'),
(216, 3, 218, 1102, '218-1102', '1', 'Wohnung', 6.5, 144, 11, 2455, 36000, 0, 1, 'A'),
(217, 3, 218, 1103, '218-1103', '1', 'Wohnung', 4.5, 103, 12, 1811, 26000, 1, 0, 'A'),
(218, 3, 218, 1104, '218-1104', '1', 'WG (Maisonette)', 9.5, 254, 13, 4512, 63500, 0, 1, 'A'),
(219, 3, 218, 1105, '218-1105', '1', 'WG (Maisonette)', 9.5, 244, 13, 4333, 61000, 0, 1, 'A'),
(220, 3, 218, 1201, '218-1201', '2', 'Wohnung', 5.5, 125, 11, 2242, 31500, 0, 1, 'A'),
(221, 3, 218, 1202, '218-1202', '2', 'Wohnung', 6.5, 144, 11, 2504, 36000, 0, 1, 'A'),
(222, 3, 218, 1203, '218-1203', '2', 'Wohnung', 4.5, 103, 12, 1847, 26000, 1, 0, 'A'),
(223, 3, 218, 1301, '218-1301', '3', 'Wohnung', 5.5, 125, 11, 2284, 31500, 0, 0, 'A'),
(224, 3, 218, 1302, '218-1302', '3', 'Wohnung', 6.5, 144, 11, 2553, 36000, 0, 0, 'A'),
(225, 3, 218, 1303, '218-1303', '3', 'Wohnung', 4.5, 103, 12, 1882, 26000, 0, 0, 'A'),
(226, 3, 218, 1304, '218-1304', '3', 'WG (Maisonette)', 8.5, 239, 30, 4494, 60000, 0, 0, 'A'),
(227, 3, 218, 1305, '218-1305', '3', 'WG (Maisonette)', 8.5, 228, 14, 4226, 57000, 0, 0, 'A'),
(228, 3, 218, 1401, '218-1401', '4', 'Wohnung', 5.5, 125, 11, 2327, 31500, 1, 0, 'A'),
(229, 3, 218, 1402, '218-1402', '4', 'Wohnung', 5.5, 129, 33, 2455, 32500, 0, 0, 'A'),
(230, 3, 218, 1403, '218-1403', '4', 'Wohnung', 4.5, 103, 12, 1918, 26000, 1, 0, 'A'),
(231, 3, 218, 1501, '218-1501', '5', 'Wohnung', 4.5, 114, 21, 2326, 28500, 0, 0, 'A'),
(232, 3, 218, 1502, '218-1502', '5', 'Wohnung', 5.5, 129, 16, 2409, 32500, 1, 0, 'A'),
(233, 3, 218, 1503, '218-1503', '5', 'Wohnung', 4.5, 103, 11, 1948, 26000, 1, 0, 'A'),
(234, 3, 218, 1504, '218-1504', '5', 'WG', 8.5, 230, 14, 4415, 57500, 0, 0, 'A'),
(235, 3, 302, 1001, '302-1001', 'P', 'Wohnatelier', 4.5, 122, 8, 2065, 30500, 0, 0, 'A'),
(236, 3, 302, 1002, '302-1002', 'P', 'Satellitenwohnung', 13, 335, 35, 6095, 84000, 0, 1, 'A'),
(237, 3, 302, 1101, '302-1101', '1', 'Satellitenwohnung', 13.5, 380, 65, 7188, 95000, 0, 0, 'A'),
(238, 3, 302, 1102, '302-1102', '1', 'Satellitenwohnung', 9.5, 273, 71, 5150, 68500, 0, 0, 'A'),
(239, 3, 302, 1201, '302-1201', '2', 'Wohnung', 4.5, 123, 8, 2162, 31000, 0, 0, 'A'),
(240, 3, 302, 1202, '302-1202', '2', 'Wohnung', 3.5, 87, 8, 1612, 22000, 1, 0, 'A'),
(241, 3, 302, 1203, '302-1203', '2', 'Wohnung', 4.5, 106, 11, 1940, 26500, 0, 0, 'A'),
(242, 3, 302, 1204, '302-1204', '2', 'Wohnung', 3.5, 85, 10, 1633, 21500, 1, 0, 'A'),
(243, 3, 302, 1205, '302-1205', '2', 'Wohnung', 4.5, 107, 11, 2013, 27000, 0, 0, 'A'),
(244, 3, 302, 1207, '302-1207', '2', 'Wohnung', 4.5, 107, 11, 1958, 27000, 1, 0, 'A'),
(245, 3, 302, 1301, '302-1301', '3', 'Wohnung', 4.5, 123, 8, 2204, 31000, 0, 0, 'A'),
(246, 3, 302, 1302, '302-1302', '3', 'Wohnung', 3.5, 87, 8, 1642, 22000, 0, 0, 'A'),
(247, 3, 302, 1303, '302-1303', '3', 'Wohnung', 4.5, 106, 11, 1977, 26500, 0, 0, 'A'),
(248, 3, 302, 1304, '302-1304', '3', 'Wohnung', 3.5, 85, 10, 1663, 21500, 1, 0, 'A'),
(249, 3, 302, 1305, '302-1305', '3', 'Wohnung', 4.5, 107, 11, 2050, 27000, 0, 0, 'A'),
(250, 3, 302, 1307, '302-1307', '3', 'Wohnung', 4.5, 107, 11, 1995, 27000, 1, 0, 'A'),
(251, 3, 302, 1401, '302-1401', '4', 'Wohnung', 4.5, 123, 8, 2246, 31000, 0, 0, 'A'),
(252, 3, 302, 1402, '302-1402', '4', 'Wohnung', 3.5, 87, 8, 1671, 22000, 0, 0, 'A'),
(253, 3, 302, 1403, '302-1403', '4', 'Wohnung', 4.5, 106, 11, 2013, 26500, 0, 0, 'A'),
(254, 3, 302, 1404, '302-1404', '4', 'Wohnung', 3.5, 85, 10, 1692, 21500, 0, 0, 'A'),
(255, 3, 302, 1405, '302-1405', '4', 'Wohnung', 4.5, 107, 11, 2086, 27000, 0, 0, 'A'),
(256, 3, 302, 1407, '302-1407', '4', 'Wohnung', 4.5, 107, 11, 2031, 27000, 0, 0, 'A'),
(257, 3, 303, 1001, '303-1001', 'P', 'Studio', 1, 44, 10, 932, 11000, 0, 0, 'A'),
(258, 3, 303, 1002, '303-1002', 'P', 'Wohnung', 2.5, 59, 10, 1129, 15000, 0, 0, 'A'),
(259, 3, 303, 1003, '303-1003', 'P', 'Wohnung', 4.5, 109, 10, 1927, 27500, 0, 0, 'A'),
(260, 3, 303, 1004, '303-1004', 'P', 'Studio', 1, 34, 10, 754, 8500, 0, 0, 'A'),
(261, 3, 303, 1005, '303-1005', 'P', 'Wohnung', 5.5, 125, 12, 2163, 31500, 0, 0, 'A'),
(262, 3, 303, 1006, '303-1006', 'P', 'Wohnung', 5.5, 126, 12, 2115, 31500, 0, 0, 'A'),
(263, 3, 303, 1101, '303-1101', '1', 'Wohnung', 4.5, 109, 10, 1899, 27500, 1, 0, 'A'),
(264, 3, 303, 1102, '303-1102', '1', 'Wohnung', 3.5, 90, 11, 1661, 22500, 1, 0, 'A'),
(265, 3, 303, 1103, '303-1103', '1', 'Wohnung', 2.5, 59, 10, 1150, 15000, 0, 0, 'A'),
(266, 3, 303, 1104, '303-1104', '1', 'Wohnung', 4.5, 109, 10, 1965, 27500, 1, 0, 'A'),
(267, 3, 303, 1105, '303-1105', '1', 'Wohnung', 4.5, 112, 10, 2006, 28000, 0, 0, 'A'),
(268, 3, 303, 1106, '303-1106', '1', 'Wohnung', 5.5, 125, 12, 2206, 31500, 0, 0, 'A'),
(269, 3, 303, 1107, '303-1107', '1', 'Wohnung', 5.5, 126, 12, 2158, 31500, 1, 0, 'A'),
(270, 3, 303, 1201, '303-1201', '2', 'Wohnung', 4.5, 109, 10, 1936, 27500, 0, 0, 'A'),
(271, 3, 303, 1202, '303-1202', '2', 'Wohnung', 3.5, 90, 11, 1692, 22500, 0, 0, 'A'),
(272, 3, 303, 1203, '303-1203', '2', 'Wohnung', 2.5, 59, 10, 1170, 15000, 0, 0, 'A'),
(273, 3, 303, 1204, '303-1204', '2', 'Wohnung', 4.5, 109, 10, 2003, 27500, 1, 0, 'A'),
(274, 3, 303, 1205, '303-1205', '2', 'Wohnung', 4.5, 112, 10, 2044, 28000, 0, 0, 'A'),
(275, 3, 303, 1206, '303-1206', '2', 'Wohnung', 5.5, 125, 12, 2249, 31500, 0, 0, 'A'),
(276, 3, 303, 1207, '303-1207', '2', 'Wohnung', 5.5, 126, 12, 2201, 31500, 1, 0, 'A'),
(277, 3, 303, 1301, '303-1301', '3', 'Wohnung', 4.5, 109, 10, 1973, 27500, 0, 0, 'A'),
(278, 3, 303, 1302, '303-1302', '3', 'Wohnung', 3.5, 90, 11, 1723, 22500, 0, 0, 'A'),
(279, 3, 303, 1303, '303-1303', '3', 'Wohnung', 2.5, 59, 10, 1191, 15000, 0, 0, 'A'),
(280, 3, 303, 1304, '303-1304', '3', 'Wohnung', 4.5, 109, 10, 2040, 27500, 0, 0, 'A'),
(281, 3, 303, 1305, '303-1305', '3', 'Wohnung', 4.5, 112, 10, 2082, 28000, 0, 0, 'A'),
(282, 3, 303, 1306, '303-1306', '3', 'Wohnung', 5.5, 125, 12, 2292, 31500, 0, 0, 'A'),
(283, 3, 303, 1307, '303-1307', '3', 'Wohnung', 5.5, 126, 12, 2244, 31500, 1, 0, 'A'),
(284, 3, 303, 1401, '303-1401', '4', 'Wohnung', 4.5, 109, 10, 2011, 27500, 0, 0, 'A'),
(285, 3, 303, 1402, '303-1402', '4', 'Wohnung', 3.5, 90, 10, 1754, 22500, 0, 0, 'A'),
(286, 3, 303, 1403, '303-1403', '4', 'Wohnung', 2.5, 59, 10, 1211, 15000, 0, 0, 'A'),
(287, 3, 303, 1404, '303-1404', '4', 'Wohnung', 4.5, 109, 10, 2076, 27500, 0, 0, 'A'),
(288, 3, 303, 1405, '303-1405', '4', 'Wohnung', 4.5, 112, 10, 2120, 28000, 0, 0, 'A'),
(289, 3, 303, 1406, '303-1406', '4', 'Wohnung', 5.5, 125, 12, 2334, 31500, 0, 0, 'A'),
(290, 3, 303, 1407, '303-1407', '4', 'Wohnung', 5.5, 124, 12, 2251, 31000, 1, 0, 'A'),
(291, 3, 306, 1001, '306-1001', 'P', 'Satellitenwohnung', 11.5, 313, 69, 6237, 78500, 0, 1, 'A'),
(292, 3, 306, 1101, '306-1101', '1', 'Satellitenwohnung', 12.5, 400, 36, 7600, 100000, 0, 0, 'A'),
(293, 3, 306, 1102, '306-1102', '1', 'Satellitenwohnung', 10.5, 325, 29, 6343, 81500, 0, 0, 'A'),
(294, 3, 306, 1201, '306-1201', '2', 'Satellitenwohnung', 12.5, 400, 36, 7737, 100000, 0, 0, 'A'),
(295, 3, 306, 1202, '306-1202', '2', 'Satellitenwohnung', 10.5, 325, 29, 6454, 81500, 0, 0, 'A'),
(296, 3, 306, 1301, '306-1301', '3', 'Satellitenwohnung', 12.5, 400, 36, 7873, 100000, 0, 0, 'A'),
(297, 3, 306, 1302, '306-1302', '3', 'Satellitenwohnung', 10.5, 325, 29, 6565, 81500, 0, 0, 'A'),
(298, 3, 306, 1401, '306-1401', '4', 'Satellitenwohnung', 12.5, 400, 36, 8010, 100000, 0, 0, 'A'),
(299, 3, 306, 1402, '306-1402', '4', 'Satellitenwohnung', 10.5, 325, 29, 6676, 81500, 0, 0, 'A'),
(300, 3, 306, 1501, '306-1501', '5', 'Satellitenwohnung', 12.5, 400, 36, 8147, 100000, 0, 0, 'A'),
(301, 3, 306, 1502, '306-1502', '5', 'Satellitenwohnung', 10.5, 325, 29, 6788, 81500, 0, 0, 'A'),
(302, 3, 307, 1001, '307-1001', 'P', 'WG', 9.5, 279, 34, 5403, 70000, 0, 0, 'A'),
(303, 3, 307, 1101, '307-1101', '1', 'Wohnung', 2.5, 60, 12, 1187, 15000, 1, 0, 'A'),
(304, 3, 307, 1102, '307-1102', '1', 'Wohnung', 3.5, 82, 12, 1527, 20500, 1, 0, 'A'),
(305, 3, 307, 1103, '307-1103', '1', 'Wohnung', 3.5, 82, 38, 1717, 20500, 0, 0, 'A'),
(306, 3, 307, 1104, '307-1104', '1', 'Wohnung', 3.5, 82, 38, 1764, 20500, 0, 0, 'A'),
(307, 3, 307, 1105, '307-1105', '1', 'Wohnung', 3.5, 82, 12, 1570, 20500, 1, 0, 'A'),
(308, 3, 307, 1106, '307-1106', '1', 'Wohnung', 2.5, 64, 30, 1391, 16000, 1, 0, 'A'),
(309, 3, 307, 1107, '307-1107', '1', 'Wohnung', 2.5, 65, 10, 1270, 16500, 0, 0, 'A'),
(310, 3, 307, 1201, '307-1201', '2', 'Wohnung', 2.5, 60, 11, 1202, 15000, 1, 0, 'A'),
(311, 3, 307, 1202, '307-1202', '2', 'Wohnung', 3.5, 82, 12, 1558, 20500, 1, 0, 'A'),
(312, 3, 307, 1203, '307-1203', '2', 'Wohnung', 3.5, 82, 35, 1731, 20500, 0, 0, 'A'),
(313, 3, 307, 1204, '307-1204', '2', 'Wohnung', 3.5, 82, 10, 1628, 20500, 0, 0, 'A'),
(314, 3, 307, 1205, '307-1205', '2', 'Wohnung', 3.5, 82, 11, 1592, 20500, 1, 0, 'A'),
(315, 3, 307, 1206, '307-1206', '2', 'Wohnung', 2.5, 64, 11, 1304, 16000, 1, 0, 'A'),
(316, 3, 307, 1207, '307-1207', '2', 'Wohnung', 2.5, 65, 10, 1289, 16500, 0, 0, 'A'),
(317, 3, 307, 1301, '307-1301', '3', 'Wohnung', 2.5, 60, 11, 1223, 15000, 1, 0, 'A'),
(318, 3, 307, 1302, '307-1302', '3', 'Wohnung', 3.5, 82, 11, 1578, 20500, 0, 0, 'A'),
(319, 3, 307, 1303, '307-1303', '3', 'Wohnung', 3.5, 82, 10, 1614, 20500, 0, 0, 'A'),
(320, 3, 307, 1304, '307-1304', '3', 'Wohnung', 3.5, 82, 10, 1657, 20500, 0, 0, 'A'),
(321, 3, 307, 1305, '307-1305', '3', 'Wohnung', 3.5, 82, 11, 1621, 20500, 0, 0, 'A'),
(322, 3, 307, 1306, '307-1306', '3', 'Wohnung', 2.5, 64, 11, 1326, 16000, 0, 0, 'A'),
(323, 3, 307, 1307, '307-1307', '3', 'Wohnung', 2.5, 65, 10, 1312, 16500, 0, 0, 'A'),
(324, 3, 307, 1401, '307-1401', '4', 'Wohnung', 2.5, 60, 11, 1245, 15000, 0, 0, 'A'),
(325, 3, 307, 1402, '307-1402', '4', 'Wohnung', 3.5, 82, 11, 1607, 20500, 0, 0, 'A'),
(326, 3, 307, 1403, '307-1403', '4', 'Wohnung', 3.5, 82, 10, 1642, 20500, 0, 0, 'A'),
(327, 3, 307, 1404, '307-1404', '4', 'Wohnung', 3.5, 82, 10, 1685, 20500, 0, 0, 'A'),
(328, 3, 307, 1405, '307-1405', '4', 'Wohnung', 3.5, 82, 11, 1649, 20500, 0, 0, 'A'),
(329, 3, 307, 1406, '307-1406', '4', 'Wohnung', 2.5, 64, 11, 1349, 16000, 0, 0, 'A'),
(330, 3, 307, 1407, '307-1407', '4', 'Wohnung', 2.5, 65, 10, 1334, 16500, 0, 0, 'A'),
(331, 3, 307, 1501, '307-1501', '5', 'Wohnung', 3.5, 81, 31, 1737, 20500, 0, 0, 'A'),
(332, 3, 307, 1502, '307-1502', '5', 'Wohnung', 3.5, 82, 11, 1635, 20500, 0, 0, 'A'),
(333, 3, 307, 1503, '307-1503', '5', 'Wohnung', 3.5, 82, 10, 1671, 20500, 0, 0, 'A'),
(334, 3, 307, 1504, '307-1504', '5', 'Wohnung', 3.5, 82, 10, 1713, 20500, 0, 0, 'A'),
(335, 3, 307, 1505, '307-1505', '5', 'Wohnung', 3.5, 82, 11, 1678, 20500, 0, 0, 'A'),
(336, 3, 307, 1506, '307-1506', '5', 'Wohnung', 3.5, 89, 20, 1861, 22500, 0, 0, 'A'),
(337, 3, 311, 1101, '311-1101', '1', 'Wohnung', 4.5, 103, 9, 1851, 26000, 0, 0, 'A'),
(338, 3, 311, 1102, '311-1102', '1', 'Wohnung', 5.5, 126, 11, 2221, 31500, 0, 0, 'A'),
(339, 3, 311, 1103, '311-1103', '1', 'Wohnung', 4.5, 105, 11, 2003, 26500, 0, 0, 'A'),
(340, 3, 311, 1104, '311-1104', '1', 'Wohnung', 4.5, 101, 9, 1870, 25500, 1, 0, 'A'),
(341, 3, 311, 1105, '311-1105', '1', 'Wohnung', 4.5, 106, 7, 1955, 26500, 1, 0, 'A'),
(342, 3, 311, 1106, '311-1106', '1', 'Wohnung', 3.5, 86, 8, 1620, 21500, 0, 0, 'A'),
(343, 3, 311, 1201, '311-1201', '2', 'Wohnung', 3, 74, 0, 1346, 18500, 0, 0, 'A'),
(344, 3, 311, 1202, '311-1202', '2', 'Wohnung', 5.5, 125, 11, 2308, 31500, 0, 0, 'A'),
(345, 3, 311, 1203, '311-1203', '2', 'Wohnung', 3, 68, 0, 1275, 17000, 0, 0, 'A'),
(346, 3, 311, 1204, '311-1204', '2', 'Wohnung', 4.5, 103, 8, 1931, 26000, 0, 0, 'A'),
(347, 3, 311, 1205, '311-1205', '2', 'Studio', 1, 33, 0, 680, 8500, 0, 0, 'A'),
(348, 3, 311, 1206, '311-1206', '2', 'Studio', 1, 33, 0, 680, 8500, 0, 0, 'A'),
(349, 3, 311, 1207, '311-1207', '2', 'Wohnung', 3.5, 85, 9, 1628, 21500, 0, 0, 'A'),
(350, 3, 311, 1301, '311-1301', '3', 'Wohnung', 4.5, 106, 11, 2031, 26500, 0, 0, 'A'),
(351, 3, 311, 1302, '311-1302', '3', 'Wohnung', 3.5, 92, 0, 1752, 23000, 0, 0, 'A'),
(352, 3, 311, 1303, '311-1303', '3', 'Wohnung', 4.5, 105, 9, 2010, 26500, 1, 0, 'A'),
(353, 3, 311, 1304, '311-1304', '3', 'Wohnung', 2.5, 70, 0, 1382, 17500, 0, 0, 'A'),
(354, 3, 311, 1305, '311-1305', '3', 'Wohnung', 4.5, 110, 8, 2045, 27500, 0, 0, 'A'),
(355, 3, 311, 1306, '311-1306', '3', 'Wohnung', 2, 51, 0, 980, 13000, 0, 0, 'A'),
(356, 3, 311, 1401, '311-1401', '4', 'Wohnung', 4.5, 103, 9, 1957, 26000, 0, 0, 'A'),
(357, 3, 311, 1402, '311-1402', '4', 'Wohnung', 5.5, 126, 11, 2350, 31500, 0, 0, 'A'),
(358, 3, 311, 1403, '311-1403', '4', 'Wohnung', 4.5, 105, 11, 2111, 26500, 0, 0, 'A'),
(359, 3, 311, 1404, '311-1404', '4', 'Wohnung', 4.5, 101, 9, 1974, 25500, 0, 0, 'A'),
(360, 3, 311, 1405, '311-1405', '4', 'Wohnung', 4.5, 106, 7, 2064, 26500, 0, 0, 'A'),
(361, 3, 311, 1406, '311-1406', '4', 'Wohnung', 3.5, 86, 8, 1708, 21500, 0, 0, 'A'),
(362, 3, 311, 1501, '311-1501', '5', 'Wohnung', 3, 74, 0, 1420, 18500, 0, 0, 'A'),
(363, 3, 311, 1502, '311-1502', '5', 'Wohnung', 5.5, 125, 11, 2436, 31500, 0, 0, 'A'),
(364, 3, 311, 1503, '311-1503', '5', 'Wohnung', 3, 68, 0, 1343, 17000, 0, 0, 'A'),
(365, 3, 311, 1504, '311-1504', '5', 'Wohnung', 4.5, 103, 8, 2037, 26000, 0, 0, 'A'),
(366, 3, 311, 1505, '311-1505', '5', 'Studio', 1, 33, 0, 713, 8500, 0, 0, 'A'),
(367, 3, 311, 1506, '311-1506', '5', 'Studio', 1, 33, 0, 713, 8500, 0, 0, 'A'),
(368, 3, 311, 1507, '311-1507', '5', 'Wohnung', 3.5, 85, 9, 1715, 21500, 0, 0, 'A'),
(369, 3, 311, 1601, '311-1601', '6', 'Wohnung', 4.5, 106, 11, 2139, 26500, 0, 0, 'A'),
(370, 3, 311, 1602, '311-1602', '6', 'Wohnung', 3.5, 92, 0, 1844, 23000, 0, 0, 'A'),
(371, 3, 311, 1603, '311-1603', '6', 'Wohnung', 4.5, 105, 9, 2118, 26500, 0, 0, 'A'),
(372, 3, 311, 1604, '311-1604', '6', 'Wohnung', 2.5, 70, 0, 1452, 17500, 0, 0, 'A'),
(373, 3, 311, 1605, '311-1605', '6', 'Wohnung', 4.5, 110, 8, 2157, 27500, 0, 0, 'A'),
(374, 3, 311, 1606, '311-1606', '6', 'Wohnung', 2, 51, 0, 1031, 13000, 0, 0, 'A')";
handle_table($query,'apartment','insert');

// addroom
$query  = "INSERT INTO `addroom` (`client_id`, `building_id`, `addroom_object`, `addroom_title`, `addroom_floor`, `addroom_type`, `addroom_rooms`, `addroom_area`, `addroom_balcony`, `addroom_rentalgross`, `addroom_participation`, `addroom_subvention`, `addroom_reserved`)
VALUES
    ('3',106,1121,'106-1121','1','Arbeitszimmer',1,15,0,234,4000,0,0),
    ('3',106,1221,'106-1221','2','Arbeitszimmer',1,15,0,239,4000,0,0),
    ('3',106,1321,'106-1321','3','Arbeitszimmer',1,15,0,244,4000,0,0),
    ('3',106,1421,'106-1421','4','Arbeitszimmer',1,15,0,249,4000,0,0),
    ('3',106,1521,'106-1521','5','Arbeitszimmer',1,15,0,254,4000,0,0),
    ('3',106,1621,'106-1621','6','Arbeitszimmer',1,15,0,259,4000,0,0),
    ('3',205,1106,'205-1106','1','Zusatzzimmer',1,20,0,407,5000,0,0),
    ('3',205,1206,'205-1206','2','Zusatzzimmer',1,20,0,413,5000,0,0),
    ('3',205,1306,'205-1306','3','Zusatzzimmer',1,20,0,420,5000,0,0),
    ('3',205,1406,'205-1406','4','Zusatzzimmer',1,20,0,427,5000,0,0),
    ('3',205,1506,'205-1506','5','Zusatzzimmer',1,20,0,433,5000,0,0),
    ('3',213,1202,'213-1202','2','Zusatzzimmer',1,19,0,393,5000,0,0),
    ('3',213,1302,'213-1302','3','Zusatzzimmer',1,19,0,403,5000,0,0),
    ('3',213,1402,'213-1402','4','Zusatzzimmer',1,19,0,410,5000,0,0),
    ('3',302,1221,'302-1221','2','Arbeitszimmer',1,21,0,349,5500,0,0),
    ('3',302,1321,'302-1321','3','Arbeitszimmer',1,21,0,355,5500,0,0),
    ('3',302,1421,'302-1421','4','Arbeitszimmer',1,21,0,362,5500,0,0),
    ('3',302,1206,'302-1206','2','Zusatzzimmer',1,19,0,392,5000,0,0),
    ('3',302,1306,'302-1306','3','Zusatzzimmer',1,19,0,398,5000,0,0),
    ('3',302,1406,'302-1406','4','Zusatzzimmer',1,19,0,404,5000,0,0),
    ('3',303,2021,'303-2021','P','Arbeitszimmer',1,20,0,354,5000,0,0),
    ('3',303,2022,'303-2022','P','Arbeitszimmer',1,20,0,354,5000,0,0),
    ('3',213,2021,'213-2021','P','Arbeitszimmer',1,16,0,272,4000,0,0),
    ('3',213,2022,'213-2022','P','Arbeitszimmer',1,15,0,267,4000,0,0),
    ('3',213,2023,'213-2023','P','Arbeitszimmer',1,20,0,346,5000,0,0),
    ('3',218,2021,'218-2021','P','Arbeitszimmer',1,13,0,230,3500,0,0),
    ('3',218,2022,'218-2022','P','Arbeitszimmer',1,16,0,279,4000,0,0),
    ('3',218,2023,'218-2023','P','Arbeitszimmer',1,13,0,228,3500,0,0),
    ('3',218,2024,'218-2024','P','Arbeitszimmer',1,12,0,216,3000,0,0)";
handle_table($query,'addroom','insert');

echo '&nbsp;'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";
echo '<b>Altlasten entfernen </b>'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";
$cleanUpArray = array('addrooms','adults','adult','apartments','assignment','assignment_addrooms','child','childs','logs','ratings','requests');
foreach ($cleanUpArray as $tableName)
    cleanup_table($tableName);




