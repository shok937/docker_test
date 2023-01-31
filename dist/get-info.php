<?php

// Получение информации из кэша, Agrad

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

// функции для вызова через API
$_funct_array = array(
  "brand" => "get_car_brand",
  "brand_top" => "get_top_brands",
  "model" => "get_car_model",
  "model_top" => "get_top_models",
  "generation" => "get_car_generation",
  "series" => "get_car_series",
  "modification" => "get_car_modification",
  "companies" => "get_sto_deps",
  "services" => "get_packages",
  "datetime" => "get_free_timeslots",
);
// параметры для функций (нужны для перечисления кэша, должны совпадать с входящими $_GET)
$_params_array = array(
  "brand" => array(),
  "brand_top" => array("limit"),
  "model" => array("brand_ref"),
  "model_top" => array("brand_ref", "limit"),
  "generation" => array("model_ref"),
  "series" => array("generation_ref"),
  "modification" => array("series_ref"),
  "companies" => array("brand_ref"),
  "services" => array("brand_ref", "model_ref", "generation_ref", "ser_ref", "modification_ref", "mil"),
  "datetime" => array("dep", "date", "adviser"),
);

$_cache_files_dir = dirname(__FILE__).'/cache/';
//http://api.agrad.ru, 185.11.6.230
$_api_url = 'http://80.91.16.218:2060/'; //ssl 2070
$_api_url_t = 'http://80.91.16.218:2034/'; //ssl 2072

// custom clear cache (if need)
if (isset($_GET['clear_cache']) && $_GET['clear_cache'] == 'yes')
{
  array_map('unlink', glob($_cache_files_dir."*.agradcache"));
}

// custom clear datetime (if need)
if (isset($_GET['clear_cache'], $_GET['date'], $_GET['dep']) && $_GET['clear_cache'] == 'record_datetime' && strlen($_GET['date']) > 5 && strlen($_GET['dep']) > 5)
{
  // ...?....&clear_cache=record_datetime&date=ФОРМАТ_ДАТЫ&dep=ССЫЛКА_REF_ПОДРАЗДЕЛЕНИЕ
  // clear, if exist
  $_filename_dt = 'datetime';
  if (isset($_params_array['datetime']) && count($_params_array['datetime']) > 0)
  {
    foreach ($_params_array['datetime'] AS $_v)
    {
      if (isset($_GET[$_v]) && check_ref($_GET[$_v])) 
      {
        // add to cache string
        $_filename_dt .= $_v.$_GET[$_v];
      }
    }
  }
  $_filename_dt .= '.agradcache';
  $_filename_dt = $_cache_files_dir.$_filename_dt;
  if (file_exists($_filename_dt))
  {
    unlink($_filename_dt);
  }
}

function check_ref($_ref) 
{
	if (!preg_match('/[^A-Za-z0-9-_ ]/', $_ref))
	{
		if (strlen($_ref) > 0)
		{
			return true;
		}
	}
	return false;
}

// save
function save_cache($_arr, $_filename)
{
	global $_cache_files_dir;
	// check arr
	if (!isset($_arr) || empty($_arr)) { return false; }

	file_put_contents($_cache_files_dir.$_filename, serialize($_arr), LOCK_EX);
}

// load
function load_cache($_functname)
{
	global $_cache_files_dir, $_api_url, $_api_url_t, $_funct_array, $_params_array;
  
  $_filename = $_functname;
  if (isset($_params_array[$_functname]) && count($_params_array[$_functname]) > 0)
  {
    foreach ($_params_array[$_functname] AS $_v)
    {
      if (isset($_GET[$_v]) && check_ref($_GET[$_v])) 
      {
        // add to cache string
        $_filename .= $_v.$_GET[$_v];
      }
    }
  }
  $_filename .= '.agradcache';
  
  //var_dump(file_exists($_cache_files_dir.$_filename));
  //var_dump(filemtime($_cache_files_dir.$_filename));
  //var_dump((time()-(10*60)));
  
  if (file_exists($_cache_files_dir.$_filename) && filemtime($_cache_files_dir.$_filename) > (time()-(10*60)) ) // 10 minutes
  {
    // load cache from file
    $_fileData = file_get_contents($_cache_files_dir.$_filename);
    
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
  {

    // get api & set new cache
    $_this_arr = array();
    
    $_api_url_full = '';
    // parse url query string
    if (isset($_params_array[$_functname]) && count($_params_array[$_functname]) > 0)
    {
      foreach ($_params_array[$_functname] AS $_v)
      {
        if (isset($_GET[$_v]) && check_ref($_GET[$_v])) 
        {
          if (empty($_api_url_full)) { $_api_url_full .= '?'; } else { $_api_url_full .= '&'; }
          $_api_url_full .= $_v.'='.$_GET[$_v];
        }
      }
    }
    $_api_final_url = $_api_url;
    if ($_functname == 'datetime') {$_api_final_url = $_api_url_t;}
    $_api_url_full = $_api_final_url.$_funct_array[$_functname].$_api_url_full;
    $_fileData = file_get_contents($_api_url_full);
    
    if ($_fileData === false)
      return false;
    else
    {
      // unserialize
      $_this_arr = json_decode($_fileData);
      
      save_cache($_this_arr, $_filename);
      
      return $_this_arr;
    }

  }
  
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$_get_f = isset($_GET['f']) && !empty($_GET['f']) && isset($_funct_array[$_GET['f']]) ? $_GET['f'] : false;
if ($_get_f !== false)
{
  $_f_array = load_cache($_get_f);
  //var_dump($_f_array);
  echo json_encode($_f_array);
}
else
{
  echo json_encode(array("status"=>"error", "info"=>"empty"));
}


?>