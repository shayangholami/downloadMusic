<?php
  $downloadDir = "/home/shayan/Music";
  $resDownloadMusic = @file_get_contents(dirname(__FILE__)."/downloadMusic.txt");
  $urlMaster = array();

  /*    next1 download  */
    for($i=1;$i<=3;$i++)
    {
      $urlArr = next1($resDownloadMusic,$i);
      $urlMaster = array_merge($urlMaster,$urlArr);
    }
  /*    next1 download  */

  $urlMaster = implode("\n",$urlMaster);
  if($urlMaster)
    file_put_contents(dirname(__FILE__)."/downloadMusic.txt", $urlMaster.PHP_EOL , FILE_APPEND | LOCK_EX);
  // startDownload($downloadDir);


  function startDownload($downloadDir)
  {
    if(!file_exists($downloadDir))
      mkdir($downloadDir);
    $downloadMusic = file_get_contents(dirname(__FILE__)."/downloadMusic.txt");
    $downloadMusic = explode("\n",$downloadMusic);
    $newArrText = array();
    foreach ($downloadMusic as $key => $value) {
      if(trim($value))
      {
        $fileArr = explode("##__##",$value);
        if($fileArr[2] == 0)
        {
          $fileName = explode("##__##",$value)[0];
          $fileName = explode("/",$fileName);
          $fileName = end($fileName);
          file_put_contents($downloadDir . $fileName, fopen($value, 'r'));
          $newArr = array($fileArr[0],$fileArr[1],1);
        }
        else {
          $newArr = array($fileArr[0],$fileArr[1],1);
        }
        $newArrText[] = implode("##__##",$newArr);
      }
    }
    $newArrText = implode("\n",$newArrText);
    file_put_contents('downloadMusic.txt', $newArrText);
  }
  function getUrl($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $resContent = curl_exec($ch);
    return $resContent;
  }
  function next1($resDownloadMusic,$page = 1)
  {
    $resContent = getUrl("https://nex1music.ir/pages/$page/");
    $resContent = explode("mre",$resContent);
    $urlArr = array();
    foreach($resContent as $valContent)
    {
      $resContentUrl = explode("href=",$valContent);
      $resContentUrl = end($resContentUrl);
      $resContentUrl = explode(" ",$resContentUrl)[0];
      $data = getUrl($resContentUrl);
      $dataMusic = explode("pctn",$data)[1];
      preg_match_all('/<a.*href="(.*mp3)">.*320/', $dataMusic, $dataMusicMatches);
      if(@$dataMusicMatches[1][0])
      {
        if(!strstr($resDownloadMusic,$dataMusicMatches[1][0]))
            $urlArr[] = $dataMusicMatches[1][0]."##__##".date('Y-m-d H:i:s')."##__##0";
      }
    }
    return $urlArr;
  }
