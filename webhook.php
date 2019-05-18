<?php

//  >_ FB: Gingdev

  // error_reporting(E_ALL);
  
   require('vendor/autoload.php');
   
   
   
   
   
   
   $curl = new Curl\Curl();
   $curl->setOpt(CURLOPT_FOLLOWLOCATION, TRUE);
   $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
   $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
   $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
      
   $baihat = isset($_GET['baihat']) ? $_GET['baihat'] : 0;
   
   $curl->get('https://www.nhaccuatui.com/tim-kiem/bai-hat', array('q' => urlencode($baihat), 'b' => 'title', 'sort' => 2));
   
   // Lấy url bài hát đầu tiên  
   
   preg_match('#<a href="(.+?)" class="button_new#', $curl->response, $url);
    
   // tách url để lấy id
   
   $res = explode('.',$url[1]);
   
   $idbaihat = $res[3];
   
   $curl->get('https://graph.nhaccuatui.com/v1/songs/' . $idbaihat . '?access_token=683501bbad17313976cb2cbe4305fb3d');
   
   $data = json_decode($curl->response);
   
   // Phản hồi chatfuel
   
   $chatfuel = new Chatfuel\Chatfuel(TRUE);        
   
   $text = 'Bài Hát ' . $data->data->{2} . ' của ' . $data->data->{3} . ' phải không?';
   
   $chatfuel->sendText($text);
   $chatfuel->sendImage($data->data->{8}); 
   $chatfuel->sendAudio($data->data->{11});    
   
?>