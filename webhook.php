<?php

//  >_ FB: Gingdev

  // error_reporting(E_ALL);
  
   require('vendor/autoload.php');
   
   
   
   
   
   
   $curl = new Curl\Curl();
      
   $baihat = isset($_GET['baihat']) ? $_GET['baihat'] : 0;
   
   $curl->get('https://www.nhaccuatui.com/tim-kiem/bai-hat', array('q' => $baihat, 'b' => 'title', 'sort' => 2));
   
   // Lấy url bài hát đầu tiên  
   
   preg_match('#key="(.+?)"#', $curl->response, $idbaihat);
   
   $curl->reset();
    
   
   $curl->get('https://graph.nhaccuatui.com/v1/songs/' . $idbaihat[1] . '?access_token=683501bbad17313976cb2cbe4305fb3d');
   
   $data = json_decode($curl->response, true);
   
   // Phản hồi chatfuel
   
   $chatfuel = new Chatfuel\Chatfuel(TRUE);        
   
   $text = 'Bài Hát ' . $data['data']['2'] . ' của ' . $data['data']['3'] . ' phải không?';
   
   $chatfuel->sendText($text);
   $chatfuel->sendImage($data['data']['8']); 
   $chatfuel->sendAudio($data['data']['11']);    
   
?>