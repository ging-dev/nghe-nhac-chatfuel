<?php

//  >_ FB: Gingdev


  // error_reporting(E_ALL);
  
   require('vendor/autoload.php');
   
   
   
   
   
   
   $curl = new Curl\Curl();
   $chatfuel = new Chatfuel\Chatfuel(TRUE);  
   
 
   $baihat = isset($_GET['baihat']) ? $_GET['baihat'] : 'Lỗi';
   
   // Do nhaccuatui xử lý sẽ mất thời gian nên tôi chọn zingmp3
   
   $curl->get('http://ac.mp3.zing.vn/complete/desktop', array('type' => 'song', 'query' => $baihat));
   $res = json_decode($curl->response, true);
   
   $idbaihat = $res['data'][0]['song'][0]['id'];
   if ($idbaihat) {
   
   
       $curl->get('https://m.zingmp3.vn/bai-hat/' . $idbaihat . '.html');
       preg_match('#data-source="(.+?)"#', $curl->response, $key);
   
       // Cài đặt gzip
  
       $curl->setOpt(CURLOPT_ENCODING, 'gzip'); 
       $curl->get('https://m.mp3.zing.vn/xhr' . $key[1]); 
       $data = json_decode($curl->response, true);
       
   
   
   
       // Phản hồi chatfuel
   
       $text = 'Bài Hát ' . $data['data']['name'] . ' của ' . $data['data']['artists_names'] . ' phải không?';
       $chatfuel->sendText($text);
       $chatfuel->sendImage($data['data']['artist']['cover']); 
       $chatfuel->sendAudio(str_replace('//', 'https://', $data['data']['source']['128']));
   } else {
       $chatfuel->sendText('Không tìm được bài hát.');
   }