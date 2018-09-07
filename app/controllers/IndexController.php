<?php

use Phalcon\Mvc\Controller;

/**
 * IndexController Class 
 *
 * This class is used for send and receive the XML from Europcar API. 
 *
 * @filesource indexcontroller.php
 * @api Europecar API
 * @since 1.0
 */

class IndexController extends Controller
{
    
    /**
     * Getcountry List
     *
     * This function is used to get all country list from the Europecar API
     * @api Europecar API
     * @filesource indexcontroller.php
     *
     * @param string $post_string is used for collect the XML request
     *
     * @return json country list
     **/
    public function indexAction()
    {
        $this->assets->addCss("Europcar/index/css/style.css");
        
        $post_string = 'XML-Request=<message>
                        <serviceRequest serviceCode="getCountries">
                        <serviceParameters><brand code ="EP"/></serviceParameters>
                        </serviceRequest>
                        </message>&callerCode=22467&password=12012015';
        $countries   = $this->curlRequest($post_string);
        $countries   = $countries['serviceResponse']['countryList']['country'];
        return $this->view->countries = $countries;
        
        
    }
    
    /**
     * cURL request and response
     *
     * This function is used for send and receive the XML format. 
     * * We will convert the XML into Json formats using the simplexml_load_string function
     * * We Remove the @ character from the json response
     *
     * @param string $post_string is used for collect the XML request
     * @filesource indexcontroller.php
     * @api Europecar API
     * @since 1.0
     * @return void
     */
    
    public function curlRequest($post_string)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://applications-ptn.europcar.com/xrs/resxml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded"
        ));
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($http_status==200 && $array['serviceResponse']['attributes']['returnCode']=='OK'){
            $xml      = simplexml_load_string($response);
            $json     = str_replace('@attributes', 'attributes', json_encode($xml));
            $array    = json_decode($json, TRUE);
        
        }else{
            echo '<script>alert("Invalid Response")</script>';
			exit;
        }
        
        curl_close($ch);
        return $array;
    }
    
    
    
    
    /**
     * Getcity List
     *
     * This function is used for collect the city list based on the countrycode.
     * @api Europecar API
     * @filesource indexcontroller.php
     *
     * @param string $countrycode is used for collect the clity list.
     *
     * @return json city list
     **/
    public function getCitiesAction()
    {
        
        $countrycode = $_GET['countrycode'];
        
        $post_string = 'XML-Request=<message>
                          <serviceRequest serviceCode="getCities">
                        <serviceParameters>
                          <country countryCode="' . $countrycode . '"/>
                        </serviceParameters>
                          </serviceRequest>
                        </message>
                        &callerCode=22467&password=12012015';
        $citylist    = $this->curlrequest($post_string);
        $cityhtml    = '';
        $cities      = $citylist['serviceResponse']['cityList']['city'];
        $cityhtml .= '<option value="">Choose your City</option>';
        foreach ($cities as $city) {
            $cityhtml .= '<option value="' . $city['attributes']['cityCode'] . '">';
            $cityhtml .= $city['attributes']['cityDescription'] . '</option>';
        }
        
        return $this->view->cityhtml = $cityhtml;
        
    }
    
    /**
     * Getstation list.
     *
     * This function is used for get the stations based on the city code
     * and to provide some background information or textual references.
     * @api Europecar API
     * @filesource indexcontroller.php
     * @param string $countrycode is a Car Station code from user select.
     * @param int $citycode is a pickup time from user select.
     * @return json station list
     **/
    
    public function getStationsAction()
    {
        $countrycode = $_GET['countrycode'];
        $citycode    = $_GET['citycode'];
        
        
        $post_string = 'XML-Request=<message>
                         <serviceRequest serviceCode="getStations">
                        <serviceParameters>
                          <station countryCode="' . $countrycode . '" cityName="' . $citycode . '" language="FR" />
                        </serviceParameters>
                          </serviceRequest>
                        </message>&callerCode=22467&password=12012015';
        
        $stations    = $this->curlrequest($post_string);
        $station     = $stations['serviceResponse']['stationList']['station'];
        $stationhtml = '';
        $stationhtml .= '<option value="">Choose your Stations</option>';
        foreach ($station as $station) {
            $stationhtml .= '<option value="' . $station['attributes']['stationCode'] . '">';
            $stationhtml .= $station['attributes']['stationName'] . '</option>';
        }
        return $this->view->stationhtml = $stationhtml;
    }
    
    /**
     * Getcarcategories list.
     *
     * This function is used for get the stations based on the city code
     * @api Europecar API
     * @filesource indexcontroller.php
     * @param string $stationcode is a Car Station code from user select.
     * @param int $pickup is a pickup time from user select.
     *
     * @return json Carcategories list
     **/
    
    public function getCarCategoriesAction()
    {
        
        $pickup           = $_GET['pickup'];
        $stationcode      = $_GET['stationcode'];
        $carcategoriesxml = '';
        
        $post_string = 'XML-Request=<message>
                        <serviceRequest serviceCode="getCarCategories">
                        <serviceContext>
                        <localisation active="true">
                        <language code="fr_FR"/>
                        </localisation>
                        </serviceContext>
                        <serviceParameters>
                        <reservation>
                        <checkout stationID="' . $stationcode . '" date="' . $pickup . '"/>
                        </reservation>
                        </serviceParameters>
                        </serviceRequest>
                        </message>&callerCode=22467&password=12012015';
        
        $carcategoriesxml = $this->curlrequest($post_string);
        $carcategories = $carcategoriesxml['serviceResponse']['carCategoryList']['carCategory'];
        $carcategoryhtml = '';
        $carcategoryhtml .= '<option value="">Choose your Car category</option>';
        foreach ($carcategories as $car) {
            if (isset($car['attributes']['carCategoryCode'])) {
                $carcategoryhtml .= '<option value="'.$car['attributes']['carCategoryCode'] .'">';
                $carcategoryhtml .= $car['attributes']['carCategoryName'].'</option>';
            }
        }
        return $this->view->carcategoryhtml = $carcategoryhtml;
    }
    
    /**
     * Getopenhours.
     *
     * This function is used for get the open hours timings based on the station code.
     * @api Europecar API
     * @filesource indexcontroller.php
     * @param string $stationcode is a Car Station code from user select.
     * @param int $pickup is a pickup time from user select.
     *
     * @return json openhours list
     **/
    
    public function getOpenHoursAction()
    {
        
        $pickup      = $_GET['pickup'];
        $stationcode = $_GET['stationcode'];
        $post_string  = 'XML-Request=<message>
                        <serviceRequest serviceCode="getOpenHours">
                        <serviceParameters>
                        <reservation>
                        <checkout stationID="' . $stationcode . '" date="' . $pickup . '"/>
                        </reservation>
                        </serviceParameters>
                        </serviceRequest>
                        </message>&callerCode=22467&password=12012015';
        $openhoursxml = $this->curlrequest($post_string);
        $begintime    = $openhoursxml['serviceResponse']['openHoursList']['openHours']['attributes']['beginTime'];
        $endtime      = $openhoursxml['serviceResponse']['openHoursList']['openHours']['attributes']['endTime'];
        $openhours    = array(
            $begintime,
            $endtime
        );
        return $this->view->openhours = $openhours;
    }
    
    /**
     * GetQuote
     *
     * This function is used for get the quotation for selected car based on the date and time.
     * @api Europecar API
     * @filesource indexcontroller.php
     *
     * @param int $pickupdate is checkout date.
     * @param int $drop is a checkin date.
     * @param string $carcategorycode is a Car Category code from user select.
     * @param string $stationcode is a Car Station code from user select.
     * @param int $begintime is a pickup time from user select.
     * @param int $endtime is a drop time from user select.
     * @return json quote information.
     **/
    
    public function getQuoteAction()
    {
        
        $pickup          = $_GET['pickup'];
        $drop            = $_GET['drop'];
        $carcategorycode = $_GET['carcategorycode'];
        $stationcode     = $_GET['stationcode'];
        $begintime       = $_GET['begintime'];
        $stationcode     = $_GET['stationcode'];
        $endtime         = $_GET['endtime'];
        $post_string     = 'XML-Request=<message>
                        <serviceRequest serviceCode="getQuote">
                        <serviceContext>
                        <localisation active="true">
                        <language code="fr_FR"/>
                        </localisation>
                        </serviceContext>
                        <caller />
                        <serviceParameters>
                        <reservation carCategory="' . $carcategorycode . '">
                        <checkout stationID="' . $stationcode . '" date="20181220" time="' . $begintime . '" />
                        <checkin stationID="' . $stationcode . '" date="20181223" time="' . $endtime . '" />
                        </reservation>
                        <driver countryOfResidence="FR" />
                        </serviceParameters>
                        </serviceRequest>
                        </message>&callerCode=22467&password=12012015';
        
        $quotexml = $this->curlrequest($post_string);
        
        return $this->view->quotexml = $quotexml;
    }
    
    /**
     * BookReservation.
     *
     * This function is used for book a reservation using our contract id.
     * @api Europecar API
     * @filesource indexcontroller.php
     *
     * @param array $data is used for collect the XML request.
     *
     * @return json book confirmation response.
     **/
    
    public function bookReservationAction()
    {
        
        $pickup          = date('Ymd', strtotime($data['pickup']));
        $drop            = date('Ymd', strtotime($data['drop']));
        $carcategorycode = $data['carcategorycode'];
        $stationcode     = $data['stationcode'];
        $begintime       = $data['begintime'];
        $endtime         = $data['endtime'];
        $post_string     = 'XML-Request=<message>
                        <serviceRequest serviceCode="getQuote">
                        <serviceContext>
                        <localisation active="true">
                        <language code="fr_FR"/>
                        </localisation>
                        </serviceContext>
                        <caller />
                        <serviceParameters>
                        <reservation carCategory="' . $carcategorycode . '">
                        <checkout stationID="' . $stationcode . '" date="20181220" time="' . $begintime . '" />
                        <checkin stationID="' . $stationcode . '" date="20181223" time="' . $endtime . '" />
                        </reservation>
                        <driver countryOfResidence="FR" />
                        </serviceParameters>
                        </serviceRequest>
                        </message>&callerCode=22467&password=12012015';
        
        $quotexml = $this->curlrequest($post_string);
        
        return $quotexml;
    }
    
    
}