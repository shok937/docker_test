<?php

// Движок SMS Agrad
define('ROOT_DIR', dirname(__FILE__));
require_once(ROOT_DIR . '/phplibs/class.mtsclient.php');

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

$_cache_smsstore_dir = dirname(__FILE__).'/sms/data/';

// save sms-verify array
function save_smsverify($_arr, $_filename)
{
	global $_cache_smsstore_dir;
	// check arr
	if (!isset($_arr) || empty($_arr)) { return false; }

	file_put_contents($_cache_smsstore_dir.$_filename, serialize($_arr), LOCK_EX);
}

// load sms-verify array
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

function sms_get_visited_user_IP()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return (string)$ip;
}

// $_filename FORMAT: {phone}-{timestamp}-{rid}.agrad

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$_get_f = isset($_GET['f']) && !empty($_GET['f']) ? $_GET['f'] : false;
$_get_phone = isset($_GET['phone']) && !empty($_GET['phone']) ? $_GET['phone'] : false;
$_get_timestamp = isset($_GET['timestamp']) && !empty($_GET['timestamp']) ? $_GET['timestamp'] : false;
$_get_rid = isset($_GET['rid']) && !empty($_GET['rid']) ? $_GET['rid'] : false;
$_get_code = isset($_GET['code']) && !empty($_GET['code']) ? $_GET['code'] : false;

// filters
$_get_phone = preg_replace('/[^0-9]/', '', $_get_phone);
$_get_timestamp = preg_replace('/[^0-9]/', '', $_get_timestamp);
$_get_rid = preg_replace('/[^a-zA-Z0-9\s]/', '', $_get_rid);

if ($_get_f == 'record' && $_get_phone !== false && $_get_timestamp !== false && $_get_rid !== false)
{
  $_v_filename = $_get_phone.'-'.$_get_timestamp.'-'.$_get_rid.'.agrad';
  $_file_sms_arr = load_smsverify($_v_filename);
  if (!file_exists($_cache_smsstore_dir.$_v_filename) || (file_exists($_cache_smsstore_dir.$_v_filename) && $_file_sms_arr['create_time'] < (time()-(2*60))) ) // if isset - error, timeleft - 2 minutes
  { 

    $_this_user_ip = sms_get_visited_user_IP(); if (empty($_this_user_ip)) { $_this_user_ip = 'empty'; }
    // $_ipf_deny_filename = 'ip_denylist.agrad';
    // $_ipf_deny_arr = load_smsverify($_ipf_filename);
    $_ipf_access_filename = 'ip_accessdata.agrad';
    $_ipf_access_arr = load_smsverify($_ipf_access_filename);
    if ($_ipf_access_arr == false) { $_ipf_access_arr = array(); }
    $_this_user_ip_updated = false;
    foreach ($_ipf_access_arr as $_i_k => $_i_v) {
      if ($_i_k == $_this_user_ip) {

        // this IP
        if ( ($_i_v['request_date']+60) > time() ) {
          // работаем с текущими 60 с.
          $_ipf_access_arr[$_i_k]['try_requests']++;
        } else {
          // обновляем request_date
          $_ipf_access_arr[$_i_k]['request_date'] = time();
          $_ipf_access_arr[$_i_k]['try_requests'] = 1;
        }

        $_this_user_ip_updated = true;
      
      } else {

        // other IP (check timeout and clear)
        if ( ($_i_v['request_date']+(60*60*24)) < time() ) {
          unset($_ipf_access_arr[$_i_k]);
        }

      }
    }
    if ($_this_user_ip_updated === false) {
      // создаем новый 
      $_ipf_access_arr[$_this_user_ip]['request_date'] = time();
      $_ipf_access_arr[$_this_user_ip]['try_requests'] = 1;
    }
    // var_dump($_ipf_access_arr);
    // save ipf
    save_smsverify($_ipf_access_arr, $_ipf_access_filename);

    // if (isset($_ipf_deny_arr[$_this_user_ip])) 
    if ($_ipf_access_arr[$_this_user_ip]['try_requests'] > 3)
    {
      echo json_encode(array("status"=>"error", "info"=>"ip_limit_exceeded"));
      exit();
    }
    else
    {

      //create new store
      $_verify_code = mt_rand(10000, 99999);
      $_sms_arr = array(
        "create_time" => time(), // дата отсылки смс (unix)
        "phone" => $_get_phone, // телефон для проверки 79990001122
        "timestamp" => $_get_timestamp, // дата начала заполнения формы онлайн записи (js unix ts - millisec)
        "rid" => $_get_rid, // record form id
        "status" => "new", // status: new, send, confirm
        "verify_code" => $_verify_code,
        "try_count" => 1,
      );
      
      // send sms
      // test - /sms.php?f=record&rid=GWNXXPYFTVPA9511819000&timestamp=1558698035867&phone=79999999999
      $_sms_message = 'Код подтверждения Автоград: '.$_verify_code;
      //$_agr_m_link = 'https://api.mcommunicator.ru/m2m/m2m_api.asmx/SendMessage?msid='.$_get_phone.'&message='.$_sms_message.'&naming=AUTOGRAD&login=agrad&password='.md5("ySPLUinsu1");
      //$_fileData_sms = file_get_contents($_agr_m_link);
      //var_dump($_fileData_sms);
      
      // post to mts
      // $_myCurl = curl_init();
      // curl_setopt_array($_myCurl, array(
      //     CURLOPT_URL => 'https://api.mcommunicator.ru/m2m/m2m_api.asmx/SendMessage',
      //     CURLOPT_RETURNTRANSFER => true,
      //     CURLOPT_POST => true,
      //     CURLOPT_POSTFIELDS => http_build_query(array('msid'=>$_get_phone, 'message'=>$_sms_message, 'naming'=>'AUTOGRAD', 'login'=>'agrad', 'password'=>md5("k7J2Xfq3RtMCd8")))
      // ));
      // $_fileData_sms = curl_exec($_myCurl);
      // curl_close($_myCurl);
      
      // отправка сообщения
      if (!isset($_mtsclient)) {
        $_mtsclient = new MTSClient();
      }
      $_sid = $_mtsclient->sendSms("AUTOGRAD", $_sms_message, $_get_phone); // отправка смс
      // если $_sid == 0, то была ошибка запроса

      $_fileData_sms = false;
      if ($_sid != "0") {
        // получение статуса по id сообщения
        // $status = $_mtsclient->getSmsInfo([$_sid]);
        // var_dump($status);
        $_sms_arr["status"] = 'send'; // set status sended
        $_fileData_sms = true;
      }

  /*    // status
      if ($_fileData_sms === false)
      {
        // ??
      }
      else
      {
        // unserialize
        //$_this_arr = json_decode($_fileData_sms);
          
        $_sms_arr["status"] = 'send'; // set status sended
      }*/
      
      // save data
      save_smsverify($_sms_arr, $_v_filename);
      
      if ($_fileData_sms === false)
        echo json_encode(array("status"=>"error", "info"=>"bad_SMS_response")); // GET по отправке SMS вернул ошибку или не ответил
      else
        echo json_encode(array("status"=>"ok"));
    }

  }
  else
  {
    echo json_encode(array("status"=>"error", "info"=>"double_record")); // отбиваем ошибку - второй раз создается файл на отправку SMS
  }
}
else if ($_get_f == 'verify' && $_get_phone !== false && $_get_timestamp !== false && $_get_rid !== false && $_get_code !== false)
{
  $_v_filename = $_get_phone.'-'.$_get_timestamp.'-'.$_get_rid.'.agrad';
  $_v_arr = load_smsverify($_v_filename);
  if ($_v_arr !== false && is_array($_v_arr) && isset($_v_arr['create_time']))
  { 
    if ($_v_arr['try_count'] < 4) {
      // diff
      if ($_v_arr['create_time'] > (time()-60*10)) // актуально только в течение 10 минут после создания
      {
        if ($_get_code == $_v_arr['verify_code'])
        {
          // checked, set status is confirm (update file)
          $_v_arr["status"] = "confirm";
          // file_put_contents($_cache_smsstore_dir.$_v_filename, serialize($_v_arr), LOCK_EX);
          
          echo json_encode(array("status"=>"ok"));
        }
        else
          echo json_encode(array("status"=>"error", "info"=>"wrong_code")); // введён не тот код подтверждения
      } else {
        echo json_encode(array("status"=>"error", "info"=>"operation_timeout")); // истёк срок действия подтверждения
      }
    } else {
      echo json_encode(array("status"=>"error", "info"=>"try_limit_exceeded")); // количество попыток превышено
    }

    // save try_count and all data
    $_v_arr['try_count']++;
    file_put_contents($_cache_smsstore_dir.$_v_filename, serialize($_v_arr), LOCK_EX);

  }
  else
    echo json_encode(array("status"=>"error", "info"=>"not_found_smsverification")); // не найден файл с массивом по данному подтверждению телефона
}
else
{
  echo json_encode(array("status"=>"error", "info"=>"empty"));
}

?>