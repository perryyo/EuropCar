<?php
/**
  * Europcar Class 
  *
  * This class is used for send and receive the XML from Europcar API. 
  *
  * @filesource europcar.php
  * @api Europecar API
  * @since 1.0
 */
class EuropCar{

	/**
  * cURL request and response
  *
  * This function is used for send and receive the XML format. 
  * * We will convert the XML into Json formats using the simplexml_load_string function
  * * We Remove the @ character from the json response
  *
  * @param string $post_string is used for collect the XML request
  * @filesource station.php
  * @api Europecar API
  * @since 1.0
  * @return json
  */
 
	public function curlRequest($post_string){
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://applications-ptn.europcar.com/xrs/resxml");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
		$response = curl_exec($ch);
		$xml = simplexml_load_string($response);
		$json = str_replace('@attributes', 'attributes', json_encode($xml));
		$array = json_decode($json,TRUE);
		curl_close($ch);
		return $array;
	}

	/**
  * Getcountry List
  *
  * This function is used to get all country list from the Europecar API
  * @api Europecar API
  * @filesource europcar.php
  *
  * @param string $post_string is used for collect the XML request
  *
  * @return json country list
**/
	public function getCountries(){
	
		$post_string = 'XML-Request=<message>
						<serviceRequest serviceCode="getCountries">
						<serviceParameters><brand code ="EP"/></serviceParameters>
						</serviceRequest>
						</message>&callerCode=22467&password=12012015';
		$countries=$this->curlRequest($post_string);
		$countries=$countries['serviceResponse']['countryList']['country'];
		return $countries;
	
	}

	/**
  * Getcity List
  *
  * This function is used for collect the city list based on the countrycode.
  * @api Europecar API
  * @filesource europcar.php
  *
  * @param string $countrycode is used for collect the clity list.
  *
  * @return json city list
**/
	public function getCities($countrycode){
	
		$post_string = 'XML-Request=<message>
	  					<serviceRequest serviceCode="getCities">
						<serviceParameters>
		  				<country countryCode="'.$countrycode.'"/>
						</serviceParameters>
	  					</serviceRequest>
						</message>
						&callerCode=22467&password=12012015';
		$citylist=$this->curlRequest($post_string);
		$cityhtml='';
		$cities=$citylist['serviceResponse']['cityList']['city'];
		$cityhtml .='<option value="">Choose your City</option>';
		foreach ($cities as $city) {
			$cityhtml .='<option value="'.$city['attributes']['cityCode'].'">'.$city['attributes']['cityDescription'].'</option>';
		}
		return $cityhtml;
		
	}

	/**
  * Getstation list.
  *
  * This function is used for get the stations based on the city code
  * and to provide some background information or textual references.
  * @api Europecar API
  * @filesource europcar.php
  * @param string $countrycode is a Car Station code from user select.
  * @param int $citycode is a pickup time from user select.
  * @return json station list
**/

	public function getStations($countrycode,$citycode){
			
		
		$post_string = 'XML-Request=<message>
	 					<serviceRequest serviceCode="getStations">
						<serviceParameters>
		  				<station countryCode="'.$countrycode.'" cityName="'.$citycode.'" language="FR" />
						</serviceParameters>
	  					</serviceRequest>
						</message>&callerCode=22467&password=12012015';
	
		$stations=$this->curlRequest($post_string);
		$station=$stations['serviceResponse']['stationList']['station'];
		$stationhtml='';
		$stationhtml .='<option value="">Choose your Stations</option>';
		foreach ($station as $station) {
			$stationhtml .='<option value="'.$station['attributes']['stationCode'].'">'.$station['attributes']['stationName'].'</option>';
		}
		return $stationhtml;
	}
	
	/**
  * Getcarcategories list.
  *
  * This function is used for get the stations based on the city code
  * @api Europecar API
  * @filesource europcar.php
  * @param string $stationcode is a Car Station code from user select.
  * @param int $pickup is a pickup time from user select.
  *
  * @return json Carcategories list
**/

	public function getCarCategories($pickup,$stationcode){
		
		
		$post_string = 'XML-Request=<message>
						<serviceRequest serviceCode="getCarCategories">
						<serviceContext>
						<localisation active="true">
						<language code="fr_FR"/>
						</localisation>
						</serviceContext>
						<serviceParameters>
						<reservation>
						<checkout stationID="'.$stationcode.'" date="'.$pickup.'"/>
						</reservation>
						</serviceParameters>
						</serviceRequest>
						</message>&callerCode=22467&password=12012015';
	
		$carcategoriesxml  =$this->curlRequest($post_string);
		$carcategories = $carcategoriesxml['serviceResponse']['carCategoryList']['carCategory'];
		
		$carcategoryhtml = '';
		$carcategoryhtml .='<option value="">Choose your Car category</option>';
		foreach ($carcategories as $car){
			if(isset($car['attributes']['carCategoryCode'])){
				$carcategoryhtml .='<option value="'.$car['attributes']['carCategoryCode'].'">'.$car['attributes']['carCategoryName'].'</option>';
			}
		}
		return $carcategoryhtml;
	}
	
/**
  * Getopenhours.
  *
  * This function is used for get the open hours timings based on the station code.
  * @api Europecar API
  * @filesource europcar.php
  * @param string $stationcode is a Car Station code from user select.
  * @param int $pickup is a pickup time from user select.
  *
  * @return json openhours list
**/

	public function getOpenHours($pickup,$stationcode){
		
		
		
		$post_string = 'XML-Request=<message>
						<serviceRequest serviceCode="getOpenHours">
						<serviceParameters>
						<reservation>
						<checkout stationID="'.$stationcode.'" date="'.$pickup.'"/>
						</reservation>
						</serviceParameters>
						</serviceRequest>
						</message>&callerCode=22467&password=12012015';
		$openhoursxml  =$this->curlRequest($post_string);
		$begintime = $openhoursxml['serviceResponse']['openHoursList']['openHours']['attributes']['beginTime'];
		$endtime = $openhoursxml['serviceResponse']['openHoursList']['openHours']['attributes']['endTime'];
		$openhours = array($begintime,$endtime);
		return $openhours;
	}
	
/**
  * GetQuote
  *
  * This function is used for get the quotation for selected car based on the date and time.
  * @api Europecar API
  * @filesource europcar.php
  *
  * @param int $pickupdate is checkout date.
  * @param int $drop is a checkin date.
  * @param string $carcategorycode is a Car Category code from user select.
  * @param string $stationcode is a Car Station code from user select.
  * @param int $begintime is a pickup time from user select.
  * @param int $endtime is a drop time from user select.
  * @return json quote information.
**/
	
	public function getQuote($pickup,$drop,$carcategorycode,$stationcode,$begintime,$endtime){
		
		$post_string = 'XML-Request=<message>
						<serviceRequest serviceCode="getQuote">
						<serviceContext>
						<localisation active="true">
						<language code="fr_FR"/>
						</localisation>
						</serviceContext>
						<caller />
						<serviceParameters>
						<reservation carCategory="'.$carcategorycode.'">
						<checkout stationID="'.$stationcode.'" date="20181220" time="'.$begintime.'" />
						<checkin stationID="'.$stationcode.'" date="20181223" time="'.$endtime.'" />
						</reservation>
						<driver countryOfResidence="FR" />
						</serviceParameters>
						</serviceRequest>
						</message>&callerCode=22467&password=12012015';
	
		$quotexml  =$this->curlRequest($post_string);
		
		return $quotexml;
	}

/**
  * BookReservation.
  *
  * This function is used for book a reservation using our contract id.
  * @api Europecar API
  * @filesource europcar.php
  *
  * @param array $data is used for collect the XML request.
  *
  * @return json book confirmation response.
**/
	
	public function bookReservation($data){
		
		$pickup = date('Ymd',strtotime($data['pickup']));
		$drop = date('Ymd',strtotime($data['drop']));
		$carcategorycode = $data['carcategorycode'];
		$stationcode = $data['stationcode'];
		$begintime = $data['begintime'];
		$endtime = $data['endtime'];
		$post_string = 'XML-Request=<message>
						<serviceRequest serviceCode="getQuote">
						<serviceContext>
						<localisation active="true">
						<language code="fr_FR"/>
						</localisation>
						</serviceContext>
						<caller />
						<serviceParameters>
						<reservation carCategory="'.$carcategorycode.'">
						<checkout stationID="'.$stationcode.'" date="20181220" time="'.$begintime.'" />
						<checkin stationID="'.$stationcode.'" date="20181223" time="'.$endtime.'" />
						</reservation>
						<driver countryOfResidence="FR" />
						</serviceParameters>
						</serviceRequest>
						</message>&callerCode=22467&password=12012015';
	
		$quotexml  =$this->curlRequest($post_string);
		
		return $quotexml;
	}
	
}
// Object create
$obj = new EuropCar();
?>

