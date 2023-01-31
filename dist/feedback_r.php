<?php
// AGRAD feedback send result

$alkfbText = isset($_POST['fbte']) && !empty($_POST['fbte']) ? $_POST['fbte'] : '';
$alkfbPhone = isset($_POST['fbph']) && !empty($_POST['fbph']) ? $_POST['fbph'] : '';
$alkfbEmail = isset($_POST['fbem']) && !empty($_POST['fbem']) ? $_POST['fbem'] : '';
$alkfbFromSource = isset($_POST['fromsource']) && !empty($_POST['fromsource']) ? $_POST['fromsource'] : '';

$_fbtext_f = '
Источник: '.htmlentities($alkfbFromSource, ENT_QUOTES).' ;
Описание ошибки: 
'.htmlentities($alkfbText, ENT_QUOTES).'
------
Телефон: '.htmlentities($alkfbPhone, ENT_QUOTES).' ;
';
if (isset($alkfbEmail) && !empty($alkfbEmail))
{
	$_fbtext_f .= 'E-mail: '.htmlentities($alkfbEmail, ENT_QUOTES).' ;';
}

// echo $_fbtext_f;

$_api_url = 'http://80.91.16.218:2060/'; //ssl 2070
// $_api_url = 'http://192.168.5.162:2060/'; // test
      
$_predata = array(
	'channel' => 'lk_feedback',
    'text' => $_fbtext_f,
);

$data = array('data' => json_encode($_predata));
// var_dump($data);
      
// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' =>  http_build_query($data)
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($_api_url.'slack/post_message', false, $context);
if ($result === FALSE) 
{ 
    echo json_encode(array("status"=>"error", "info"=>"bad_backend_response")); // питон отдал ошибку или не загрузился
}
else
{
    echo $result;
}


?>