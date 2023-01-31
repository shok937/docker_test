<?php
define('AGRAD', 'yep!');
// Agrad, предложение записи на ТО
// if (!defined('AGRAD')) {exit('err');}

// */5 * * * * wget -qO- https://booking.agrad.ru/aor-predict-generate-and-send.php &> /dev/null


// exit...
exit();


// libs
require_once(dirname(__FILE__) . '/phplibs/class.simpleDB.php');
require_once(dirname(__FILE__) . '/phplibs/class.simpleMysqli.php');
// settings
require_once(dirname(__FILE__) . '/agcore/agsettings.php');



function generate_random_string($length = 10) 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function get_user_IP()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
$_user_ip = get_user_IP();
// var_dump($_user_ip);

// access only agrad users or VDS (80.87.198.217)
if (!filter_var($_user_ip, FILTER_VALIDATE_IP) || !in_array($_user_ip, $_ag_ipwhitelist)) //80.91.16.218
{
  echo 'Access denied :(';
  exit();
}

function save_info_to_logfile($textinfo='', $logmode='sms') 
{
  if (!isset($textinfo) || empty($textinfo)) return false;

  $_logfilename = '';
  if ($logmode == 'sms')
  {
    $_logfilename = 'send_sms';
  }

  $logline = '['.date('Y-m-d H:i:s') . '] '.$textinfo;
  file_put_contents(__DIR__ . '/aglogs/'.$_logfilename.'_'.date('Y-m-d').'.log', $logline . PHP_EOL, FILE_APPEND);

  return true;

}


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

    // $_myCurl = curl_init();
    // curl_setopt_array($_myCurl, array(
    //     CURLOPT_URL => 'https://api.mcommunicator.ru/m2m/m2m_api.asmx/SendMessage',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_POST => true,
    //     CURLOPT_POSTFIELDS => http_build_query(array('msid'=>'-', 'message'=>'test7', 'naming'=>'AUTOGRAD', 'login'=>'-', 'password'=>md5("-")))
    // ));
    // $_fileData_sms = curl_exec($_myCurl);
    // curl_close($_myCurl);
    
    // var_dump((int)simplexml_load_string($_fileData_sms)[0]);


$db = new simpleMysqli($_aor_db_settings);


// select all active communications
// send_status
// 0 - not sended (default)
// 1 - sended, but isset other phones
// 2 - sended to all phones (phones_count == last_phone_count)
// выбираем только коммуникации более 2 суток с последней отправки смс (~48 часов)
$_selected_communications = $db->select("
  SELECT
    communications.*, communications_type.*, communications_type_images.comt_img_url
  FROM communications
    INNER JOIN communications_type
      ON communications_type.comtype_id = communications.comtype_id
    LEFT JOIN communications_type_images
      ON communications_type_images.comt_img_id = communications_type.viber_button_img_id
  WHERE communications.send_status<2 AND communications.recorded=0 AND communications.canceled=0 AND communications.send_confirmed='1' AND (communications.send_date<1 OR (communications.send_date+(60*60*48))<? )
", time());
//var_dump($_selected_communications);

$_add_client_from_communications_ids = array();
$_add_client_from_communications_ids_fullarray = array();
$_communications_onidarray = array();
$_comtypes_ids = array('-1');
foreach ($_selected_communications as $_comm_t_k => $_comm_t_v) 
{
  if (isset($_comm_t_v['c_client_id']) && (int)$_comm_t_v['c_client_id'] > 0)
  {
    $_add_client_from_communications_ids[] = (int)$_comm_t_v['c_client_id'];
    $_add_client_from_communications_ids_fullarray[] = array('c_client_id'=>(int)$_comm_t_v['c_client_id'], 'comm_id'=>(int)$_comm_t_v['id']);
  }
  if (isset($_comm_t_v['comtype_id']) && (int)$_comm_t_v['comtype_id']>0 && !in_array((int)$_comm_t_v['comtype_id'], $_comtypes_ids))
  {
    $_comtypes_ids[] = (int)$_comm_t_v['comtype_id'];
  }
  $_communications_onidarray[(int)$_comm_t_v['id']] = $_comm_t_v;
}
if (count($_add_client_from_communications_ids) == 0) { $_add_client_from_communications_ids[] = '-1'; }


// select communications_type_images
$_selected_communications_type_images = $db->select("SELECT * FROM `communications_type_images` WHERE `comtype_id` IN (%s)", $_comtypes_ids);
$_comtype_images_byids = array();
foreach ($_selected_communications_type_images as $_cti_k => $_cti_v) 
{
  if (isset($_cti_v['comtype_id'], $_cti_v['comt_img_url'], $_cti_v['comt_img_type'], $_cti_v['comt_img_id']) && !empty($_cti_v['comt_img_url']) && !isset($_comtype_images_byids[$_cti_v['comt_img_type']][$_cti_v['comtype_id']][$_cti_v['comt_img_id']]))
  {
    $_comtype_images_byids[$_cti_v['comt_img_type']][$_cti_v['comtype_id']][] = $_cti_v;
  }
}
// var_dump($_selected_communications_type_images);
// var_dump($_comtype_images_byids);


$_communication_clients_array = array(); 

// select: communications.id - commercial_offers.client 
// (связку или список клиентов для выбора всех телефонов)
$_selected_comm_clients = $db->select("
SELECT
  DISTINCT communications.id AS communication_id, communications.task_creation_date AS communication_create_date, commercial_offers.client AS client_id
FROM communications_offers
  INNER JOIN communications
    ON communications_offers.communication = communications.id
  INNER JOIN commercial_offers
    ON communications_offers.offer = commercial_offers.id
WHERE communications.send_status < 2 AND communications.recorded = 0
");
// select all phones
// TODO: выбираем телефоны только если phones_clients.last_send > communications.task_creation_date AND phones_clients.last_send > 0, то есть на которые ещё ничего не отправлялось после создания данной коммуникации
$_selected_comm_phones = $db->select("
SELECT T1.client, T1.phone, phones_clients.last_send  FROM (
    SELECT
         client,
         phone,
         MAX(timestamp) AS timestamp
    FROM phones_clients

    GROUP BY client, phone) AS T1
  
  INNER JOIN  phones_clients ON phones_clients.client = T1.client AND phones_clients.timestamp = T1.timestamp AND phones_clients.phone = T1.phone AND phones_clients.not_actual = 0

  WHERE (phones_clients.client IN (
    SELECT
      DISTINCT commercial_offers.client
    FROM communications_offers
      INNER JOIN communications
        ON communications_offers.communication = communications.id
      INNER JOIN commercial_offers
        ON communications_offers.offer = commercial_offers.id
    WHERE communications.send_status<2 AND communications.recorded=0
  ) OR phones_clients.client IN (%s) ) AND phones_clients.not_actual=0 AND phones_clients.verified=1
  ORDER BY phones_clients.timestamp DESC
", $_add_client_from_communications_ids);
// SELECT * FROM phones_clients WHERE phone LIKE '%123123%' AND not_actual='0'

// наполняем клиентов в коммуникации (те, по которым нашлись клиенты + телефоны)
$_clients_idarray = array();
foreach ($_selected_comm_clients as $comm_clients_key => $comm_clients_val) 
{
  $_phones_array = array();
  // дополняем телефонами
  foreach ($_selected_comm_phones as $comm_phones_key => $comm_phones_val) 
  {
    // оставляем только цифры
    $_filtred_phone = preg_replace("/[^0-9]/", '', $comm_phones_val['phone']);
    // берём телефоны только текущего клиента 
    // - отсеиваем дубли
    // - отсеиваем телефоны != 10 символам (оставляем только цифровые)
    if ($comm_phones_val['client'] === $comm_clients_val['client_id'] && !in_array($_filtred_phone, $_phones_array) && strlen($_filtred_phone) == 10 && $_filtred_phone == $comm_phones_val['phone'])
    { 
      // выбираем телефоны только если phones_clients.last_send < communications.task_creation_date AND phones_clients.last_send > 0, то есть на которые ещё ничего не отправлялось после создания данной коммуникации
      // на этом шаге мы отсеиваем телефоны, на которые уже были отправлены SMS
      if (($comm_phones_val['last_send'] > 0 && $comm_phones_val['last_send'] < $comm_clients_val['communication_create_date']) || $comm_phones_val['last_send'] == 0) 
      {
        $_phones_array[] = $_filtred_phone;
      }
    }
  }
  // основной блок
  $_communication_clients_array[$comm_clients_val['communication_id']][] = array(
    'client_id' => $comm_clients_val['client_id'],
    'client_phones' => $_phones_array,
  );

  //ну и плюсом для выборки
  if (isset($comm_clients_val['client_id']) && (int)$comm_clients_val['client_id']>0 && !in_array((int)$comm_clients_val['client_id'], $_clients_idarray))
  {
    $_clients_idarray[] = (int)$comm_clients_val['client_id'];
  }

}

// + наполняем по клиентам из коммуникаций (c_client_id)
foreach ($_add_client_from_communications_ids_fullarray as $_commaddclientids_k => $_commaddclientids_v) 
{
  $_phones_array = array();
  $_thiscommunication_array = $_communications_onidarray[(int)$_commaddclientids_v['comm_id']]; // это массив текущей коммуникации, тут он нужен
  // дополняем телефонами
  foreach ($_selected_comm_phones as $comm_phones_key => $comm_phones_val) 
  {
    // оставляем только цифры
    $_filtred_phone = preg_replace("/[^0-9]/", '', $comm_phones_val['phone']);
    // берём телефоны только текущего клиента 
    // - отсеиваем дубли
    // - отсеиваем телефоны != 10 символам (оставляем только цифровые)
    if ($comm_phones_val['client'] === (int)$_commaddclientids_v['c_client_id'] && !in_array($_filtred_phone, $_phones_array) && strlen($_filtred_phone) == 10 && $_filtred_phone == $comm_phones_val['phone'])
    { 
      // выбираем телефоны только если phones_clients.last_send < communications.task_creation_date AND phones_clients.last_send > 0, то есть на которые ещё ничего не отправлялось после создания данной коммуникации
      // на этом шаге мы отсеиваем телефоны, на которые уже были отправлены SMS
      if (($comm_phones_val['last_send'] > 0 && $comm_phones_val['last_send'] < $_thiscommunication_array['task_creation_date']) || $comm_phones_val['last_send'] == 0) 
      {
        $_phones_array[] = $_filtred_phone;
      }
    }
  }
  // основной блок
  // ищем, нет ли уже клиента
  $_client_already_exist_inthiscomm = false;
  foreach ($_communication_clients_array[$_thiscommunication_array['id']] as $_ccs_k => $_ccs_v) 
  {
    if ((int)$_ccs_v['client_id'] == (int)$_commaddclientids_v['c_client_id'])
    {
      $_client_already_exist_inthiscomm = true;
    }
  }
  if ($_client_already_exist_inthiscomm == false)
  {
    $_communication_clients_array[$_thiscommunication_array['id']][] = array(
      'client_id' => (int)$_commaddclientids_v['c_client_id'],
      'client_phones' => $_phones_array,
    );
  }

  //ну и плюсом для выборки
  if (isset($_commaddclientids_v['c_client_id']) && (int)$_commaddclientids_v['c_client_id']>0 && !in_array((int)$_commaddclientids_v['c_client_id'], $_clients_idarray))
  {
    $_clients_idarray[] = (int)$_commaddclientids_v['c_client_id'];
  }

}


// var_dump($_clients_idarray);
// var_dump($_communication_clients_array);


// забираем все устройства, то есть по клиентам
$_s_pushdevices = $db->select("SELECT * FROM communication_channels WHERE channel_type='2' AND actual='1' AND client_id IN (%s)", $_clients_idarray);
$_s_pushdevices_array = array();
if (isset($_s_pushdevices[0]['id']))
{
  foreach ($_s_pushdevices as $_pd_k => $_pd_v) 
  {
    if (isset($_pd_v['client_id'], $_pd_v['value']) && (int)$_pd_v['client_id']>0 && !empty($_pd_v['value']))
    {
      $_s_pushdevices_array[(int)$_pd_v['client_id']][] = $_pd_v['value'];
    }
  } 
}
// var_dump($_s_pushdevices_array);


// убираем ЛИШНИХ клиентов, если клиент явно задан в c_client_id
// - т.е. если в коммуникации есть c_client_id, тогда выкидываем из $_communication_clients_array всех клиентов ['client_id'] !== c_client_id
foreach ($_selected_communications as $_comm_p_k => $_comm_p_v) 
{
  if (isset($_communication_clients_array[$_comm_p_v['id']][0], $_comm_p_v['c_client_id']) && (int)$_comm_p_v['c_client_id'] > 0)
  {
    foreach ($_communication_clients_array[$_comm_p_v['id']] as $_comcl_p_k => $_comcl_p_v) 
    {
      if ((int)$_comcl_p_v['client_id'] != (int)$_comm_p_v['c_client_id'])
      {
        unset($_communication_clients_array[$_comm_p_v['id']][$_comcl_p_k]);
      }
    }

    // reindex keys
    $_communication_clients_array[$_comm_p_v['id']] = array_values($_communication_clients_array[$_comm_p_v['id']]);
  }
}


$_comm_notexitsclientarray = array();
$_comm_zerophonearray = array();


$_max_communications_curr = 0; // текущий элемент цикла
$_max_communications_limit = 100; // максимальное количество для обработки коммуникаций за 1 скрипт

foreach ($_selected_communications as $_comm_k => $_comm_v) 
{
  if ((int)$_comm_v['id'] < 1) { continue; }

  // check clients
  // var_dump($_communication_clients_array[$_comm_v['id']]);
  if (!isset($_communication_clients_array[$_comm_v['id']][0])) 
  { 
    echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' is passed (NOT EXIST CLIENT)</span><br />';
    $_comm_notexitsclientarray[] = (int)$_comm_v['id'];
    continue; 
  }
  // check phones
  if (!isset($_communication_clients_array[$_comm_v['id']][0]['client_phones'][0])) 
  { 
    echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' is passed (0 PHONES)</span><br />';
    $_comm_zerophonearray[] = (int)$_comm_v['id'];
    continue; 
  }


  if ($_max_communications_curr >= $_max_communications_limit)
  {
    // просто игнорим лишняк
    continue;
  }


  $_clients_count = 0;
  $_phones_count = 0;
  // актуализируем информацию
  foreach ($_communication_clients_array[$_comm_v['id']] AS $c_client_key=>$c_client) 
  {
    foreach ($_communication_clients_array[$_comm_v['id']][$c_client_key]['client_phones'] AS $c_phone)
    {
      $_phones_count++;
    }
    $_clients_count++;
  }

  // var_dump($_clients_count);
  // var_dump($_phones_count);

  // обновляем общее количество телефонов (если оно == 0 и телефоны вообще есть)
  // - телефоны точно должны быть - проверка выше
  if ((int)$_comm_v['phones_count'] == 0 || (int)$_comm_v['clients_count'] == 0)
  {
    $db->update("UPDATE communications SET clients_count=?, phones_count=? WHERE id=? LIMIT 1", $_clients_count, $_phones_count, $_comm_v['id']);
  }

  // актуальные клиент и телефон (default)
  $_thiscom_client = $_communication_clients_array[$_comm_v['id']][0]['client_id'];
  $_thiscom_phone = $_communication_clients_array[$_comm_v['id']][0]['client_phones'][0];

  // полный телефон, на который отправляем SMS
  $_user_phone = '7'.$_thiscom_phone;
  // $_user_phone = '79991112233'; //temprory test
  
  // для рекомендации берем установленный в коммуникации телефон
  $_its_recomend_singlephone = false;
  if ($_comm_v['comtype_id'] == '2' && !empty($_comm_v['phone']))
  {
    $_thiscom_phone = $_comm_v['phone'];
    $_user_phone = '7'.$_comm_v['phone'];
    $_its_recomend_singlephone = true;
  }

  // var_dump($_thiscom_client);
  // var_dump($_thiscom_phone);



  // generate and update url (if need)

  if (isset($_comm_v['external_url']) && !is_null($_comm_v['external_url']) && strlen($_comm_v['external_url'])>0)
  {
    // not updating url in isset urls
    $_update_communication_url = 'not_need';
    $_this_url = $_comm_v['external_url'];
  }
  else
  {
    $_this_url = generate_random_string(6).'-'.generate_random_string(6).'-'.generate_random_string(6);
    $_update_communication_url = $db->update("UPDATE `communications` SET `communications`.`external_url`=?  WHERE `communications`.`id`=? LIMIT 1",  $_this_url, (int)$_comm_v['id']);
  }


  $_sms_text = 'Предлагаем Вам записаться на ТО'; // default
  $_sms_title = 'Запишитесь на ТО'; // default
  $_sms_link = 'https://lk.agrad.ru/';
  if (isset($_comm_v['comtype_smstext']) && !empty(trim($_comm_v['comtype_smstext'])))
  {
    $_sms_text = $_comm_v['comtype_smstext'];
  }
  if (isset($_comm_v['op_title']) && !empty(trim($_comm_v['op_title'])))
  {
    $_sms_title = $_comm_v['op_title'];
  }

  // check if is predict communication
  $_is_predict_TO_communication = false;
  if ( in_array($_comm_v['comtype_id'], array('1', '8', '9', '10')) )
  {
    $_is_predict_TO_communication = true;
  }

  // ссылки
  // - https://booking.agrad.ru/r-XXXXXXXXXXX (шиномонтаж или предложение записи)
  // - https://lk.agrad.ru/r/XXXXXXXXXXX (рекомендация, ЛК)
  if ($_comm_v['comtype_id'] == '2' || $_comm_v['comtype_id'] == '4' || $_comm_v['comtype_id'] == '5' || $_comm_v['comtype_id'] == '6' || $_comm_v['comtype_id'] == '7' || $_comm_v['comtype_id'] == '11' || $_comm_v['comtype_id'] == '12' || $_comm_v['comtype_id'] == '13' || $_is_predict_TO_communication === true)
  {
    $_sms_redirectto = isset($_comm_v['com_redirect'])?$_comm_v['com_redirect']:'';
    if (!empty($_sms_redirectto))
    {
      $_sms_redirectto = '?rd='.$_sms_redirectto;
    }

    // рекомендация, ЛК (+ кондиционер + уведомление неоплаченный заказ)
    $_sms_link = 'https://lk.agrad.ru/r/'.$_this_url.$_sms_redirectto;

    if (isset($_comm_v['custom_title'], $_comm_v['custom_text']) && !empty($_comm_v['custom_title']) && !empty($_comm_v['custom_text'])) //$_comm_v['comtype_id'] == '7' && 
    {
      // логика с кастомным текстом и заголовком
      $_sms_title = $_comm_v['custom_title'];
      $_sms_message = $_comm_v['custom_text'].' - https://lk.agrad.ru/r/'.$_this_url.$_sms_redirectto;
    }
    else
    {
      // стандарт
      $_sms_message = $_sms_text.' - https://lk.agrad.ru/r/'.$_this_url.$_sms_redirectto;
    }
  }
  else
  {
    // остальное через букинг
    $_sms_link = 'https://booking.agrad.ru/r-'.$_this_url;
    $_sms_message = $_sms_text.' - https://booking.agrad.ru/r-'.$_this_url;
  }



  // отсылаем сообщение, если новая ссылка сгенерирована и есть подтверждение
  // $_update_communication_url - индикатор наличия URL у предложения
  if ( ($_update_communication_url === true || $_update_communication_url == 'not_need') && $_comm_v['send_confirmed'] == 1 )
  {
    
    if (isset($_comm_v['viber_trysend']) && (int)$_comm_v['viber_trysend'] < 2) //&& $_comm_v['comtype_id'] == '6'
    {
      if ((int)$_comm_v['viber_trysend'] == 1)
      {
        // wait 5 min and change status

        $_viber_successsendcount = 0;
        $_viber_errsendcount = 0;

        // select viber sendmessage
        $_s_vibersendmessages = $db->select("SELECT * FROM `viber_sendmessages` WHERE `communication_id`=?", (int)$_comm_v['id']);

        // update statuses
        $_currtime = time();
        if (isset($_s_vibersendmessages[0]['communication_id'], $_s_vibersendmessages[0]['sendmessage_date']) && (int)$_s_vibersendmessages[0]['sendmessage_date'] > 0 && ((int)$_s_vibersendmessages[0]['sendmessage_date'] + (60*5)) < $_currtime )
        {
          // request & update statuses
          foreach ($_s_vibersendmessages as $_smsgs_k => $_smsgs_v) 
          {
            // request statuses
            $xml = new SimpleXMLElement('<xml/>');
            $provideInstantMessageDlvStatusRequest = $xml->addChild('provideInstantMessageDlvStatusRequest');

              $header = $provideInstantMessageDlvStatusRequest->addChild('header');
                $auth = $header->addChild('auth');
                  $login = $auth->addChild('login', 'agrad0'); //agrad0
                  $password = $auth->addChild('password', 'h4JNdcYa'); //h4JNdcYa

              $payload = $provideInstantMessageDlvStatusRequest->addChild('payload');
                $instantMessageList = $payload->addChild('instantMessageList');
                  $instantMessage = $instantMessageList->addChild('instantMessage');
                  $instantMessage->addAttribute('providerId', $_smsgs_v['providerId']);

            $_fxml = str_replace(array('<xml>', '</xml>'), array('',''), $xml->asXML());

            $url = 'http://gate40.mfms.ru:12900/avtograd/connector0/service';
            $data = $_fxml;

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => $data
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) 
            { 
              // cast error
              $_viber_errsendcount++;
            }
            else
            {
              $_resObj = simplexml_load_string($result);
              $_th_instantMessageList = (array)$_resObj->payload->instantMessageList;
              foreach ($_th_instantMessageList as $_thiml_k => $_thiml_v) 
              {
                $_thiml_v_array = (array)$_thiml_v;
                $_thDlvStatusiml_v_array = (array)$_thiml_v_array['instantMessageDlvStatus'];

                // var_dump( (string)$_thiml_v->attributes()->providerId );
                // var_dump( (string)$_thDlvStatusiml_v_array['dlvStatus'] );
                // var_dump( (string)$_thDlvStatusiml_v_array['dlvStatusAt'] );
                // var_dump( (string)$_thDlvStatusiml_v_array['dlvError'] );

                $_upd_vibersendmessage = $db->update("UPDATE `viber_sendmessages` SET `dlvStatus`=?, `dlvStatusAt`=?, `dlvError`=? WHERE `communication_id`=? AND `phone`=? LIMIT 1", (string)$_thDlvStatusiml_v_array['dlvStatus'], (string)$_thDlvStatusiml_v_array['dlvStatusAt'], (string)$_thDlvStatusiml_v_array['dlvError'], $_smsgs_v['communication_id'], $_smsgs_v['phone']);

                if ((string)$_thDlvStatusiml_v_array['dlvStatus'] == 'delivered' || (string)$_thDlvStatusiml_v_array['dlvStatus'] == 'sent' || (string)$_thDlvStatusiml_v_array['dlvStatus'] == 'read')
                {
                  $_viber_successsendcount++;
                }

              }
              
            }

          }


          // viber error exeption
          if ($_viber_errsendcount > 0 && $_viber_successsendcount == 0)
          {
            // go next communications
            continue;
          }
          // viber success (one of list)
          elseif ($_viber_successsendcount > 0)
          {
            // pass communication (set trysend == 2)

            // success close communication (status=2) and log
            $_update_communication_status = $db->update("UPDATE `communications` SET `communications`.`send_status`=?, `communications`.`send_date`=?, `communications`.`phone`=?, `communications`.`viber_trysend`=?  WHERE `communications`.`id`=? LIMIT 1", '2', time(), '-2', '3', (int)$_comm_v['id']);
            if ($_update_communication_status != false)
            {
              // all ok, go next
              echo '<span style="color:darkgreen;">Communication #'.(int)$_comm_v['id'].' (Viber send: '.count($_s_vibersendmessages).' phones, '.$_viber_successsendcount.' successed) is OK!</span>';
            }
            else
              echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' (Viber) is not updated (fail update send_status query)</span>';

            echo '<br />'; // visual /n
            
            // log
            save_info_to_logfile('Отправлены Viber сообщения на '.count($_s_vibersendmessages).' телефонов ('.$_viber_successsendcount.' успешных отправок, коммуникация '.(int)$_comm_v['id'].')');

            continue;
          }
          // viber pass (all is not found in viber)
          else
          {
            // update communication and gonext
            $_update_communication_vibertrysend = $db->update("UPDATE `communications` SET `communications`.`viber_trysend`='2'  WHERE `communications`.`id`=? LIMIT 1", (int)$_comm_v['id']);
          }


        }
        else
        {
          // ignore and wait
          continue;
        }


      }
      else
      {
        // отправляем по всем телефонам Viber message
        // - если хотя бы 1 из них прошёл, то ставим `communications`.`send_status`==2 и запечатываем эту коммуникацию
        // $_communication_clients_array[$_comm_v['id']][0]['client_phones']
        // comm clients
        $_thiscommunication_phonespull = array();
        foreach ($_communication_clients_array[$_comm_v['id']] as $c_cl_k => $c_cl_v) 
        {
          // comm phones
          foreach ($c_cl_v['client_phones'] as $c_cl_ph_k => $c_cl_ph_v) 
          {
            if (!empty($c_cl_ph_v) && !in_array($c_cl_ph_v, $_thiscommunication_phonespull))
            {
              $_thiscommunication_phonespull[] = $c_cl_ph_v;
            }
          }
        }
        $_viber_successsendcount = 0;
        $_viber_errsendcount = 0;
        if (count($_thiscommunication_phonespull)>0)
        {
          
          // get viber image
          $_this_viber_image_url = 'https://lk.agrad.ru/img/icon/icon-512x512.png';
          $_this_viber_button_image_id = null;
          // image is found
          if (isset($_comm_v['comt_img_url']) && !empty($_comm_v['comt_img_url']))
          {
            $_this_viber_image_url = $_comm_v['comt_img_url'];
            $_this_viber_button_image_id = (int)$_comm_v['viber_button_img_id'];
          }
          elseif (isset($_comtype_images_byids['viber']) && is_array($_comtype_images_byids['viber'][$_comm_v['comtype_id']]) && count($_comtype_images_byids['viber'][$_comm_v['comtype_id']]) > 0)
          {
            // get rand img
            $_rand_img_num = mt_rand( 0, ( count($_comtype_images_byids['viber'][$_comm_v['comtype_id']])-1 ) );
            $_this_viber_image_url = $_comtype_images_byids['viber'][$_comm_v['comtype_id']][$_rand_img_num]['comt_img_url'];
            $_this_viber_button_image_id = (int)$_comtype_images_byids['viber'][$_comm_v['comtype_id']][$_rand_img_num]['comt_img_id'];
            // var_dump($_rand_img_num);
            // var_dump($_this_viber_image_url);
            // var_dump($_this_viber_button_image_id);
          }

          // var_dump($_comtype_images_byids[$_comm_v['comt_img_type']][$_comm_v['comtype_id']]);
          // var_dump($_comm_v['comtype_id']);

          foreach ($_thiscommunication_phonespull as $_thc_phone_k => $_thc_phone_v) 
          {
            /* VIBER SEND MESSAGE */

            $xml = new SimpleXMLElement('<xml/>');

            $consumeInstantMessageRequest = $xml->addChild('consumeInstantMessageRequest');

              $header = $consumeInstantMessageRequest->addChild('header');

                $auth = $header->addChild('auth');

                  $login = $auth->addChild('login', 'agrad0'); //agrad0
                  $password = $auth->addChild('password', 'h4JNdcYa'); //h4JNdcYa


              $payload = $consumeInstantMessageRequest->addChild('payload');

                $instantMessageList = $payload->addChild('instantMessageList');

                  $instantMessage = $instantMessageList->addChild('instantMessage');

                  $instantMessage->addAttribute('clientId', (int)$_comm_v['id'].$_thc_phone_k.time());

                    $address = $instantMessage->addChild('address', '7'.$_thc_phone_v);
                    $subject = $instantMessage->addChild('subject', 'Avtograd');
                    $priority = $instantMessage->addChild('priority', 'normal');
                    $validityPeriodSeconds = $instantMessage->addChild('validityPeriodSeconds', '300');
                    $comment = $instantMessage->addChild('comment', 'Communication ID '.(int)$_comm_v['id'].' send');
                    $instantMessageType = $instantMessage->addChild('instantMessageType', 'viber');
                    $contentType = $instantMessage->addChild('contentType', isset($_comm_v['viber_contentType'])&&!empty($_comm_v['viber_contentType'])?$_comm_v['viber_contentType']:'text');

                    $content = $instantMessage->addChild('content');

                      $text = $content->addChild('text', $_sms_message.( $_is_predict_TO_communication === true || $_comm_v['comtype_id'] == '11' || $_comm_v['comtype_id'] == '12' || $_comm_v['comtype_id'] == '13' ?'':' или новый ViberBot Автоград: viber://pa?chatURI=avtogradbot') );
                      $caption = $content->addChild('caption', isset($_comm_v['viber_link_title'])&&!empty($_comm_v['viber_link_title'])?$_comm_v['viber_link_title']:'Личный Кабинет Автоград');
                      $action = $content->addChild('action', isset($_comm_v['viber_link_url'])&&!empty($_comm_v['viber_link_url'])?$_comm_v['viber_link_url']:$_sms_link);
                      $imageUrl = $content->addChild('imageUrl', $_this_viber_image_url);


            $_fxml = str_replace(array('<xml>', '</xml>'), array('',''), $xml->asXML());

            // http://gate40.mfms.ru:12900/avtograd/connector0/service
            // https://gate40.mfms.ru:12901/avtograd/connector0/service

            $url = 'https://gate40.mfms.ru:12901/avtograd/connector0/service';
            $data = $_fxml;

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => $data
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) 
            { 
              // cast error
              $_viber_errsendcount++;
              break;
            }
            else
            {
              $_resObj = simplexml_load_string($result);
              $_th_instantMessageList = (array)$_resObj->payload->instantMessageList;
              foreach ($_th_instantMessageList as $_thiml_k => $_thiml_v) 
              {
                
                $_thiml_v_array = (array)$_thiml_v;

                $_i_vibersend = $db->insert("INSERT INTO `viber_sendmessages` SET `clientId`=?, `communication_id`=?, `providerId`=?, `phone`=?, `code`=?, `sendmessage_date`=?", (string)$_thiml_v->attributes()->clientId, (int)$_comm_v['id'], (string)$_thiml_v->attributes()->providerId, $_thc_phone_v, (string)$_thiml_v_array['code'], time());

                // var_dump($_i_vibersend);

                if ((string)$_thiml_v_array['code'] == 'ok')
                {
                  $_viber_successsendcount++;
                }

                // var_dump( (array)$_thiml_v );

                /*
                array(2) {
                  ["@attributes"]=>
                  array(2) {
                    ["clientId"]=>
                    string(17) "23423612452632111"
                    ["providerId"]=>
                    string(4) "2056"
                  }
                  ["code"]=>
                  string(42) "error-instant-message-client-id-not-unique"
                }
                */
              }
              
            }

            // var_dump($result);

            /* VIBER SEND MESSAGE: END */
          }
        }

        // viber error exeption
        if ($_viber_errsendcount > 0 && $_viber_successsendcount == 0)
        {
          // go next communicarions
          continue;
        }
        // viber success (one of list)
        elseif ($_viber_successsendcount > 0)
        {
          // pass (set trysend == 1 and wait 5 min)
          $_viber_button_image_id = isset($_this_viber_button_image_id) && (int)$_this_viber_button_image_id>0 ? (int)$_this_viber_button_image_id : null;
          $_update_communication_vibertrysend = $db->update("UPDATE `communications` SET `communications`.`viber_trysend`='1', `communications`.`viber_button_image`=?  WHERE `communications`.`id`=? LIMIT 1", $_viber_button_image_id, (int)$_comm_v['id']);
          continue;
        }
        // viber pass (all is not found in viber)
        else
        {
          // update communication and gonext
          $_update_communication_vibertrysend = $db->update("UPDATE `communications` SET `communications`.`viber_trysend`='2'  WHERE `communications`.`id`=? LIMIT 1", (int)$_comm_v['id']);
        }

      }

    }



    // по пушам - если есть устройства, проходим по ним
    $_commclient = (int)$_communication_clients_array[$_comm_v['id']][0]['client_id'];
    if (isset($_s_pushdevices_array[$_commclient]) && is_array($_s_pushdevices_array[$_commclient]) && count($_s_pushdevices_array[$_commclient])>0)
    {

      // пуши
      $_successpushedcount = 0;
      $_push_fail_count = 0;
      foreach ($_s_pushdevices_array[$_commclient] as $_pd_k => $_pd_v) 
      {
          if (!empty($_pd_v))
          {
              // send push
              $url = 'https://fcm.googleapis.com/fcm/send';
              $YOUR_API_KEY = 'AAAAmleJZ84:APA91bHR_n9W4HG8tX8-M3ysZuN_zXw80mwPnQz1K1mFfXdIumBCaoGTGgAwGhvO9UiuIVx5Y8ifkQrKDrQzpty4l9ecNFWkTsVnL4on96ZzTpfyTaBEpRekthYyofVj1Th7eAXKc7Oy'; // Server key
              $YOUR_TOKEN_ID = $_pd_v; // Client token id

              $request_body = [
                  'to' => $YOUR_TOKEN_ID,
                  'notification' => [
                      'title' => $_sms_title,
                      'body' => $_sms_message,
                      'icon' => 'https://lk.agrad.ru/img/icon/android-icon-192x192.png',
                      'click_action' => $_sms_link,
                  ],
              ];
              $fields = json_encode($request_body);

              $request_headers = [
                  'Content-Type: application/json',
                  'Authorization: key=' . $YOUR_API_KEY,
              ];

              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
              curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
              $response = curl_exec($ch);
              curl_close($ch);

              // echo $response;
              $_result_resp = json_decode($response, true);
              if (isset($_result_resp['success']) && $_result_resp['success'] == '1')
              {
                $_successpushedcount++;
              }
              if (isset($_result_resp['failure']) && (int)$_result_resp['failure']>0)
              {
                $_push_fail_count++;

                // update actual
                $_update_ch_actual = $db->update("UPDATE `communication_channels` SET `actual`='0' WHERE client_id=? AND channel_type=? AND value=?", (int)$_commclient, 2, $_pd_v);
              }

          }
      }


      // если были успешные отправки - обновляемся и выходим
      if ($_successpushedcount > 0)
      {
        $_update_communication_status = $db->update("UPDATE `communications` SET `communications`.`send_status`=?, `communications`.`send_date`=?, `communications`.`phone`=?  WHERE `communications`.`id`=? LIMIT 1", '2', time(), '-1', (int)$_comm_v['id']);
        if ($_update_communication_status != false)
        {
          // all ok, go next
          echo '<span style="color:darkgreen;">Communication #'.(int)$_comm_v['id'].' (PUSH on '.count($_s_pushdevices_array[$_commclient]).' devices, '.$_successpushedcount.' successed) is OK!</span>';
        }
        else
          echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' (PUSH) is not updated (fail update send_status query)</span>';

        echo '<br />'; // visual /n
        
        // log
        save_info_to_logfile('Отправлены PUSH уведомления на '.count($_s_pushdevices_array[$_commclient]).' девайсов ('.$_successpushedcount.' успешных пушей, коммуникация '.(int)$_comm_v['id'].', клиент '.$_commclient.')');

        continue;
      }
    }


    // send sms

    //$_sms_message = 'Предлагаем Вам записаться на ТО - https://test2.agrad.ru:1111/OnlineRecord/aor-load-full.php?to='.$_this_url;
    // post to mts
    $_myCurl = curl_init();
    curl_setopt_array($_myCurl, array(
        CURLOPT_URL => 'https://api.mcommunicator.ru/m2m/m2m_api.asmx/SendMessage',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(array('msid'=>$_user_phone, 'message'=>$_sms_message, 'naming'=>'AUTOGRAD', 'login'=>'agrad', 'password'=>md5("k7J2Xfq3RtMCd8")))
    ));
    $_fileData_sms = curl_exec($_myCurl);
    curl_close($_myCurl);

    // load xml data from mtscommunicator
    // - need (int)simplexml_load_string($_fileData_sms)[0] > 0
    $_fileData_sms_default = $_fileData_sms;
    $_fileData_sms = simplexml_load_string($_fileData_sms);
    // var_dump($_fileData_sms);


    // status
    if ($_fileData_sms === false || !isset($_fileData_sms[0]) || (int)$_fileData_sms[0] < 1)
    {
      // error
      echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' is not sended (SMS send error)</span>';
      save_info_to_logfile('ОШИБКА отправки SMS на номер '.$_user_phone.' (коммуникация '.(int)$_comm_v['id'].', клиент '.$_thiscom_client.', ответ от MTS Communicator: '.(is_string($_fileData_sms)?$_fileData_sms:json_encode($_fileData_sms_default)).')');

      // update send count
      $_send_count = (int)$_comm_v['send_count']+1;
      $_update_communication_status = $db->update("UPDATE `communications` SET `communications`.`send_count`=?  WHERE `communications`.`id`=? LIMIT 1", $_send_count, (int)$_comm_v['id']);

      // when >= 10 send_count, canceling
      if ($_send_count > 9)
      {
        $_update_communication_status_c = $db->update("UPDATE `communications` SET `communications`.`canceled`='1'  WHERE `communications`.`id`=? LIMIT 1", (int)$_comm_v['id']);
        save_info_to_logfile('Больше 10 попыток, автоматическая ОТМЕНА '.$_user_phone.' (коммуникация '.(int)$_comm_v['id'].', клиент '.$_thiscom_client.')');
      }

    }
    else
    {
      
      // update send sms status, if sms sended
      // если это последний телефон, тогда обновляем статус communications.send_status на 2
      // для коммуникации типа 2 (рекомендация) проходим только по одному телефону (который в коммуникации)
      $_new_send_status = '1';
      if ($_phones_count == 1 || $_its_recomend_singlephone === true)
      {
        $_new_send_status = '2';
      }
      // update
      $_send_count = (int)$_comm_v['send_count']+1;
      $_update_communication_status = $db->update("UPDATE `communications` SET `communications`.`send_status`=?, `communications`.`send_date`=?, `communications`.`send_count`=?, `communications`.`phone`=?  WHERE `communications`.`id`=? LIMIT 1", $_new_send_status, time(), $_send_count, $_thiscom_phone, (int)$_comm_v['id']);
      if ($_update_communication_status != false)
      {
        // all ok, go next
        echo '<span style="color:darkgreen;">Communication #'.(int)$_comm_v['id'].' is OK!</span>';
      }
      else
        echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' is not updated (fail update send_status query)</span>';

      // обновляем по данному телефону время последней отправки и ставим ID, полученный от MTS Communicator'а
      $db->update("UPDATE phones_clients SET last_send=?, last_mtscommid=? WHERE client=? AND phone=?", time(), (int)$_fileData_sms[0], $_thiscom_client, $_thiscom_phone);

      // log
      save_info_to_logfile('Отправлено SMS на номер '.$_user_phone.' (коммуникация '.(int)$_comm_v['id'].', клиент '.$_thiscom_client.', ответ от MTS Communicator: '.(int)$_fileData_sms[0].')');

      // add sms statistic to db
      $db->insert("INSERT INTO communications_send SET comsend_date=?, communication_id=?, channel_type=?, channel_key=?", time(), (int)$_comm_v['id'], '1', $_thiscom_phone);

    }

  }
  elseif ($_comm_v['send_confirmed'] == 0 && $_update_communication_url !== false)
  {
    echo '<span style="color:gray;">- Communication #'.(int)$_comm_v['id'].' need confirm (ready for send SMS)</span>';
  }
  else
    echo '<span style="color:darkred;">- Communication #'.(int)$_comm_v['id'].' is not updated (fail update URL query)</span>';
  

  echo '<span style="color:gray;"> (клиентов - '.(int)$_clients_count.', телефонов - '.(int)$_phones_count.')</span>';

  echo '<br />'; // visual /n

  $_max_communications_curr++;

}


if (count($_comm_notexitsclientarray) > 0)
{
  echo '<br />Not exist clients: ';
  foreach ($_comm_notexitsclientarray as $_cnec_k => $_cnec_v) 
  {
    echo "'".$_cnec_v."',";
  }

  $_upd_commuticationsq = $db->update("UPDATE communications SET canceled='1', automatic_close='1' WHERE id IN (%s)", $_comm_notexitsclientarray);
  if ($_upd_commuticationsq !== false)
  {
    echo '<br />'.count($_comm_notexitsclientarray).' communications is automatic closed';
  }
  else
  {
    echo '<br />'.count($_comm_notexitsclientarray).' communications close FAIL';
  }
}
if (count($_comm_zerophonearray) > 0)
{
  echo '<br />Zero phones: ';
  foreach ($_comm_zerophonearray as $_czp_k => $_czp_v) 
  {
    echo "'".$_czp_v."',";
  }

  $_upd_commuticationsq = $db->update("UPDATE communications SET canceled='1', automatic_close='1' WHERE id IN (%s)", $_comm_zerophonearray);
  if ($_upd_commuticationsq !== false)
  {
    echo '<br />'.count($_comm_zerophonearray).' communications is automatic closed';
  }
  else
  {
    echo '<br />'.count($_comm_zerophonearray).' communications close FAIL';
  }

}


// empty
if (is_array($_selected_communications) && count($_selected_communications) == 0)
{
  echo 'All communication is successfully processed.';
}
elseif (is_array($_selected_communications))
{
  echo count($_selected_communications).' communication in process.';
}

exit();

?>