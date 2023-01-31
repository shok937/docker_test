<?php

// Отправка метки, что "Это не мой авто" в предложении записи на ТО, Agrad

// debug mode (show errors)
if (isset($_GET['debug']) && $_GET['debug'] == 'agrad')
{
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
}
else
{
  error_reporting(0);
  ini_set('display_errors', 0);
}

$_api_url = 'http://80.91.16.218:2060/'; //ssl 2070
//$_api_url = 'http://192.168.5.134:2060/'; // test http://192.168.5.134:2060/

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$_get_f = isset($_GET['f']) && !empty($_GET['f']) && $_GET['f']=='m' ? $_GET['f'] : false;
$_get_data = isset($_GET['d']) && !empty($_GET['d']) ? $_GET['d'] : false; // need filtering or no?

// load data from python
$_fileData = file_get_contents($_api_url.'del_car_from_client?data='.htmlentities(urlencode($_get_data)));

if ($_fileData == false)
  echo json_encode(array("status"=>"error", "info"=>"bad_backend_response")); // питон отдал ошибку или не загрузился
else
{
  // unserialize
  $_this_arr = json_decode($_fileData);
  
  echo json_encode($_this_arr);
}

?>