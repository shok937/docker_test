<?php
// Agrad, предложение записи на ТО
if (!defined('AGRAD')) {exit('err');}

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

// $_to_content = '';
// $_GET['to']

// libs
require_once(dirname(__FILE__) . '/phplibs/class.simpleDB.php');
require_once(dirname(__FILE__) . '/phplibs/class.simpleMysqli.php');
// settings
require_once(dirname(__FILE__) . '/agcore/agsettings.php');

$db = new simpleMysqli($_aor_db_settings);

$_to_data = array();
$_to_yep = false;
$_to_timeout = false;

if (isset($_POST['to']) && preg_match('/^[A-Za-z0-9-_]+$/', $_POST['to']))
{
  // select client data
  $_selected_to = $db->select("
    SELECT
      communications.id AS 'communication_id',
      communications.external_url,
      communications.send_status,
      communications.phone,
      clients.id AS 'client_id',
      clients.ref_ones AS 'client_ref',
      clients.middle_name,
      clients.first_name,
      clients.last_name,
      clients.gender,
      commercial_offers.comment,
      commercial_offers.date_actual,
      cars.id AS 'car_id',
      cars.model,
      cars.brand,
      cars.vin,
      cars.ref_ones AS 'car_ref',
      cars.name AS 'car_name',
      -- operations.name AS 'operation_name',
      communications_type.comtype_name AS 'operation_name',
      communications_type.op_title,
      communications_type.op_description,
      communications_type.icon,
      brands.name AS 'brand_name',
      models.name AS 'model_name',
      models.ref_ones AS 'model_ref',
      brands.ref_ones AS 'brand_ref'
    FROM communications_offers
      INNER JOIN communications
        ON communications_offers.communication = communications.id
      INNER JOIN communications_type
        ON communications_type.comtype_id = communications.comtype_id
      INNER JOIN commercial_offers
        ON communications_offers.offer = commercial_offers.id
      INNER JOIN clients
        ON commercial_offers.client = clients.id
      INNER JOIN operations
        ON commercial_offers.operation = operations.id
      INNER JOIN cars
        ON commercial_offers.car = cars.id
      INNER JOIN brands
        ON cars.brand = brands.id
      INNER JOIN models
        ON cars.model = models.id
    WHERE communications.external_url=? AND communications.canceled='0' AND commercial_offers.date_actual>? AND commercial_offers.date_actual>'0' LIMIT 1
  ", $_POST['to'], time());
  
  if (isset($_selected_to[0]['communication_id'])) // isset client, communication_id
  {
    // select versions (packets)
    $_selected_to_versions = $db->select("
    SELECT
      communications.external_url,
      communications_offers.*,
      commercial_offers.operation_version,
      operations.icon,
      commercial_offers.id AS 'comm_offer_id',
      commercial_offers.summ,
      commercial_offers.disc_summ,
      commercial_offers.comment,
      operations.name AS 'operation_name',
      operations.id AS 'operation_id'
    FROM communications_offers
      INNER JOIN communications
        ON communications_offers.communication = communications.id
      INNER JOIN commercial_offers
        ON communications_offers.offer = commercial_offers.id
      INNER JOIN operations
        ON commercial_offers.operation = operations.id
    WHERE communications_offers.communication=? AND communications.external_url=?
    ORDER BY commercial_offers.summ ASC
    ", (int)$_selected_to[0]['communication_id'], $_POST['to']);
    
    if (isset($_selected_to_versions[0]['comm_offer_id'])) // isset commercial_offer
    {
      // parce commercial_offers.id's and groups sorting
      $_comm_offers_array = array();
      $_comm_offers_id_array = array();
      $__to_groups_data = array(); // on groups data
      foreach ($_selected_to_versions AS $_version) 
      {
        if ((int)$_version['comm_offer_id'] > 0)
        {
          $_comm_offers_id_array[] = (int)$_version['comm_offer_id'];
          $_comm_offers_array[(int)$_version['comm_offer_id']]['offer_array'] = $_version;
        }
        // grouping on operations
        $__to_groups_data[$_version['operation_id']][] = $_version;
      }
      
      //var_dump($_comm_offers_array);
      //var_dump($_comm_offers_id_array);
      
      // select all parts
      $_selected_parts = $db->select("
        SELECT
          commercial_offers_parts.id,
          commercial_offers_parts.offer,
          commercial_offers_parts.ref_ones,
          commercial_offers_parts.name,
          commercial_offers_parts.summ,
          commercial_offers_parts.price,
          commercial_offers_parts.amount,
          commercial_offers_parts.summ_disc
        FROM commercial_offers_parts
        WHERE commercial_offers_parts.offer IN (%s)
      ", $_comm_offers_id_array);
      
      // parse parts and attach to offer
      foreach ($_selected_parts AS $_part) 
      {
        if ((int)$_part['offer'] > 0 && isset($_comm_offers_array[(int)$_part['offer']]))
        {
          $_comm_offers_array[(int)$_part['offer']]['parts_array'][] = $_part;
        }
      }
      
      // select all works
      $_select_works = $db->select("
        SELECT
          commercial_offers_works.amount,
          commercial_offers_works.work_hour,
          commercial_offers_works.summ_disc,
          commercial_offers_works.summ,
          commercial_offers_works.price,
          commercial_offers_works.name,
          commercial_offers_works.id,
          commercial_offers_works.ref_ones,
          commercial_offers_works.offer
        FROM commercial_offers_works
        WHERE commercial_offers_works.offer IN (%s)
      ", $_comm_offers_id_array);
      
      // parse works and attach to offer
      foreach ($_select_works AS $_work) 
      {
        if ((int)$_work['offer'] > 0 && isset($_comm_offers_array[(int)$_work['offer']]))
        {
          $_comm_offers_array[(int)$_work['offer']]['works_array'][] = $_work;
        }
      }
      
      //echo '<pre>';
      //var_dump($_comm_offers_array);
      //echo '</pre>';
      
      
      
    }
  }

}
else
{
  $_selected_to = '';
}


//header('Access-Control-Allow-Origin: *');
//header('Content-Type: application/json');

if ($_selected_to === false)
{
  //echo json_encode(array("status"=>"error", "info"=>"db_error"));
}
elseif (is_array($_selected_to) && count($_selected_to) < 1)
{
  //echo json_encode(array("status"=>"error", "info"=>"empty"));
  $_to_timeout = true;
}
elseif (is_array($_selected_to) && isset($_selected_to[0]))
{
  // $_selected_to[]
  $_to_yep = true;
  $_to_data = $_selected_to[0];
  //echo json_encode($_to_data);
}
else
{
  //echo json_encode(array("status"=>"error", "info"=>"unknow null"));
}

//var_dump($_selected_to);
//var_dump($_to_timeout);

?>