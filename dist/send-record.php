<?php

// Отправка записи Agrad

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

// load sms-verify array
$_cache_smsstore_dir = dirname(__FILE__).'/sms/data/';
function load_smsverify($_filename)
{
	global $_cache_smsstore_dir;
	// check arr
	if (!isset($_filename) || empty($_filename)) { return false; }

	if (file_exists($_cache_smsstore_dir.$_filename))
	{
    // load arr from file
    $_fileData = file_get_contents($_cache_smsstore_dir.$_filename);
    
    if ($_fileData === false)
      return false;
    else
    {
      // unserialize
      $_fileDataUns = unserialize($_fileData);
      
      return $_fileDataUns;
    }
	}
	else
    return false;
}

function save_sendrecord_info_to_logfile($textinfo='') 
{
  if (!isset($textinfo) || empty($textinfo)) return false;

  $_logfilename = 'add_record';

  $logline = '['.date('Y-m-d H:i:s') . '] '.$textinfo;
  file_put_contents(__DIR__ . '/aglogs/'.$_logfilename.'_'.date('Y-m-d').'.log', $logline . PHP_EOL, FILE_APPEND);

  return true;

}

// $_api_url = 'http://80.91.16.218:2060/'; //ssl 2070
$_api_url = 'http://192.168.5.162:2060/'; // test

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$_get_f = isset($_GET['f']) && !empty($_GET['f']) && $_GET['f']=='m' ? $_GET['f'] : false;
$_get_data = isset($_REQUEST['d']) && !empty($_REQUEST['d']) ? $_REQUEST['d'] : false; // need filtering or no?
$_get_record_type = isset($_GET['record_type']) && !empty($_GET['record_type']) ? $_GET['record_type'] : '';

if ($_get_f !== false && $_get_data !== false)
{
  $_get_phone = isset($_GET['phone']) && !empty($_GET['phone']) ? $_GET['phone'] : false;
  $_get_timestamp = isset($_GET['timestamp']) && !empty($_GET['timestamp']) ? $_GET['timestamp'] : false;
  $_get_rid = isset($_GET['rid']) && !empty($_GET['rid']) ? $_GET['rid'] : false;
  
  $_v_filename = $_get_phone.'-'.$_get_timestamp.'-'.$_get_rid.'.agrad';
  $_v_arr = load_smsverify($_v_filename);
  
  // check phone verification (status == confirm && create_time 10+1 min diff)
  if ($_v_arr !== false && is_array($_v_arr) && isset($_v_arr['status'], $_v_arr['create_time']) && $_v_arr['status'] == 'confirm' && $_v_arr['create_time'] > (time()-60*11))
  {
    // send record
    $_f_array = array();
    
    $_log_message = '===startstring==='.PHP_EOL;
    
    // method name - standart or predict
    $_api_recordmethodname = 'set_new_online_record';
    if ($_get_record_type === 'preto') 
    { 
      
      //predict record
      $_api_recordmethodname = 'set_new_smart_crm_record'; 
      
      $data = array('data' => $_get_data);
      
      $_log_message .= 'Новая запись (POST, rid - '.$_get_rid.'): URL - '.$_api_url.$_api_recordmethodname.', DATA - '.htmlentities(urlencode($_get_data)).PHP_EOL;
      $_log_message .= 'DATA JSON (rid - '.$_get_rid.'): '.json_encode($_get_data).PHP_EOL;
      $_r_ref = false;
      
      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' =>  http_build_query($data)
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($_api_url.$_api_recordmethodname, false, $context);
      if ($result === FALSE) 
      { 
        echo json_encode(array("status"=>"error", "info"=>"bad_backend_response")); // питон отдал ошибку или не загрузился
      }
      else
      {
        // unserialize
        $_this_arr = json_decode($result, true);
        
        if (isset($_this_arr['r_ref']) && strlen($_this_arr['r_ref']) > 5)
        {
          $_r_ref = $_this_arr['r_ref'];
        }
        
        echo json_encode($_this_arr);
      }

      $_fileData = $result;
      
    }
    elseif ($_get_record_type === 'akrrecord')
    {
      
      //predict record
      $_api_recordmethodname = 'akr_set_new_smartcrm_record'; 
      
      $data = array('data' => $_get_data);
      
      $_log_message .= 'Новая запись (POST, rid - '.$_get_rid.'): URL - '.$_api_url.$_api_recordmethodname.', DATA - '.htmlentities(urlencode($_get_data)).PHP_EOL;
      $_log_message .= 'DATA JSON (rid - '.$_get_rid.'): '.json_encode($_get_data).PHP_EOL;
      $_r_ref = false;
      
      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' =>  http_build_query($data)
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($_api_url.$_api_recordmethodname, false, $context);
      if ($result === FALSE) 
      { 
        echo json_encode(array("status"=>"error", "info"=>"bad_backend_response")); // питон отдал ошибку или не загрузился
      }
      else
      {
        // unserialize
        $_this_arr = json_decode($result, true);
        
        if (isset($_this_arr['r_ref']) && strlen($_this_arr['r_ref']) > 5)
        {
          $_r_ref = $_this_arr['r_ref'];
        }
        
        echo json_encode($_this_arr);
      }

      $_fileData = $result;

    }
    elseif ($_get_record_type === 'rerecord')
    {
      // re record

      //predict record
      $_api_recordmethodname = 'set_re_record_smart'; 

      // send GET
      $_sendrecord_str = $_api_url.$_api_recordmethodname.'?data='.htmlentities(urlencode($_get_data));
      $_log_message .= 'Новая запись (GET, rid - '.$_get_rid.'): '.$_sendrecord_str.PHP_EOL;
      $_log_message .= 'DATA JSON (rid - '.$_get_rid.'): '.json_encode($_get_data).PHP_EOL;
      $_r_ref = false;
      $_fileData = file_get_contents($_sendrecord_str);
      
      if ($_fileData == false)
        echo json_encode(array("status"=>"error", "info"=>"bad_backend_response")); // питон отдал ошибку или не загрузился
      else
      {
        // unserialize
        $_this_arr = json_decode($_fileData, true);
        
        if (isset($_this_arr['r_ref']) && strlen($_this_arr['r_ref']) > 5)
        {
          $_r_ref = $_this_arr['r_ref'];
        }
          
        echo json_encode($_this_arr);
      }
    }
    else
    {
      // standart record
      $_sendrecord_str = $_api_url.$_api_recordmethodname.'?data='.htmlentities(urlencode($_get_data));
      $_log_message .= 'Новая запись (GET, rid - '.$_get_rid.'): '.$_sendrecord_str.PHP_EOL;
      $_log_message .= 'DATA JSON (rid - '.$_get_rid.'): '.json_encode($_get_data).PHP_EOL;
      $_r_ref = false;
      $_fileData = file_get_contents($_sendrecord_str);
      
      if ($_fileData == false)
        echo json_encode(array("status"=>"error", "info"=>"bad_backend_response")); // питон отдал ошибку или не загрузился
      else
      {
        // unserialize
        $_this_arr = json_decode($_fileData, true);
        
        if (isset($_this_arr['r_ref']) && strlen($_this_arr['r_ref']) > 5)
        {
          $_r_ref = $_this_arr['r_ref'];
        }
          
        echo json_encode($_this_arr);
      }
    
    }
    
    $_log_message .= 'Результат записи (rid - '.$_get_rid.'): '.( $_r_ref != false && strlen($_r_ref) > 5 ? 'УСПЕШНАЯ ЗАПИСЬ (ref: '.$_r_ref.')' : 'ОШИБКА ЗАПИСИ' ).PHP_EOL;
    $_log_message .= 'Ответ API_ONES: '.var_export($_fileData, true).PHP_EOL;
    $_log_message .= '===endstring===';
    // write to log 
    save_sendrecord_info_to_logfile($_log_message);
    
    //var_dump($_f_array);
    //echo json_encode($_f_array);
  }
  else
    echo json_encode(array("status"=>"error", "info"=>"phone_verification_failed"));
}
else
{
  echo json_encode(array("status"=>"error", "info"=>"empty"));
}

?>