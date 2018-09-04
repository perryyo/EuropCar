<?php
//include the europcar.php file for collection XML response.
include('europcar.php');

  //select cities using country code
  if(isset($_GET['citydetails']) && !empty($_GET['countrycode'])){
     $countrycode = $_GET['countrycode'];
     $cities = $obj->getcities($countrycode);
     echo json_encode($cities);
  }
   //select stations using country code, city code
  if(isset($_GET['stationsdetails']) && !empty($_GET['citycode'])){
   $countrycode = $_GET['countrycode'];
     $citycode = $_GET['citycode'];
     $stations = $obj->getstations($countrycode,$citycode);
     echo json_encode($stations);
  }
  
   //select car categories using station code, pickup date
  if(isset($_GET['carcategories']) && !empty($_GET['stationcode']) && !empty($_GET['pickup'])){
     $pickup = date('Ymd',strtotime($_GET['pickup']));
   $stationcode = $_GET['stationcode'];
     $carcategories = $obj->getcarcategories($pickup,$stationcode);
     echo json_encode($carcategories);
  }
  
  //select open hours using station code, pickup date
  if(isset($_GET['openhours']) && !empty($_GET['stationcode']) && !empty($_GET['pickup'])){
     $pickup = date('Ymd',strtotime($_GET['pickup']));
   $stationcode = $_GET['stationcode'];
     $openhours = $obj->getopenhours($pickup,$stationcode);
     echo json_encode($openhours);
  }
  
  //select car categories using carcategory code, pickup date
  if(isset($_GET['quote']) && !empty($_GET['carcategorycode']) && !empty($_GET['pickup'])){
  $pickup = date('Ymd',strtotime($_GET['pickup']));
  $drop = date('Ymd',strtotime($_GET['drop']));
  $carcategorycode = $_GET['carcategorycode'];
  $stationcode = $_GET['stationcode'];
  $begintime = $_GET['begintime'];
  $endtime = $_GET['endtime'];
  $getquote = $obj->getquote($pickup,$drop,$carcategorycode,$stationcode,$begintime,$endtime);
  echo json_encode($getquote);
  }
