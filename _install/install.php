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
$query .= "`username` varchar(255) NOT NULL,";
$query .= "`email` varchar(255) NOT NULL,";
$query .= "`password` varchar(255) NOT NULL,";
$query .= "`loginlevel` smallint(1) NOT NULL,";
$query .= "`created` datetime DEFAULT NULL,";
$query .= "`changed` datetime DEFAULT NULL,";
$query .= "`enabled` tinyint(4) NOT NULL DEFAULT '1',";
$query .= "PRIMARY KEY (`user_id`)";
$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'user');


$query  = "CREATE TABLE IF NOT EXISTS `customer` (";
$query .= "`customer_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`company` varchar(255) DEFAULT NULL,";
$query .= "`company2` varchar(255) DEFAULT NULL,";
$query .= "`firstname` varchar(255) DEFAULT NULL,";
$query .= "`lastname` varchar(255) DEFAULT NULL,";
$query .= "`gender` varchar(1) DEFAULT NULL,";
$query .= "`email` varchar(255) DEFAULT NULL,";
$query .= "`enabled` tinyint(4) NOT NULL DEFAULT '1',";
$query .= "PRIMARY KEY (`customer_id`)";
$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'customer');


$query  = "CREATE TABLE IF NOT EXISTS `address` (";
$query .= "`address_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`customer_id` int(11) DEFAULT NULL,";
$query .= "`address` varchar(255) NOT NULL,";
$query .= "`pobox` varchar(255) NOT NULL,";
$query .= "`city` varchar(255) NOT NULL,";
$query .= "`code` varchar(10) NOT NULL,";
$query .= "`country_id` int(11) NOT NULL,";
//$query .= "`region_id` int(11) DEFAULT NULL,";
//$query .= "`latitude` varchar(20) DEFAULT NULL,";
//$query .= "`longitude` varchar(20) DEFAULT NULL,";
$query .= "PRIMARY KEY (`address_id`),";
$query .= "KEY `country_id` (`country_id`),";
$query .= "KEY `customer_id` (`customer_id`)";
$query .= ") ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'address');


$query  = "CREATE TABLE IF NOT EXISTS `country` (";
$query .= "`country_id` int(11) NOT NULL AUTO_INCREMENT,";
$query .= "`name` varchar(200) DEFAULT NULL,";
$query .= "`code` varchar(10) DEFAULT NULL,";
$query .= "`tld` varchar(10) DEFAULT NULL,";
$query .= "PRIMARY KEY (`country_id`)";
$query .= ") ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
handle_table($query,'country');

/*----------------------------------------------------------------------------------------------------------------------
    Table Content
----------------------------------------------------------------------------------------------------------------------*/


echo '&nbsp;'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";
echo '<b>Abf&uuml;llen der Daten </b>'."<br />\n";
echo '------------------------------------------------------------------------------------------------------'."<br />\n";


// Benutzer
$query  = "INSERT INTO `user` (`username`, `email`,`password`, `loginlevel`,`created`,`changed`,`enabled`) VALUES ";
$query .= "('info@pan-x.ch','info@pan-x.ch', 'beetester45', 3, CURRENT_TIMESTAMP, NULL,1)";
handle_table($query,'user','insert');


$query  = "INSERT INTO `country` (`country_id`, `name`, `code`, `tld`, `enabled`) VALUES
(1, 'Afghanistan', 'AF', '.af', 1),
(2, 'Albania', 'AL', '.al', 1),
(3, 'Algeria', 'DZ', '.dz', 1),
(4, 'Andorra', 'AD', '.ad', 1),
(5, 'Angola', 'AO', '.ao', 1),
(6, 'Antigua and Barbuda', 'AG', '.ag', 1),
(7, 'Argentina', 'AR', '.ar', 1),
(8, 'Armenia', 'AM', '.am', 1),
(9, 'Australia', 'AU', '.au', 1),
(10, 'Austria', 'AT', '.at', 1),
(11, 'Azerbaijan', 'AZ', '.az', 1),
(12, 'Bahamas, The', 'BS', '.bs', 1),
(13, 'Bahrain', 'BH', '.bh', 1),
(14, 'Bangladesh', 'BD', '.bd', 1),
(15, 'Barbados', 'BB', '.bb', 1),
(16, 'Belarus', 'BY', '.by', 1),
(17, 'Belgium', 'BE', '.be', 1),
(18, 'Belize', 'BZ', '.bz', 1),
(19, 'Benin', 'BJ', '.bj', 1),
(20, 'Bhutan', 'BT', '.bt', 1),
(21, 'Bolivia', 'BO', '.bo', 1),
(22, 'Bosnia and Herzegovina', 'BA', '.ba', 1),
(23, 'Botswana', 'BW', '.bw', 1),
(24, 'Brazil', 'BR', '.br', 1),
(25, 'Brunei', 'BN', '.bn', 1),
(26, 'Bulgaria', 'BG', '.bg', 1),
(27, 'Burkina Faso', 'BF', '.bf', 1),
(28, 'Burundi', 'BI', '.bi', 1),
(29, 'Cambodia', 'KH', '.kh', 1),
(30, 'Cameroon', 'CM', '.cm', 1),
(31, 'Canada', 'CA', '.ca', 1),
(32, 'Cape Verde', 'CV', '.cv', 1),
(33, 'Central African Republic', 'CF', '.cf', 1),
(34, 'Chad', 'TD', '.td', 1),
(35, 'Chile', 'CL', '.cl', 1),
(36, 'China, People''s Republic of', 'CN', '.cn', 1),
(37, 'Colombia', 'CO', '.co', 1),
(38, 'Comoros', 'KM', '.km', 1),
(39, 'Congo, Democratic Republic of the (Congo ﾖ Kinshasa)', 'CD', '.cd', 1),
(40, 'Congo, Republic of the (Congo ﾖ Brazzaville)', 'CG', '.cg', 1),
(41, 'Costa Rica', 'CR', '.cr', 1),
(42, 'Cote d''Ivoire (Ivory Coast)', 'CI', '.ci', 1),
(43, 'Croatia', 'HR', '.hr', 1),
(44, 'Cuba', 'CU', '.cu', 1),
(45, 'Cyprus', 'CY', '.cy', 1),
(46, 'Czech Republic', 'CZ', '.cz', 1),
(47, 'Denmark', 'DK', '.dk', 1),
(48, 'Djibouti', 'DJ', '.dj', 1),
(49, 'Dominica', 'DM', '.dm', 1),
(50, 'Dominican Republic', 'DO', '.do', 1),
(51, 'Ecuador', 'EC', '.ec', 1),
(52, 'Egypt', 'EG', '.eg', 1),
(53, 'El Salvador', 'SV', '.sv', 1),
(54, 'Equatorial Guinea', 'GQ', '.gq', 1),
(55, 'Eritrea', 'ER', '.er', 1),
(56, 'Estonia', 'EE', '.ee', 1),
(57, 'Ethiopia', 'ET', '.et', 1),
(58, 'Fiji', 'FJ', '.fj', 1),
(59, 'Finland', 'FI', '.fi', 1),
(60, 'France', 'FR', '.fr', 1),
(61, 'Gabon', 'GA', '.ga', 1),
(62, 'Gambia, The', 'GM', '.gm', 1),
(63, 'Georgia', 'GE', '.ge', 1),
(64, 'Germany', 'DE', '.de', 1),
(65, 'Ghana', 'GH', '.gh', 1),
(66, 'Greece', 'GR', '.gr', 1),
(67, 'Grenada', 'GD', '.gd', 1),
(68, 'Guatemala', 'GT', '.gt', 1),
(69, 'Guinea', 'GN', '.gn', 1),
(70, 'Guinea-Bissau', 'GW', '.gw', 1),
(71, 'Guyana', 'GY', '.gy', 1),
(72, 'Haiti', 'HT', '.ht', 1),
(73, 'Honduras', 'HN', '.hn', 1),
(74, 'Hungary', 'HU', '.hu', 1),
(75, 'Iceland', 'IS', '.is', 1),
(76, 'India', 'IN', '.in', 1),
(77, 'Indonesia', 'ID', '.id', 1),
(78, 'Iran', 'IR', '.ir', 1),
(79, 'Iraq', 'IQ', '.iq', 1),
(80, 'Ireland', 'IE', '.ie', 1),
(81, 'Israel', 'IL', '.il', 1),
(82, 'Italy', 'IT', '.it', 1),
(83, 'Jamaica', 'JM', '.jm', 1),
(84, 'Japan', 'JP', '.jp', 1),
(85, 'Jordan', 'JO', '.jo', 1),
(86, 'Kazakhstan', 'KZ', '.kz', 1),
(87, 'Kenya', 'KE', '.ke', 1),
(88, 'Kiribati', 'KI', '.ki', 1),
(89, 'Korea, Democratic People''s Republic of (North Korea)', 'KP', '.kp', 1),
(90, 'Korea, Republic of  (South Korea)', 'KR', '.kr', 1),
(91, 'Kuwait', 'KW', '.kw', 1),
(92, 'Kyrgyzstan', 'KG', '.kg', 1),
(93, 'Laos', 'LA', '.la', 1),
(94, 'Latvia', 'LV', '.lv', 1),
(95, 'Lebanon', 'LB', '.lb', 1),
(96, 'Lesotho', 'LS', '.ls', 1),
(97, 'Liberia', 'LR', '.lr', 1),
(98, 'Libya', 'LY', '.ly', 1),
(99, 'Liechtenstein', 'LI', '.li', 1),
(100, 'Lithuania', 'LT', '.lt', 1),
(101, 'Luxembourg', 'LU', '.lu', 1),
(102, 'Macedonia', 'MK', '.mk', 1),
(103, 'Madagascar', 'MG', '.mg', 1),
(104, 'Malawi', 'MW', '.mw', 1),
(105, 'Malaysia', 'MY', '.my', 1),
(106, 'Maldives', 'MV', '.mv', 1),
(107, 'Mali', 'ML', '.ml', 1),
(108, 'Malta', 'MT', '.mt', 1),
(109, 'Marshall Islands', 'MH', '.mh', 1),
(110, 'Mauritania', 'MR', '.mr', 1),
(111, 'Mauritius', 'MU', '.mu', 1),
(112, 'Mexico', 'MX', '.mx', 1),
(113, 'Micronesia', 'FM', '.fm', 1),
(114, 'Moldova', 'MD', '.md', 1),
(115, 'Monaco', 'MC', '.mc', 1),
(116, 'Mongolia', 'MN', '.mn', 1),
(117, 'Montenegro', 'ME', '.me and .y', 1),
(118, 'Morocco', 'MA', '.ma', 1),
(119, 'Mozambique', 'MZ', '.mz', 1),
(120, 'Myanmar (Burma)', 'MM', '.mm', 1),
(121, 'Namibia', 'NA', '.na', 1),
(122, 'Nauru', 'NR', '.nr', 1),
(123, 'Nepal', 'NP', '.np', 1),
(124, 'Netherlands', 'NL', '.nl', 1),
(125, 'New Zealand', 'NZ', '.nz', 1),
(126, 'Nicaragua', 'NI', '.ni', 1),
(127, 'Niger', 'NE', '.ne', 1),
(128, 'Nigeria', 'NG', '.ng', 1),
(129, 'Norway', 'NO', '.no', 1),
(130, 'Oman', 'OM', '.om', 1),
(131, 'Pakistan', 'PK', '.pk', 1),
(132, 'Palau', 'PW', '.pw', 1),
(133, 'Panama', 'PA', '.pa', 1),
(134, 'Papua New Guinea', 'PG', '.pg', 1),
(135, 'Paraguay', 'PY', '.py', 1),
(136, 'Peru', 'PE', '.pe', 1),
(137, 'Philippines', 'PH', '.ph', 1),
(138, 'Poland', 'PL', '.pl', 1),
(139, 'Portugal', 'PT', '.pt', 1),
(140, 'Qatar', 'QA', '.qa', 1),
(141, 'Romania', 'RO', '.ro', 1),
(142, 'Russia', 'RU', '.ru and .s', 1),
(143, 'Rwanda', 'RW', '.rw', 1),
(144, 'Saint Kitts and Nevis', 'KN', '.kn', 1),
(145, 'Saint Lucia', 'LC', '.lc', 1),
(146, 'Saint Vincent and the Grenadines', 'VC', '.vc', 1),
(147, 'Samoa', 'WS', '.ws', 1),
(148, 'San Marino', 'SM', '.sm', 1),
(149, 'Sao Tome and Principe', 'ST', '.st', 1),
(150, 'Saudi Arabia', 'SA', '.sa', 1),
(151, 'Senegal', 'SN', '.sn', 1),
(152, 'Serbia', 'RS', '.rs and .y', 1),
(153, 'Seychelles', 'SC', '.sc', 1),
(154, 'Sierra Leone', 'SL', '.sl', 1),
(155, 'Singapore', 'SG', '.sg', 1),
(156, 'Slovakia', 'SK', '.sk', 1),
(157, 'Slovenia', 'SI', '.si', 1),
(158, 'Solomon Islands', 'SB', '.sb', 1),
(159, 'Somalia', 'SO', '.so', 1),
(160, 'South Africa', 'ZA', '.za', 1),
(161, 'Spain', 'ES', '.es', 1),
(162, 'Sri Lanka', 'LK', '.lk', 1),
(163, 'Sudan', 'SD', '.sd', 1),
(164, 'Suriname', 'SR', '.sr', 1),
(165, 'Swaziland', 'SZ', '.sz', 1),
(166, 'Sweden', 'SE', '.se', 1),
(167, 'Switzerland', 'CH', '.ch', 1),
(168, 'Syria', 'SY', '.sy', 1),
(169, 'Tajikistan', 'TJ', '.tj', 1),
(170, 'Tanzania', 'TZ', '.tz', 1),
(171, 'Thailand', 'TH', '.th', 1),
(172, 'Timor-Leste (East Timor)', 'TL', '.tp and .t', 1),
(173, 'Togo', 'TG', '.tg', 1),
(174, 'Tonga', 'TO', '.to', 1),
(175, 'Trinidad and Tobago', 'TT', '.tt', 1),
(176, 'Tunisia', 'TN', '.tn', 1),
(177, 'Turkey', 'TR', '.tr', 1),
(178, 'Turkmenistan', 'TM', '.tm', 1),
(179, 'Tuvalu', 'TV', '.tv', 1),
(180, 'Uganda', 'UG', '.ug', 1),
(181, 'Ukraine', 'UA', '.ua', 1),
(182, 'United Arab Emirates', 'AE', '.ae', 1),
(183, 'United Kingdom', 'GB', '.uk', 1),
(184, 'United States', 'US', '.us', 1),
(185, 'Uruguay', 'UY', '.uy', 1),
(186, 'Uzbekistan', 'UZ', '.uz', 1),
(187, 'Vanuatu', 'VU', '.vu', 1),
(188, 'Vatican City', 'VA', '.va', 1),
(189, 'Venezuela', 'VE', '.ve', 1),
(190, 'Vietnam', 'VN', '.vn', 1),
(191, 'Yemen', 'YE', '.ye', 1),
(192, 'Zambia', 'ZM', '.zm', 1),
(193, 'Zimbabwe', 'ZW', '.zw', 1),
(194, 'Abkhazia', 'GE', '.ge', 1),
(195, 'China, Republic of (Taiwan)', 'TW', '.tw', 1),
(196, 'Nagorno-Karabakh', 'AZ', '.az', 1),
(197, 'Northern Cyprus', 'CY', '.nc.tr', 1),
(198, 'Pridnestrovie (Transnistria)', 'MD', '.md', 1),
(199, 'Somaliland', 'SO', '.so', 1),
(200, 'South Ossetia', 'GE', '.ge', 1),
(201, 'Ashmore and Cartier Islands', 'AU', '.au', 1),
(202, 'Christmas Island', 'CX', '.cx', 1),
(203, 'Cocos (Keeling) Islands', 'CC', '.cc', 1),
(204, 'Coral Sea Islands', 'AU', '.au', 1),
(205, 'Heard Island and McDonald Islands', 'HM', '.hm', 1),
(206, 'Norfolk Island', 'NF', '.nf', 1),
(207, 'New Caledonia', 'NC', '.nc', 1),
(208, 'French Polynesia', 'PF', '.pf', 1),
(209, 'Mayotte', 'YT', '.yt', 1),
(210, 'Saint Barthelemy', 'GP', '.gp', 1),
(211, 'Saint Martin', 'GP', '.gp', 1),
(212, 'Saint Pierre and Miquelon', 'PM', '.pm', 1),
(213, 'Wallis and Futuna', 'WF', '.wf', 1),
(214, 'French Southern and Antarctic Lands', 'TF', '.tf', 1),
(215, 'Clipperton Island', 'PF', '.pf', 1),
(216, 'Bouvet Island', 'BV', '.bv', 1),
(217, 'Cook Islands', 'CK', '.ck', 1),
(218, 'Niue', 'NU', '.nu', 1),
(219, 'Tokelau', 'TK', '.tk', 1),
(220, 'Guernsey', 'GG', '.gg', 1),
(221, 'Isle of Man', 'IM', '.im', 1),
(222, 'Jersey', 'JE', '.je', 1),
(223, 'Anguilla', 'AI', '.ai', 1),
(224, 'Bermuda', 'BM', '.bm', 1),
(225, 'British Indian Ocean Territory', 'IO', '.io', 1),
(226, 'British Sovereign Base Areas', '', '', 1),
(227, 'British Virgin Islands', 'VG', '.vg', 1),
(228, 'Cayman Islands', 'KY', '.ky', 1),
(229, 'Falkland Islands (Islas Malvinas)', 'FK', '.fk', 1),
(230, 'Gibraltar', 'GI', '.gi', 1),
(231, 'Montserrat', 'MS', '.ms', 1),
(232, 'Pitcairn Islands', 'PN', '.pn', 1),
(233, 'Saint Helena', 'SH', '.sh', 1),
(234, 'South Georgia and the South Sandwich Islands', 'GS', '.gs', 1),
(235, 'Turks and Caicos Islands', 'TC', '.tc', 1),
(236, 'Northern Mariana Islands', 'MP', '.mp', 1),
(237, 'Puerto Rico', 'PR', '.pr', 1),
(238, 'American Samoa', 'AS', '.as', 1),
(239, 'Baker Island', 'UM', '', 1),
(240, 'Guam', 'GU', '.gu', 1),
(241, 'Howland Island', 'UM', '', 1),
(242, 'Jarvis Island', 'UM', '', 1),
(243, 'Johnston Atoll', 'UM', '', 1),
(244, 'Kingman Reef', 'UM', '', 1),
(245, 'Midway Islands', 'UM', '', 1),
(246, 'Navassa Island', 'UM', '', 1),
(247, 'Palmyra Atoll', 'UM', '', 1),
(248, 'U.S. Virgin Islands', 'VI', '.vi', 1),
(249, 'Wake Island', 'UM', '', 1),
(250, 'Hong Kong', 'HK', '.hk', 1),
(251, 'Macau', 'MO', '.mo', 1),
(252, 'Faroe Islands', 'FO', '.fo', 1),
(253, 'Greenland', 'GL', '.gl', 1),
(254, 'French Guiana', 'GF', '.gf', 1),
(255, 'Guadeloupe', 'GP', '.gp', 1),
(256, 'Martinique', 'MQ', '.mq', 1),
(257, 'Reunion', 'RE', '.re', 1),
(258, 'Aland', 'AX', '.ax', 1),
(259, 'Aruba', 'AW', '.aw', 1),
(260, 'Netherlands Antilles', 'AN', '.an', 1),
(261, 'Svalbard', 'SJ', '.sj', 1),
(262, 'Ascension', 'AC', '.ac', 1),
(263, 'Tristan da Cunha', 'TA', '', 1),
(264, 'Antarctica', 'AQ', '.aq', 1),
(265, 'Kosovo', 'CS', '.cs and .y', 1),
(266, 'Palestinian Territories (Gaza Strip and West Bank)', 'PS', '.ps', 1),
(267, 'Western Sahara', 'EH', '.eh', 1),
(268, 'Australian Antarctic Territory', 'AQ', '.aq', 1),
(269, 'Ross Dependency', 'AQ', '.aq', 1),
(270, 'Peter I Island', 'AQ', '.aq', 1),
(271, 'Queen Maud Land', 'AQ', '.aq', 1),
(272, 'British Antarctic Territory', 'AQ', '.aq', 1)";
handle_table($query,'country','insert');
