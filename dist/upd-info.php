<?php
define('AGRAD', 'yep!');

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

// libs
require_once(dirname(__FILE__) . '/phplibs/class.simpleDB.php');
require_once(dirname(__FILE__) . '/phplibs/class.simpleMysqli.php');
// settings
require_once(dirname(__FILE__) . '/agcore/agsettings.php');

$db = new simpleMysqli($_aor_db_settings);

  
// update queries
$_upd_type = isset($_GET['upd_type']) && !empty($_GET['upd_type']) ? $_GET['upd_type'] : '';
$_upd_client = isset($_GET['upd_client']) && !empty($_GET['upd_client']) ? (int)$_GET['upd_client'] : '';
$_upd_car = isset($_GET['upd_car']) && !empty($_GET['upd_car']) ? (int)$_GET['upd_car'] : '';
$_upd_communication_url = isset($_GET['upd_commurl']) && !empty($_GET['upd_commurl']) ? $_GET['upd_commurl'] : '';
if ($_upd_type === 'not_mine_auto' && (int)$_upd_client > 0 && (int)$_upd_car > 0)
{
  $db->update("UPDATE `cars_clients` SET `not_actual`=? WHERE `client`=? AND `car`=?", '1', (int)$_upd_client, (int)$_upd_car);
}
elseif ($_upd_type === 'set_opened' && strlen($_upd_communication_url) > 0)
{
  $db->update("UPDATE `communications` SET `opened`=? WHERE `external_url`=? LIMIT 1", '1', $_upd_communication_url);
}
elseif ($_upd_type === 'set_recorded' && strlen($_upd_communication_url) > 0)
{
  $_this_time = time();
  $db->update("UPDATE `communications` SET `recorded`=?,`record_date`=? WHERE `external_url`=? LIMIT 1", '1', $_this_time, $_upd_communication_url);
}
echo json_encode(array("status"=>"not", "info"=>"not_info"));
exit();

?>