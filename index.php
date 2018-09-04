<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EuropCar API</title>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="style.css" rel="stylesheet" media="screen">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script>
$(document).ready(function() {

//ajax call is used for collect the city list based on the country code
$('#country').change(function() {
	$.ajax({
		url: 'src/apicall.php',
		type: 'GET',
		dataType: 'JSON',
		data: {countrycode : $(this).val(),citydetails : 'citylist'},
		success: function(data) {
			$("#citylist").html(data);
		}
	});
});

//This change function used for collect the station list based on the city code 
$('#citylist').change(function() {
	var country = $('#country').val();
	var citylist = $('#citylist').val();
	$.ajax({
		url: 'src/apicall.php',
		type: 'GET',
		dataType: 'JSON',
		data: {countrycode : country,citycode : citylist, stationsdetails : 'stationslist'},
		success: function(data) {
			$("#stationslist").html(data);
		}
	});
});

//This ajax call is used for collect the car carcategry list
$('#stationslist').change(function() {
	var stationcode = $('#stationslist').val();
	var pickup = '2018-12-20';
	if(stationcode !== ''){
		$.ajax({
			url: 'src/apicall.php',
			type: 'GET',
			dataType: 'JSON',
			data: {stationcode : stationcode, pickup : pickup, carcategories: 'carcategories'},
			success: function(data) {
			$("#carcategorylist").html(data);
			}
		});
		
		$.ajax({
			url: 'src/apicall.php',
			type: 'GET',
			dataType: 'JSON',
			data: {stationcode : stationcode, pickup : pickup, openhours: 'openhours'},
			success: function(data) {
				$('#begintime').val(data[0]);
				$('#endtime').val(data[1]);
			}
		});
	
	}
});

$('#carcategorylist').change(function() {
	var stationcode = $('#stationslist').val();
	var carcategorycode = $('#carcategorylist').val();
	var pickup = '2018-12-20';
	var drop = '2018-12-23';
	var begintime = $('#begintime').val();
	var endtime = $('#endtime').val();
	$.ajax({
		url: 'src/apicall.php',
		type: 'GET',
		dataType: 'JSON',
		data: {stationcode : stationcode, carcategorycode : carcategorycode, pickup : pickup, drop : drop, begintime: begintime, endtime: endtime, quote : 'getquote'},
		success: function(data) {
			//$("#stationlist").html(data);
		}
	});
});

});
</script>

</head>
<?php 
//include the Europcar.php file for collecting the API response
include('src/europcar.php');

$countries = $obj->getcountries();
$cityhtml='';
?>

<body>
<h1>Europcar API Demo</h1>
<form action="" method="">
	<div class="form-container">
        <div class="form-block">
            <label for="country">Country</label>
            <select id="country">
                <option value=""  selected >Choose your country</option>
                <?php 
                foreach ($countries as $country) {
                    # code...
                     echo $cityhtml .='<option value="'.$country['attributes']['countryCode'].'">'.$country['attributes']['countryDescription'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-block">
        	<label for="city">City</label>
            <select id="citylist">
                <option value=""  selected >Choose your City</option>
            </select>
        </div>
        <div class="form-block">
            <label for="stations">Stations</label>
            <select id="stationslist">
            	<option value=""  selected >Choose your Stations</option>
            </select>
        </div>     
        <div class="form-block">
            <label for="car_category">Car category</label>
            <select id="carcategorylist">
                <option value=""  selected >Choose your Car category</option>
            </select>
        </div>
        <input type="hidden" id="begintime" value=""  />
        <input type="hidden" id="endtime" value=""  />
    </div>
</form>
</body>
</html>

