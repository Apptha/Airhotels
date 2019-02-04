<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Airhotels
 * @version     0.2.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Airhotels_Block_Property_Form extends Mage_Catalog_Block_Product_Abstract {
 
 /**
  * Get the time zone
  * 
  * @return string
  */
 public function getTimezone() {
     /**
      * Getting customerId from session
      * @var unknown
      */
  $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
  /**
   * Load customer photo from customerphoto collection
   * Using customerId   
   */
  $customerData = Mage::getModel ( 'airhotels/product' )->getCustomerPictureById ( $customerId );
  foreach ( $customerData as $customer ) {
   $timeZone = $customer ['time_zone'];
  }
  /** 
   * Setting $timeZone   
   */
  $html = "";
  if (isset ( $timeZone )) {
   $html .= '<option value="0" "selected">' . $this->__ ( "-- Select Timezone --" ) . '</option>';
  }
  $timezones = array (
    'Pacific/Midway' => "(GMT-11:00) Midway Island",
    'US/Samoa' => "(GMT-11:00) Samoa",
    'US/Hawaii' => "(GMT-10:00) Hawaii",
    'US/Alaska' => "(GMT-09:00) Alaska",
    'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
    'America/Tijuana' => "(GMT-08:00) Tijuana",
    'US/Arizona' => "(GMT-07:00) Arizona",
    'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
    'America/Chihuahua' => "(GMT-07:00) Chihuahua",
    'America/Mazatlan' => "(GMT-07:00) Mazatlan",
    'America/Mexico_City' => "(GMT-06:00) Mexico City",
    'America/Monterrey' => "(GMT-06:00) Monterrey",
    'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
    'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
    'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
    'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
    'America/Bogota' => "(GMT-05:00) Bogota",
    'America/Lima' => "(GMT-05:00) Lima",
    'America/Caracas' => "(GMT-04:30) Caracas",
    'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
    'America/La_Paz' => "(GMT-04:00) La Paz",
    'America/Santiago' => "(GMT-04:00) Santiago",
    'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
    'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
    'Greenland' => "(GMT-03:00) Greenland",
    'Atlantic/Stanley' => "(GMT-02:00) Stanley",
    'Atlantic/Azores' => "(GMT-01:00) Azores",
    'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
    'Africa/Casablanca' => "(GMT) Casablanca",
    'Europe/Dublin' => "(GMT) Dublin",
    'Europe/Lisbon' => "(GMT) Lisbon",
    'Europe/London' => "(GMT) London",
    'Africa/Monrovia' => "(GMT) Monrovia",
    'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
    'Europe/Belgrade' => "(GMT+01:00) Belgrade",
    'Europe/Berlin' => "(GMT+01:00) Berlin",
    'Europe/Bratislava' => "(GMT+01:00) Bratislava",
    'Europe/Brussels' => "(GMT+01:00) Brussels",
    'Europe/Budapest' => "(GMT+01:00) Budapest",
    'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
    'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
    'Europe/Madrid' => "(GMT+01:00) Madrid",
    'Europe/Paris' => "(GMT+01:00) Paris",
    'Europe/Prague' => "(GMT+01:00) Prague",
    'Europe/Rome' => "(GMT+01:00) Rome",
    'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
    'Europe/Skopje' => "(GMT+01:00) Skopje",
    'Europe/Stockholm' => "(GMT+01:00) Stockholm",
    'Europe/Vienna' => "(GMT+01:00) Vienna",
    'Europe/Warsaw' => "(GMT+01:00) Warsaw",
    'Europe/Zagreb' => "(GMT+01:00) Zagreb",
    'Europe/Athens' => "(GMT+02:00) Athens",
    'Europe/Bucharest' => "(GMT+02:00) Bucharest",
    'Africa/Cairo' => "(GMT+02:00) Cairo",
    'Africa/Harare' => "(GMT+02:00) Harare",
    'Europe/Helsinki' => "(GMT+02:00) Helsinki",
    'Europe/Istanbul' => "(GMT+02:00) Istanbul",
    'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
    'Europe/Kiev' => "(GMT+02:00) Kyiv",
    'Europe/Minsk' => "(GMT+02:00) Minsk",
    'Europe/Riga' => "(GMT+02:00) Riga",
    'Europe/Sofia' => "(GMT+02:00) Sofia",
    'Europe/Tallinn' => "(GMT+02:00) Tallinn",
    'Europe/Vilnius' => "(GMT+02:00) Vilnius",
    'Asia/Baghdad' => "(GMT+03:00) Baghdad",
    'Asia/Kuwait' => "(GMT+03:00) Kuwait",
    'Africa/Nairobi' => "(GMT+03:00) Nairobi",
    'Asia/Riyadh' => "(GMT+03:00) Riyadh",
    'Europe/Moscow' => "(GMT+03:00) Moscow",
    'Asia/Tehran' => "(GMT+03:30) Tehran",
    'Asia/Baku' => "(GMT+04:00) Baku",
    'Europe/Volgograd' => "(GMT+04:00) Volgograd",
    'Asia/Muscat' => "(GMT+04:00) Muscat",
    'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
    'Asia/Yerevan' => "(GMT+04:00) Yerevan",
    'Asia/Kabul' => "(GMT+04:30) Kabul",
    'Asia/Karachi' => "(GMT+05:00) Karachi",
    'Asia/Tashkent' => "(GMT+05:00) Tashkent",
    'Asia/Chennai' => "(GMT+05:30) Chennai",
    'Asia/Kolkata' => "(GMT+05:30) Kolkata",
    'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
    'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg",
    'Asia/Almaty' => "(GMT+06:00) Almaty",
    'Asia/Dhaka' => "(GMT+06:00) Dhaka",
    'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
    'Asia/Bangkok' => "(GMT+07:00) Bangkok",
    'Asia/Jakarta' => "(GMT+07:00) Jakarta",
    'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
    'Asia/Chongqing' => "(GMT+08:00) Chongqing",
    'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
    'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
    'Australia/Perth' => "(GMT+08:00) Perth",
    'Asia/Singapore' => "(GMT+08:00) Singapore",
    'Asia/Taipei' => "(GMT+08:00) Taipei",
    'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
    'Asia/Urumqi' => "(GMT+08:00) Urumqi",
    'Asia/Irkutsk' => "(GMT+09:00) Irkutsk",
    'Asia/Seoul' => "(GMT+09:00) Seoul",
    'Asia/Tokyo' => "(GMT+09:00) Tokyo",
    'Australia/Adelaide' => "(GMT+09:30) Adelaide",
    'Australia/Darwin' => "(GMT+09:30) Darwin",
    'Asia/Yakutsk' => "(GMT+10:00) Yakutsk",
    'Australia/Brisbane' => "(GMT+10:00) Brisbane",
    'Australia/Canberra' => "(GMT+10:00) Canberra",
    'Pacific/Guam' => "(GMT+10:00) Guam",
    'Australia/Hobart' => "(GMT+10:00) Hobart",
    'Australia/Melbourne' => "(GMT+10:00) Melbourne",
    'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
    'Australia/Sydney' => "(GMT+10:00) Sydney",
    'Asia/Vladivostok' => "(GMT+11:00) Vladivostok",
    'Asia/Magadan' => "(GMT+12:00) Magadan",
    'Pacific/Auckland' => "(GMT+12:00) Auckland",
    'Pacific/Fiji' => "(GMT+12:00) Fiji" 
  );
  foreach ( $timezones as $timezone ) {
   $html .= '<option value="' . $this->__ ( $timezone ) . '"';
   if ($customerData [0] ['time_zone'] == $timezone) {
    $html .= "selected";
   }
   $html .= '>' . $this->__ ( $timezone ) . '</option>';
  }
  return $html;
 }
 /**
  * Country code
  * 
  * @return string
  */
 public function getIsdCode() {
     /**
      * Getting ISD for country
      * @var unknown
      */
  $countryIsdCode = "";
  $countryIsdCode .= "countryCodeList['AX']='+358';";
  $countryIsdCode .= "countryCodeList['AL']='+355';";
  $countryIsdCode .= "countryCodeList['DZ']='+213';";
  $countryIsdCode .= "countryCodeList['AS']='+684';";
  $countryIsdCode .= "countryCodeList['AD']='+376';";
  $countryIsdCode .= "countryCodeList['AO']='+244';";
  $countryIsdCode .= "countryCodeList['AI']='+1';";
  $countryIsdCode .= "countryCodeList['AQ']='+672';";
  $countryIsdCode .= "countryCodeList['AG']='+1';";
  $countryIsdCode .= "countryCodeList['AF']='+93';";
  $countryIsdCode .= "countryCodeList['AR']='+54';";
  $countryIsdCode .= "countryCodeList['AM']='+374';";
  $countryIsdCode .= "countryCodeList['AW']='+297';";
  $countryIsdCode .= "countryCodeList['AU']='+61';";
  $countryIsdCode .= "countryCodeList['AT']='+43';";
  $countryIsdCode .= "countryCodeList['AZ']='+994';";
  $countryIsdCode .= "countryCodeList['BS']='+1';";
  $countryIsdCode .= "countryCodeList['BH']='+973';";
  $countryIsdCode .= "countryCodeList['BD']='+880';";
  $countryIsdCode .= "countryCodeList['BB']='+1';";
  $countryIsdCode .= "countryCodeList['BY']='+375';";
  $countryIsdCode .= "countryCodeList['BE']='+32';";
  $countryIsdCode .= "countryCodeList['BZ']='+501';";
  $countryIsdCode .= "countryCodeList['BJ']='+229';";
  $countryIsdCode .= "countryCodeList['BM']='+1';";
  $countryIsdCode .= "countryCodeList['BT']='+975';";
  $countryIsdCode .= "countryCodeList['BO']='+591';";
  $countryIsdCode .= "countryCodeList['BA']='+387';";
  $countryIsdCode .= "countryCodeList['BW']='+267';";
  $countryIsdCode .= "countryCodeList['BV']='+47';";
  $countryIsdCode .= "countryCodeList['BR']='+55';";
  $countryIsdCode .= "countryCodeList['IO']='+246';";
  $countryIsdCode .= "countryCodeList['BN']='+673';";
  $countryIsdCode .= "countryCodeList['BG']='+359';";
  $countryIsdCode .= "countryCodeList['BF']='+226';";
  $countryIsdCode .= "countryCodeList['BI']='+257';";
  $countryIsdCode .= "countryCodeList['KH']='+855';";
  $countryIsdCode .= "countryCodeList['CM']='+237';";
  $countryIsdCode .= "countryCodeList['CA']='+1';";
  $countryIsdCode .= "countryCodeList['']='+238';";
  $countryIsdCode .= "countryCodeList['KY']='+1';";
  $countryIsdCode .= "countryCodeList['CF']='+236';";
  $countryIsdCode .= "countryCodeList['TD']='+235';";
  $countryIsdCode .= "countryCodeList['CL']='+56';";
  $countryIsdCode .= "countryCodeList['CN']='+86';";
  $countryIsdCode .= "countryCodeList['CX']='+';";
  $countryIsdCode .= "countryCodeList['CC']='+';";
  $countryIsdCode .= "countryCodeList['CO']='+57';";
  $countryIsdCode .= "countryCodeList['KM']='+269';";
  $countryIsdCode .= "countryCodeList['CG']='+242';";
  $countryIsdCode .= "countryCodeList['CD']='+243';";
  $countryIsdCode .= "countryCodeList['CK']='+682';";
  $countryIsdCode .= "countryCodeList['CR']='+506';";
  $countryIsdCode .= "countryCodeList['CI']='+225';";
  $countryIsdCode .= "countryCodeList['HR']='+385';";
  $countryIsdCode .= "countryCodeList['CU']='+53';";
  $countryIsdCode .= "countryCodeList['CY']='+357';";
  $countryIsdCode .= "countryCodeList['CZ']='+420';";
  $countryIsdCode .= "countryCodeList['DK']='+45';";
  $countryIsdCode .= "countryCodeList['DJ']='+253';";
  $countryIsdCode .= "countryCodeList['DM']='+1';";
  $countryIsdCode .= "countryCodeList['DO']='+1';";
  $countryIsdCode .= "countryCodeList['EC']='+593';";
  $countryIsdCode .= "countryCodeList['EG']='+20';";
  $countryIsdCode .= "countryCodeList['SV']='+503';";
  $countryIsdCode .= "countryCodeList['GQ']='+240';";
  $countryIsdCode .= "countryCodeList['ER']='+291';";
  $countryIsdCode .= "countryCodeList['EE']='+201';";
  $countryIsdCode .= "countryCodeList['ET']='+251';";
  $countryIsdCode .= "countryCodeList['FK']='+500';";
  $countryIsdCode .= "countryCodeList['FO']='+298';";
  $countryIsdCode .= "countryCodeList['FJ']='+679';";
  $countryIsdCode .= "countryCodeList['FI']='+358';";
  $countryIsdCode .= "countryCodeList['FR']='+33';";
  $countryIsdCode .= "countryCodeList['FX']='+33';";
  $countryIsdCode .= "countryCodeList['GF']='+594';";
  $countryIsdCode .= "countryCodeList['PF']='+689';";
  $countryIsdCode .= "countryCodeList['TF']='+33';";
  $countryIsdCode .= "countryCodeList['GA']='+241';";
  $countryIsdCode .= "countryCodeList['GM']='+220';";
  $countryIsdCode .= "countryCodeList['GE']='+995';";
  $countryIsdCode .= "countryCodeList['DE']='+49';";
  $countryIsdCode .= "countryCodeList['GG']='+44';";
  $countryIsdCode .= "countryCodeList['GH']='+233';";
  $countryIsdCode .= "countryCodeList['GI']='+350';";
  $countryIsdCode .= "countryCodeList['GR']='+30';";
  $countryIsdCode .= "countryCodeList['GL']='+299';";
  $countryIsdCode .= "countryCodeList['GD']='+1';";
  $countryIsdCode .= $this->CountryCodeOne ();
  return $countryIsdCode .= $this->CountryCodeTwo (); 
 }
 /**
  * get the Country code
  * 
  * @return string
  */
 public function CountryCodeOne() {
     /**
      * Getting ISD for country
      * @var unknown
      */
  $countryIsdCode = "";
  $countryIsdCode .= "countryCodeList['GP']='+590';";
  $countryIsdCode .= "countryCodeList['GU']='+1';";
  $countryIsdCode .= "countryCodeList['GT']='+502';";
  $countryIsdCode .= "countryCodeList['GN']='+224';";
  $countryIsdCode .= "countryCodeList['GW']='+245';";
  $countryIsdCode .= "countryCodeList['GY']='+592';";
  $countryIsdCode .= "countryCodeList['HT']='+509';";
  $countryIsdCode .= "countryCodeList['HM']='+1';";
  $countryIsdCode .= "countryCodeList['HN']='+504';";
  $countryIsdCode .= "countryCodeList['HK']='+852';";
  $countryIsdCode .= "countryCodeList['HU']='+36';";
  $countryIsdCode .= "countryCodeList['IS']='+354';";
  $countryIsdCode .= "countryCodeList['IN']='+91';";
  $countryIsdCode .= "countryCodeList['ID']='+62';";
  $countryIsdCode .= "countryCodeList['IR']='+98';";
  $countryIsdCode .= "countryCodeList['IQ']='+964';";
  $countryIsdCode .= "countryCodeList['IE']='+353';";
  $countryIsdCode .= "countryCodeList['IL']='+972';";
  $countryIsdCode .= "countryCodeList['IT']='+39';";
  $countryIsdCode .= "countryCodeList['JM']='+1';";
  $countryIsdCode .= "countryCodeList['JP']='+81';";
  $countryIsdCode .= "countryCodeList['JO']='+962';";
  $countryIsdCode .= "countryCodeList['KZ']='+7';";
  $countryIsdCode .= "countryCodeList['KE']='+254';";
  $countryIsdCode .= "countryCodeList['KI']='+686';";
  $countryIsdCode .= "countryCodeList['KR']='+82';";
  $countryIsdCode .= "countryCodeList['KP']='+850';";
  $countryIsdCode .= "countryCodeList['KW']='+965';";
  $countryIsdCode .= "countryCodeList['KG']='+996';";
  $countryIsdCode .= "countryCodeList['LA']='+856';";
  $countryIsdCode .= "countryCodeList['LV']='+371';";
  $countryIsdCode .= "countryCodeList['LB']='+961';";
  $countryIsdCode .= "countryCodeList['LS']='+266';";
  $countryIsdCode .= "countryCodeList['LR']='+231';";
  $countryIsdCode .= "countryCodeList['LY']='+218';";
  $countryIsdCode .= "countryCodeList['LI']='+423';";
  $countryIsdCode .= "countryCodeList['LT']='+370';";
  $countryIsdCode .= "countryCodeList['LU']='+352';";
  $countryIsdCode .= "countryCodeList['MO']='+853';";
  $countryIsdCode .= "countryCodeList['MK']='+389';";
  $countryIsdCode .= "countryCodeList['MG']='+261';";
  $countryIsdCode .= "countryCodeList['MW']='+265';";
  $countryIsdCode .= "countryCodeList['MY']='+60';";
  $countryIsdCode .= "countryCodeList['MV']='+960';";
  $countryIsdCode .= "countryCodeList['ML']='+223';";
  $countryIsdCode .= "countryCodeList['MT']='+356';";
  $countryIsdCode .= "countryCodeList['MH']='+692';";
  $countryIsdCode .= "countryCodeList['MQ']='+596';";
  $countryIsdCode .= "countryCodeList['MR']='+222';";
  $countryIsdCode .= "countryCodeList['MU']='+230';";
  $countryIsdCode .= "countryCodeList['YT']='+269';";
  $countryIsdCode .= "countryCodeList['MX']='+52';";
  $countryIsdCode .= "countryCodeList['FM']='+691';";
  $countryIsdCode .= "countryCodeList['MD']='+373';";
  $countryIsdCode .= "countryCodeList['MC']='+377';";
  $countryIsdCode .= "countryCodeList['MN']='+976';";
  $countryIsdCode .= "countryCodeList['ME']='+382';";
  $countryIsdCode .= "countryCodeList['MS']='+1';";
  $countryIsdCode .= "countryCodeList['MA']='+212';";
  $countryIsdCode .= "countryCodeList['MZ']='+258';";
  $countryIsdCode .= "countryCodeList['MM']='+95';";
  $countryIsdCode .= "countryCodeList['NA']='+264';";
  $countryIsdCode .= "countryCodeList['NR']='+674';";
  $countryIsdCode .= "countryCodeList['NP']='+977';";
  $countryIsdCode .= "countryCodeList['NL']='+31';";
  $countryIsdCode .= "countryCodeList['AN']='+599';";
  $countryIsdCode .= "countryCodeList['NC']='+687';";
  $countryIsdCode .= "countryCodeList['NZ']='+64';";
  $countryIsdCode .= "countryCodeList['NI']='+505';";
  $countryIsdCode .= "countryCodeList['NE']='+227';";
  $countryIsdCode .= "countryCodeList['NG']='+234';";
  $countryIsdCode .= "countryCodeList['NU']='+683';";
  $countryIsdCode .= "countryCodeList['NF']='+6723';";
  $countryIsdCode .= "countryCodeList['MP']='+1';";
  $countryIsdCode .= "countryCodeList['NO']='+47';";
  $countryIsdCode .= "countryCodeList['OM']='+968';";
  $countryIsdCode .= "countryCodeList['PK']='+92';";
  $countryIsdCode .= "countryCodeList['PW']='+680';";
  $countryIsdCode .= "countryCodeList['PS']='+970';";
  $countryIsdCode .= "countryCodeList['PA']='+507';";
  $countryIsdCode .= "countryCodeList['PG']='+675';";
  $countryIsdCode .= "countryCodeList['PY']='+595';";
  $countryIsdCode .= "countryCodeList['PE']='+51';";
  $countryIsdCode .= "countryCodeList['PH']='+63';";
  $countryIsdCode .= "countryCodeList['PN']='+870';";
  $countryIsdCode .= "countryCodeList['PL']='+48';";
  $countryIsdCode .= "countryCodeList['PT']='+351';";
  $countryIsdCode .= "countryCodeList['PR']='+1';";
  $countryIsdCode .= "countryCodeList['QA']='+974';";
  $countryIsdCode .= "countryCodeList['RE']='+262';";
  return $countryIsdCode .= "countryCodeList['RO']='+40';";  
 }
 /**
  * get the Country code
  * 
  * @return string
  */
 public function CountryCodeTwo() {
     /**
      * Getting ISD for country
      * @var unknown
      */
  $countryIsdCode = "";
  $countryIsdCode .= "countryCodeList['RU']='+7';";
  $countryIsdCode .= "countryCodeList['RW']='+250';";
  $countryIsdCode .= "countryCodeList['SH']='+290';";
  $countryIsdCode .= "countryCodeList['KN']='+1';";
  $countryIsdCode .= "countryCodeList['LC']='+1';";
  $countryIsdCode .= "countryCodeList['PM']='+508';";
  $countryIsdCode .= "countryCodeList['VC']='+1';";
  $countryIsdCode .= "countryCodeList['WS']='+685';";
  $countryIsdCode .= "countryCodeList['SM']='+378';";
  $countryIsdCode .= "countryCodeList['ST']='+239';";
  $countryIsdCode .= "countryCodeList['SA']='+966';";
  $countryIsdCode .= "countryCodeList['SN']='+221';";
  $countryIsdCode .= "countryCodeList['RS']='+381';";
  $countryIsdCode .= "countryCodeList['SC']='+248';";
  $countryIsdCode .= "countryCodeList['SL']='+232';";
  $countryIsdCode .= "countryCodeList['SG']='+65';";
  $countryIsdCode .= "countryCodeList['SK']='+421';";
  $countryIsdCode .= "countryCodeList['SI']='+386';";
  $countryIsdCode .= "countryCodeList['SB']='+677';";
  $countryIsdCode .= "countryCodeList['SO']='+252';";
  $countryIsdCode .= "countryCodeList['ZA']='+27';";
  $countryIsdCode .= "countryCodeList['GS']='+44';";
  $countryIsdCode .= "countryCodeList['ES']='+34';";
  $countryIsdCode .= "countryCodeList['LK']='+94';";
  $countryIsdCode .= "countryCodeList['SD']='+249';";
  $countryIsdCode .= "countryCodeList['SR']='+597';";
  $countryIsdCode .= "countryCodeList['SJ']='+79';";
  $countryIsdCode .= "countryCodeList['SZ']='+268';";
  $countryIsdCode .= "countryCodeList['SE']='+46';";
  $countryIsdCode .= "countryCodeList['CH']='+41';";
  $countryIsdCode .= "countryCodeList['SY']='+963';";
  $countryIsdCode .= "countryCodeList['TW']='+886';";
  $countryIsdCode .= "countryCodeList['TJ']='+992';";
  $countryIsdCode .= "countryCodeList['TZ']='+255';";
  $countryIsdCode .= "countryCodeList['TH']='+66';";
  $countryIsdCode .= "countryCodeList['TL']='+670';";
  $countryIsdCode .= "countryCodeList['TG']='+228';";
  $countryIsdCode .= "countryCodeList['TK']='+690';";
  $countryIsdCode .= "countryCodeList['TO']='+676';";
  $countryIsdCode .= "countryCodeList['TT']='+1';";
  $countryIsdCode .= "countryCodeList['TN']='+216';";
  $countryIsdCode .= "countryCodeList['TR']='+90';";
  $countryIsdCode .= "countryCodeList['TM']='+993';";
  $countryIsdCode .= "countryCodeList['TC']='+1';";
  $countryIsdCode .= "countryCodeList['TV']='+688';";
  $countryIsdCode .= "countryCodeList['UG']='+256';";
  $countryIsdCode .= "countryCodeList['UA']='+380';";
  $countryIsdCode .= "countryCodeList['AE']='+971';";
  $countryIsdCode .= "countryCodeList['GB']='+44';";
  $countryIsdCode .= "countryCodeList['US']='+1';";
  $countryIsdCode .= "countryCodeList['UM']='+1';";
  $countryIsdCode .= "countryCodeList['UY']='+598';";
  $countryIsdCode .= "countryCodeList['UZ']='+998';";
  $countryIsdCode .= "countryCodeList['VU']='+678';";
  $countryIsdCode .= "countryCodeList['VA']='+39';";
  $countryIsdCode .= "countryCodeList['VE']='+58';";
  $countryIsdCode .= "countryCodeList['VN']='+84';";
  $countryIsdCode .= "countryCodeList['VG']='+1284';";
  $countryIsdCode .= "countryCodeList['VI']='+1340';";
  $countryIsdCode .= "countryCodeList['WF']='+681';";
  $countryIsdCode .= "countryCodeList['EH']='+212';";
  $countryIsdCode .= "countryCodeList['YE']='+967';";
  $countryIsdCode .= "countryCodeList['ZM']='+260';";
  return $countryIsdCode .= "countryCodeList['ZW']='+263';";  
 }
 /**
  * Function for getting the subscriptiontype model
  * 
  * @return subscriptiontype collection
  */
 public function getSubscriptionTypeModel() {
  /**
   * Get Model for subscription tyep
   */
  return Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ()->addFieldToFilter ( 'status', '1' );
 }
 /**
  * Get the Subscription Collection
  * 
  * @return Array
  */
 public function getSubscripitonCollection() {
     /**
      * Declare subscription array
      * 
      * @var unknown
      */
     $subscriptionId = array();
  $isSubscriptionEnabled = Mage::getStoreConfig ( 'airhotels/subscription/activate_subscription_enable' );
  /**
   * Check if $isSubscriptionEnabled or not
   */
  if ($isSubscriptionEnabled == 0) {
   /**
    * Declaring the subscriptionTypes to array
    */
   $subscriptionTypes = array ();
   /**
    * Called the subscriptiontype COllection
    */
   $subscriptionCollection = Mage::getModel ( 'airhotels/subscriptiontype' )->getCollection ();
   /**
    * Get the Data of Subscription Colletion
    */
   $subscriptionTypes = $subscriptionCollection->getData ();
   foreach ( $subscriptionTypes as $subscriptions ) {
    /**
     * Store id's to SunscriptionID Array
     */
    $subscriptionId [] = $subscriptions ['id'];
   }
   /**
    * Returnming the Subscription Array
    */
   return $subscriptionId;
  }
 } 
 /**
  * Ge the Subsriptipon Method
  */
 /**
  * Fucntion to get 
  * Subscription method
  * 
  * @param unknown $productId
  * @return string
  */
 public function subscriptionMethod($productId) {
     /**
      * init subscription type
      * with array as 0
      * @var unknown
      */
  $_subscriptionType = array (
    0 
  );
  /**
   * Get the product Subscription Model value
   */
  $productSubscriptionCollection = $this->productSubscriptionModel ()->addFieldToFilter ( 'product_id', $productId );
  /**
   * Getting product subscription collection
   */
  /**
   * For loop the product subscription collection 
   * assign it to subscription type array
   */
  foreach ( $productSubscriptionCollection as $productSubscription ) {
   $_subscriptionType [] = $productSubscription ['subscription_type'];
  }
  /**
   * check if $_subscriptionType count is greater than one
   * @var $subscription
   */
  if (count ( $_subscriptionType ) >= 1) {
      /**
       * If the subscription type array has count 
       * more than 1
       * @var unknown
       */
      /**
       * Get the subscription type model collections 
       * based on the subscription type 
       * @var unknown
       */
   $subscription = $this->subscriptionTypeModel ()->addFieldToFilter ( 'id', array (
     $_subscriptionType 
   ) );
  } else {
   $subscription = '';
  }
  /**
   * return the subscription array
   */
  return $subscription;
 } 
 /**
  * Get the Subscription type Mode
  */
 /**
  * Function to get data
  * from subscription type model
  * @return subscription type array
  */
 public function subscriptionTypeModel() {
     /**
      * return subscriptionTypeCollection
      */
     /**
      * get the subscription type
      * Collection which will be used to 
      * assign for products
      */
  return Mage::getModel ( 'airhotels/subscriptiontype' )->subscriptionTypeCollection ();
 } 
 /**
  * Get the Product Subscription type Mode
  */
 /**
  * Function to get the product subscription model
  * param @return Subscription
  * Product collection
  */
 public function productSubscriptionModel() {
     /**
      * return productSubscriptionCollection
      */
     /**
      * get the subscritption collection
      * allocated for products
      * and return to corresponding model
      */
  return Mage::getModel ( 'airhotels/subscriptiontype' )->productSubscriptionCollection ();
 }
 /**
  * Get product data collection
  *
  * Passed the product id to get product details
  *
  * @param int $productId
  *            Return product details as array
  * @return array
  */
 public function getProductData($productId, $storeId) {
     /**
      * Getting product collection data
      */
     /**
      * load and return the products
      * Based on the product entity id
      */
  return Mage::getModel ( 'catalog/product' )->setStoreId ( $storeId )->load ( $productId );
 }
}