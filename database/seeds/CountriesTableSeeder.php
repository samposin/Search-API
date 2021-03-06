<?php

use Illuminate\Database\Seeder;


class CountriesTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('countries')->delete();

        $countries = array(
            array('id'=>1,'code'=>"AD",'name'=>"Andorra"),
            array('id'=>2,'code'=>"AE",'name'=>"United Arab Emirates"),
            array('id'=>3,'code'=>"AF",'name'=>"Afghanistan"),
            array('id'=>4,'code'=>"AG",'name'=>"Antigua and Barbuda"),
            array('id'=>5,'code'=>"AI",'name'=>"Anguilla"),
            array('id'=>6,'code'=>"AL",'name'=>"Albania"),
            array('id'=>7,'code'=>"AM",'name'=>"Armenia"),
            array('id'=>8,'code'=>"AO",'name'=>"Angola"),
            array('id'=>9,'code'=>"AQ",'name'=>"Antarctica"),
            array('id'=>10,'code'=>"AR",'name'=>"Argentina"),
            array('id'=>11,'code'=>"AS",'name'=>"American Samoa"),
            array('id'=>12,'code'=>"AT",'name'=>"Austria"),
            array('id'=>13,'code'=>"AU",'name'=>"Australia"),
            array('id'=>14,'code'=>"AW",'name'=>"Aruba"),
            array('id'=>15,'code'=>"AX",'name'=>"Aland Islands"),
            array('id'=>16,'code'=>"AZ",'name'=>"Azerbaijan"),
            array('id'=>17,'code'=>"BA",'name'=>"Bosnia and Herzegovina"),
            array('id'=>18,'code'=>"BB",'name'=>"Barbados"),
            array('id'=>19,'code'=>"BD",'name'=>"Bangladesh"),
            array('id'=>20,'code'=>"BE",'name'=>"Belgium"),
            array('id'=>21,'code'=>"BF",'name'=>"Burkina Faso"),
            array('id'=>22,'code'=>"BG",'name'=>"Bulgaria"),
            array('id'=>23,'code'=>"BH",'name'=>"Bahrain"),
            array('id'=>24,'code'=>"BI",'name'=>"Burundi"),
            array('id'=>25,'code'=>"BJ",'name'=>"Benin"),
            array('id'=>26,'code'=>"BL",'name'=>"Saint Barthélemy"),
            array('id'=>27,'code'=>"BM",'name'=>"Bermuda"),
            array('id'=>28,'code'=>"BN",'name'=>"Brunei"),
            array('id'=>29,'code'=>"BO",'name'=>"Bolivia"),
            array('id'=>30,'code'=>"BQ",'name'=>"Bonaire, Saint Eustatius and Saba "),
            array('id'=>31,'code'=>"BR",'name'=>"Brazil"),
            array('id'=>32,'code'=>"BS",'name'=>"Bahamas"),
            array('id'=>33,'code'=>"BT",'name'=>"Bhutan"),
            array('id'=>34,'code'=>"BV",'name'=>"Bouvet Island"),
            array('id'=>35,'code'=>"BW",'name'=>"Botswana"),
            array('id'=>36,'code'=>"BY",'name'=>"Belarus"),
            array('id'=>37,'code'=>"BZ",'name'=>"Belize"),
            array('id'=>38,'code'=>"CA",'name'=>"Canada"),
            array('id'=>39,'code'=>"CC",'name'=>"Cocos Islands"),
            array('id'=>40,'code'=>"CD",'name'=>"Democratic Republic of the Congo"),
            array('id'=>41,'code'=>"CF",'name'=>"Central African Republic"),
            array('id'=>42,'code'=>"CG",'name'=>"Republic of the Congo"),
            array('id'=>43,'code'=>"CH",'name'=>"Switzerland"),
            array('id'=>44,'code'=>"CI",'name'=>"Ivory Coast"),
            array('id'=>45,'code'=>"CK",'name'=>"Cook Islands"),
            array('id'=>46,'code'=>"CL",'name'=>"Chile"),
            array('id'=>47,'code'=>"CM",'name'=>"Cameroon"),
            array('id'=>48,'code'=>"CN",'name'=>"China"),
            array('id'=>49,'code'=>"CO",'name'=>"Colombia"),
            array('id'=>50,'code'=>"CR",'name'=>"Costa Rica"),
            array('id'=>51,'code'=>"CU",'name'=>"Cuba"),
            array('id'=>52,'code'=>"CV",'name'=>"Cape Verde"),
            array('id'=>53,'code'=>"CW",'name'=>"Curaçao"),
            array('id'=>54,'code'=>"CX",'name'=>"Christmas Island"),
            array('id'=>55,'code'=>"CY",'name'=>"Cyprus"),
            array('id'=>56,'code'=>"CZ",'name'=>"Czech Republic"),
            array('id'=>57,'code'=>"DE",'name'=>"Germany"),
            array('id'=>58,'code'=>"DJ",'name'=>"Djibouti"),
            array('id'=>59,'code'=>"DK",'name'=>"Denmark"),
            array('id'=>60,'code'=>"DM",'name'=>"Dominica"),
            array('id'=>61,'code'=>"DO",'name'=>"Dominican Republic"),
            array('id'=>62,'code'=>"DZ",'name'=>"Algeria"),
            array('id'=>63,'code'=>"EC",'name'=>"Ecuador"),
            array('id'=>64,'code'=>"EE",'name'=>"Estonia"),
            array('id'=>65,'code'=>"EG",'name'=>"Egypt"),
            array('id'=>66,'code'=>"EH",'name'=>"Western Sahara"),
            array('id'=>67,'code'=>"ER",'name'=>"Eritrea"),
            array('id'=>68,'code'=>"ES",'name'=>"Spain"),
            array('id'=>69,'code'=>"ET",'name'=>"Ethiopia"),
            array('id'=>70,'code'=>"FI",'name'=>"Finland"),
            array('id'=>71,'code'=>"FJ",'name'=>"Fiji"),
            array('id'=>72,'code'=>"FK",'name'=>"Falkland Islands"),
            array('id'=>73,'code'=>"FM",'name'=>"Micronesia"),
            array('id'=>74,'code'=>"FO",'name'=>"Faroe Islands"),
            array('id'=>75,'code'=>"FR",'name'=>"France"),
            array('id'=>76,'code'=>"GA",'name'=>"Gabon"),
            array('id'=>77,'code'=>"GB",'name'=>"United Kingdom"),
            array('id'=>78,'code'=>"GD",'name'=>"Grenada"),
            array('id'=>79,'code'=>"GE",'name'=>"Georgia"),
            array('id'=>80,'code'=>"GF",'name'=>"French Guiana"),
            array('id'=>81,'code'=>"GG",'name'=>"Guernsey"),
            array('id'=>82,'code'=>"GH",'name'=>"Ghana"),
            array('id'=>83,'code'=>"GI",'name'=>"Gibraltar"),
            array('id'=>84,'code'=>"GL",'name'=>"Greenland"),
            array('id'=>85,'code'=>"GM",'name'=>"Gambia"),
            array('id'=>86,'code'=>"GN",'name'=>"Guinea"),
            array('id'=>87,'code'=>"GP",'name'=>"Guadeloupe"),
            array('id'=>88,'code'=>"GQ",'name'=>"Equatorial Guinea"),
            array('id'=>89,'code'=>"GR",'name'=>"Greece"),
            array('id'=>90,'code'=>"GS",'name'=>"South Georgia and the South Sandwich Islands"),
            array('id'=>91,'code'=>"GT",'name'=>"Guatemala"),
            array('id'=>92,'code'=>"GU",'name'=>"Guam"),
            array('id'=>93,'code'=>"GW",'name'=>"Guinea-Bissau"),
            array('id'=>94,'code'=>"GY",'name'=>"Guyana"),
            array('id'=>95,'code'=>"HK",'name'=>"Hong Kong"),
            array('id'=>96,'code'=>"HM",'name'=>"Heard Island and McDonald Islands"),
            array('id'=>97,'code'=>"HN",'name'=>"Honduras"),
            array('id'=>98,'code'=>"HR",'name'=>"Croatia"),
            array('id'=>99,'code'=>"HT",'name'=>"Haiti"),
            array('id'=>100,'code'=>"HU",'name'=>"Hungary"),
            array('id'=>101,'code'=>"ID",'name'=>"Indonesia"),
            array('id'=>102,'code'=>"IE",'name'=>"Ireland"),
            array('id'=>103,'code'=>"IL",'name'=>"Israel"),
            array('id'=>104,'code'=>"IM",'name'=>"Isle of Man"),
            array('id'=>105,'code'=>"IN",'name'=>"India"),
            array('id'=>106,'code'=>"IO",'name'=>"British Indian Ocean Territory"),
            array('id'=>107,'code'=>"IQ",'name'=>"Iraq"),
            array('id'=>108,'code'=>"IR",'name'=>"Iran"),
            array('id'=>109,'code'=>"IS",'name'=>"Iceland"),
            array('id'=>110,'code'=>"IT",'name'=>"Italy"),
            array('id'=>111,'code'=>"JE",'name'=>"Jersey"),
            array('id'=>112,'code'=>"JM",'name'=>"Jamaica"),
            array('id'=>113,'code'=>"JO",'name'=>"Jordan"),
            array('id'=>114,'code'=>"JP",'name'=>"Japan"),
            array('id'=>115,'code'=>"KE",'name'=>"Kenya"),
            array('id'=>116,'code'=>"KG",'name'=>"Kyrgyzstan"),
            array('id'=>117,'code'=>"KH",'name'=>"Cambodia"),
            array('id'=>118,'code'=>"KI",'name'=>"Kiribati"),
            array('id'=>119,'code'=>"KM",'name'=>"Comoros"),
            array('id'=>120,'code'=>"KN",'name'=>"Saint Kitts and Nevis"),
            array('id'=>121,'code'=>"KP",'name'=>"North Korea"),
            array('id'=>122,'code'=>"KR",'name'=>"South Korea"),
            array('id'=>123,'code'=>"XK",'name'=>"Kosovo"),
            array('id'=>124,'code'=>"KW",'name'=>"Kuwait"),
            array('id'=>125,'code'=>"KY",'name'=>"Cayman Islands"),
            array('id'=>126,'code'=>"KZ",'name'=>"Kazakhstan"),
            array('id'=>127,'code'=>"LA",'name'=>"Laos"),
            array('id'=>128,'code'=>"LB",'name'=>"Lebanon"),
            array('id'=>129,'code'=>"LC",'name'=>"Saint Lucia"),
            array('id'=>130,'code'=>"LI",'name'=>"Liechtenstein"),
            array('id'=>131,'code'=>"LK",'name'=>"Sri Lanka"),
            array('id'=>132,'code'=>"LR",'name'=>"Liberia"),
            array('id'=>133,'code'=>"LS",'name'=>"Lesotho"),
            array('id'=>134,'code'=>"LT",'name'=>"Lithuania"),
            array('id'=>135,'code'=>"LU",'name'=>"Luxembourg"),
            array('id'=>136,'code'=>"LV",'name'=>"Latvia"),
            array('id'=>137,'code'=>"LY",'name'=>"Libya"),
            array('id'=>138,'code'=>"MA",'name'=>"Morocco"),
            array('id'=>139,'code'=>"MC",'name'=>"Monaco"),
            array('id'=>140,'code'=>"MD",'name'=>"Moldova"),
            array('id'=>141,'code'=>"ME",'name'=>"Montenegro"),
            array('id'=>142,'code'=>"MF",'name'=>"Saint Martin"),
            array('id'=>143,'code'=>"MG",'name'=>"Madagascar"),
            array('id'=>144,'code'=>"MH",'name'=>"Marshall Islands"),
            array('id'=>145,'code'=>"MK",'name'=>"Macedonia"),
            array('id'=>146,'code'=>"ML",'name'=>"Mali"),
            array('id'=>147,'code'=>"MM",'name'=>"Myanmar"),
            array('id'=>148,'code'=>"MN",'name'=>"Mongolia"),
            array('id'=>149,'code'=>"MO",'name'=>"Macao"),
            array('id'=>150,'code'=>"MP",'name'=>"Northern Mariana Islands"),
            array('id'=>151,'code'=>"MQ",'name'=>"Martinique"),
            array('id'=>152,'code'=>"MR",'name'=>"Mauritania"),
            array('id'=>153,'code'=>"MS",'name'=>"Montserrat"),
            array('id'=>154,'code'=>"MT",'name'=>"Malta"),
            array('id'=>155,'code'=>"MU",'name'=>"Mauritius"),
            array('id'=>156,'code'=>"MV",'name'=>"Maldives"),
            array('id'=>157,'code'=>"MW",'name'=>"Malawi"),
            array('id'=>158,'code'=>"MX",'name'=>"Mexico"),
            array('id'=>159,'code'=>"MY",'name'=>"Malaysia"),
            array('id'=>160,'code'=>"MZ",'name'=>"Mozambique"),
            array('id'=>161,'code'=>"NA",'name'=>"Namibia"),
            array('id'=>162,'code'=>"NC",'name'=>"New Caledonia"),
            array('id'=>163,'code'=>"NE",'name'=>"Niger"),
            array('id'=>164,'code'=>"NF",'name'=>"Norfolk Island"),
            array('id'=>165,'code'=>"NG",'name'=>"Nigeria"),
            array('id'=>166,'code'=>"NI",'name'=>"Nicaragua"),
            array('id'=>167,'code'=>"NL",'name'=>"Netherlands"),
            array('id'=>168,'code'=>"NO",'name'=>"Norway"),
            array('id'=>169,'code'=>"NP",'name'=>"Nepal"),
            array('id'=>170,'code'=>"NR",'name'=>"Nauru"),
            array('id'=>171,'code'=>"NU",'name'=>"Niue"),
            array('id'=>172,'code'=>"NZ",'name'=>"New Zealand"),
            array('id'=>173,'code'=>"OM",'name'=>"Oman"),
            array('id'=>174,'code'=>"PA",'name'=>"Panama"),
            array('id'=>175,'code'=>"PE",'name'=>"Peru"),
            array('id'=>176,'code'=>"PF",'name'=>"French Polynesia"),
            array('id'=>177,'code'=>"PG",'name'=>"Papua New Guinea"),
            array('id'=>178,'code'=>"PH",'name'=>"Philippines"),
            array('id'=>179,'code'=>"PK",'name'=>"Pakistan"),
            array('id'=>180,'code'=>"PL",'name'=>"Poland"),
            array('id'=>181,'code'=>"PM",'name'=>"Saint Pierre and Miquelon"),
            array('id'=>182,'code'=>"PN",'name'=>"Pitcairn"),
            array('id'=>183,'code'=>"PR",'name'=>"Puerto Rico"),
            array('id'=>184,'code'=>"PS",'name'=>"Palestinian Territory"),
            array('id'=>185,'code'=>"PT",'name'=>"Portugal"),
            array('id'=>186,'code'=>"PW",'name'=>"Palau"),
            array('id'=>187,'code'=>"PY",'name'=>"Paraguay"),
            array('id'=>188,'code'=>"QA",'name'=>"Qatar"),
            array('id'=>189,'code'=>"RE",'name'=>"Reunion"),
            array('id'=>190,'code'=>"RO",'name'=>"Romania"),
            array('id'=>191,'code'=>"RS",'name'=>"Serbia"),
            array('id'=>192,'code'=>"RU",'name'=>"Russia"),
            array('id'=>193,'code'=>"RW",'name'=>"Rwanda"),
            array('id'=>194,'code'=>"SA",'name'=>"Saudi Arabia"),
            array('id'=>195,'code'=>"SB",'name'=>"Solomon Islands"),
            array('id'=>196,'code'=>"SC",'name'=>"Seychelles"),
            array('id'=>197,'code'=>"SD",'name'=>"Sudan"),
            array('id'=>198,'code'=>"SE",'name'=>"Sweden"),
            array('id'=>199,'code'=>"SG",'name'=>"Singapore"),
            array('id'=>200,'code'=>"SH",'name'=>"Saint Helena"),
            array('id'=>201,'code'=>"SI",'name'=>"Slovenia"),
            array('id'=>202,'code'=>"SJ",'name'=>"Svalbard and Jan Mayen"),
            array('id'=>203,'code'=>"SK",'name'=>"Slovakia"),
            array('id'=>204,'code'=>"SL",'name'=>"Sierra Leone"),
            array('id'=>205,'code'=>"SM",'name'=>"San Marino"),
            array('id'=>206,'code'=>"SN",'name'=>"Senegal"),
            array('id'=>207,'code'=>"SO",'name'=>"Somalia"),
            array('id'=>208,'code'=>"SR",'name'=>"Suriname"),
            array('id'=>209,'code'=>"ST",'name'=>"Sao Tome and Principe"),
            array('id'=>210,'code'=>"SV",'name'=>"El Salvador"),
            array('id'=>211,'code'=>"SX",'name'=>"Sint Maarten"),
            array('id'=>212,'code'=>"SY",'name'=>"Syria"),
            array('id'=>213,'code'=>"SZ",'name'=>"Swaziland"),
            array('id'=>214,'code'=>"TC",'name'=>"Turks and Caicos Islands"),
            array('id'=>215,'code'=>"TD",'name'=>"Chad"),
            array('id'=>216,'code'=>"TF",'name'=>"French Southern Territories"),
            array('id'=>217,'code'=>"TG",'name'=>"Togo"),
            array('id'=>218,'code'=>"TH",'name'=>"Thailand"),
            array('id'=>219,'code'=>"TJ",'name'=>"Tajikistan"),
            array('id'=>220,'code'=>"TK",'name'=>"Tokelau"),
            array('id'=>221,'code'=>"TL",'name'=>"East Timor"),
            array('id'=>222,'code'=>"TM",'name'=>"Turkmenistan"),
            array('id'=>223,'code'=>"TN",'name'=>"Tunisia"),
            array('id'=>224,'code'=>"TO",'name'=>"Tonga"),
            array('id'=>225,'code'=>"TR",'name'=>"Turkey"),
            array('id'=>226,'code'=>"TT",'name'=>"Trinidad and Tobago"),
            array('id'=>227,'code'=>"TV",'name'=>"Tuvalu"),
            array('id'=>228,'code'=>"TW",'name'=>"Taiwan"),
            array('id'=>229,'code'=>"TZ",'name'=>"Tanzania"),
            array('id'=>230,'code'=>"UA",'name'=>"Ukraine"),
            array('id'=>231,'code'=>"UG",'name'=>"Uganda"),
            array('id'=>232,'code'=>"UM",'name'=>"United States Minor Outlying Islands"),
            array('id'=>233,'code'=>"US",'name'=>"United States"),
            array('id'=>234,'code'=>"UY",'name'=>"Uruguay"),
            array('id'=>235,'code'=>"UZ",'name'=>"Uzbekistan"),
            array('id'=>236,'code'=>"VA",'name'=>"Vatican"),
            array('id'=>237,'code'=>"VC",'name'=>"Saint Vincent and the Grenadines"),
            array('id'=>238,'code'=>"VE",'name'=>"Venezuela"),
            array('id'=>239,'code'=>"VG",'name'=>"British Virgin Islands"),
            array('id'=>240,'code'=>"VI",'name'=>"U.S. Virgin Islands"),
            array('id'=>241,'code'=>"VN",'name'=>"Vietnam"),
            array('id'=>242,'code'=>"VU",'name'=>"Vanuatu"),
            array('id'=>243,'code'=>"WF",'name'=>"Wallis and Futuna"),
            array('id'=>244,'code'=>"WS",'name'=>"Samoa"),
            array('id'=>245,'code'=>"YE",'name'=>"Yemen"),
            array('id'=>246,'code'=>"YT",'name'=>"Mayotte"),
            array('id'=>247,'code'=>"ZA",'name'=>"South Africa"),
            array('id'=>248,'code'=>"ZM",'name'=>"Zambia"),
            array('id'=>249,'code'=>"ZW",'name'=>"Zimbabwe"),
            array('id'=>250,'code'=>"CS",'name'=>"Serbia and Montenegro"),
            array('id'=>251,'code'=>"AN",'name'=>"Netherlands Antilles")
        );

        DB::table('countries')->insert($countries);
    }
}