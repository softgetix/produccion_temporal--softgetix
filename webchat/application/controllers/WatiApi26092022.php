<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('vendor/autoload.php');

session_start();
//error_reporting(E_ALL & ~E_NOTICE);


class WatiApi extends CI_Controller {

	function __construct() {
        parent::__construct();
        $gmt = isset($_GET['gmt']) ? $_GET['gmt'] : '';
		
    }

	public function index()
	{
		$data['us_id'] = isset($_GET['us_id']) ? $_GET['us_id'] : '';
		$gmt = isset($_GET['gmt']) ? $_GET['gmt'] : '';
		$data['gmt'] = $gmt;
		
		$this->load->view('WPApi/welcome_message',$data);
	}

	public function WsCall($action){
		
		$wsUrl = 'http://localhost/gateway/wswati.php';
		$clientID = 'MDI1YkpPcThCS0ZyTHRoY2Y3NEdXMVpBRTM5N0lpMGV1WTZkMWNm';
		$clientSecret = '0e3a35916d4910594634c32fe13830af';
		
		$us_id = isset($_POST['us_id']) ? $_POST['us_id'] : '';
		$gmt = isset($_POST['gmt']) ? $_POST['gmt'] : '';
		//print_r($_POST);
		$url = $wsUrl.'?clientID='.$clientID.'&clientSecret='.$clientSecret.'&us_id='.$us_id.'&action='.$action;
		//echo $url;
		switch ($action) {

			  case 'contactlist':

			  	$contactlistfilterid = isset($_POST['contactlistfilterid']) ? $_POST['contactlistfilterid'] : 1;

			  	if(!empty($_POST['search'])){
			  		$search = $_POST['search'];
			  		$url .= '&searchbar='.$search;
			  	}else{

					$url .= '&contactlistfilterid='.$contactlistfilterid;
			  	}
			  	//echo $url;die;
			    break;

			  case 'NewMessageStatus':
			  	
			  	$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
			  	$url .= '&contactPhoneNumber='.$phone;
			    break;

			  case 'contactdetail':
			  	
			  	$contactDetailId = isset($_POST['contactDetailId']) ? $_POST['contactDetailId'] : '';	
			  	$url .= '&ContactDetail_id='.$contactDetailId;
			  	//echo $url;die;
			    break;

			  case 'changeticketstatus':
			  
			  	$contactDetailId = isset($_POST['contactDetailId']) ? $_POST['contactDetailId'] : '';
			  	$newTicketStatus = isset($_POST['newTicketStatus']) ? $_POST['newTicketStatus'] : '';	
			  	$url .= '&ContactDetail_id='.$contactDetailId;
			  	$url .= '&newticketstatus='.$newTicketStatus;
			  	//echo $url;die;
			    break;

			  case 'templatesent':
			  $contactDetailId = isset($_POST['contactDetailId']) ? $_POST['contactDetailId'] : '';	  
			  $url .= '&ContactDetail_id='.$contactDetailId;
			  //echo $url;
			  default:
		}		
		//echo $url;die;
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    $response_array = json_decode($response);
	    //echo '<pre>';print_r($response_array);echo '</pre>';	
	    /*if($response_array->transaction_status == 'ok'){
			//return $response_array;	    	
	    }else{
	    	//return 'Error';
	    }*/
	    //echo $response;die;
	    return $response;
	}


	public function WatiCall($url, $token, $action, $params = NULL)
	{
		
		$media = false;		
		if($action == 'getMedia')
			$media = true;
		
		if(!empty($params))
			$action .= '?'.$params;

	
		$client = new GuzzleHttp\Client();
		$response = $client->get($url.'/api/v1/'.$action, 
		 [
		  'headers' => [
		    'Authorization' => $token,
		  ],
		]);

		$responseBody = $response->getBody();
		
		if($media == true){
			$body = $responseBody->getContents();
			return $body;
		
		}else{
			return $responseBody;	
		}	
	}

	public function getContacts()
	{	   
    $response = $this->WsCall('contactlist');
	  $response_array = json_decode($response);
      $data['contacts'] = $response_array;
      $data['prev_contacts'] = 	isset($_SESSION['prev_contacts']) ? $_SESSION['prev_contacts'] : ''; 
      $_SESSION['prev_contacts'] = $response_array;
	  	//var_dump($_SESSION['prev_contacts']);
      echo $this->load->view('WPApi/contact_list',$data, true);die;		
	}

	public function getContactFilters()
	{
	  $response = $this->WsCall('contactlistfilters');
	  echo $response;die; 	
	}
	public function NewMessageStatus()
	{
	  $response = $this->WsCall('NewMessageStatus');
	  echo $response;die; 	
	}  

	public function getMessages()
	{
		if($this->input->post()){

			$post = $this->input->post();
			$wp_number = $post['wp_number'];
			$page_number = $post['page_number'];
			$refresh = $post['refresh'];
			$gmt = $post['gmt'];
			$token = $post['token'];
			$url = $post['url'];
			$name = $post['name'];
			
			$params = '&pageSize=10&'.'pageNumber='.$page_number;
			$response = $this->WatiCall($url,$token,"getMessages/$wp_number",$params);
			$response_array = json_decode($response);
       
      if(!empty($response_array->info) && $response_array->info == 'Invalid WhatsApp Phone Number'){
          $this->addContact($url,$token,$wp_number,$name);      
      } 

       if(!empty($response_array->info) && $response_array->info == 'Invalid Conversation') {
       	//$this->sendMessageWithoutPost($url,$wp_number,'',$token,$name);
       }

    	$data['messages'] = $response_array;
    	//echo '<pre>';print_r($response_array);echo '</pre>';die;
    	$data['prev_messages'] = 	isset($_SESSION['prev_messages']) ? $_SESSION['prev_messages'] : ''; 
    	if($page_number == 1){
      	$_SESSION['prev_messages'] = $response_array;
    	}
    	$data['page_number'] = $page_number;
    	$data['gmt'] = $gmt;
    	$data['refresh'] = $refresh;
    	$this->load->view('WPApi/messages',$data);	
      }	
	}

	public function getMedia()
	{
		if($this->input->post()){
			$post = $this->input->post();
			$fileName = $post['fileName'];
			$token = $post['token'];
			$url = $post['url'];
			//$fileName = 'data/images/febe8ddd-072b-4c21-b309-282fd32d1ab7.jpg';

			$params = 'fileName='.$fileName;
			$fileType =  $post['fileType'];
			//$fileTyp = 'image';
			$response = $this->WatiCall($url,$token,"getMedia",$params);

			$base64 = base64_encode($response);
			if($fileType == 'image'){
			  $mime = "$fileType/jpeg";
			  $file = 'data:' . $mime . ';base64,' . $base64;
			  $img = "<a class='example-image-link' href='$file' data-lightbox='example-1'><img src='$file' alt='image' class='example-image' alt='image-1' /></a>";
			  //$img = "<a href='$img' download><img src='$img'></a>";
	      	  echo $img;
			}elseif($fileType == 'audio'){
			   $mime = "$fileType/mpeg";
			   $file = 'data:' . $mime . ';base64,' . $base64;	
			   $mime1 = "$fileType/ogg";
			   $file1 = 'data:' . $mime1 . ';base64,' . $base64;	
			   $audio =	'<audio class="media_audio" controls="controls"><source src="'.$file.'" type="'.$mime.'"><source src="'.$file1.'" type="audio/mpeg">Your browser does not support the audio element.</audio>';
			   echo $audio; 
			}elseif($fileType == 'video'){
				$mime = "$fileType/mp4";
			    $file = 'data:' . $mime . ';base64,' . $base64;	
				$mime1 = "$fileType/ogg";
			    $file1 = 'data:' . $mime1 . ';base64,' . $base64;	
				$video = '<video width="320" height="240" controls><source src="'.$file.'" type="'.$mime.'"><source src="'.$file1.'" type="'.$mime1.'">Your browser does not support the video tag.</video>';
				echo $video;
			}else{
				$donwload = "<a href='$base64' download>descargar archivo</a>";
				echo $donwload;
			}
	      	//echo '<pre>';print_r($img);echo '</pre>';die('adsa');
      }	
	}
  public function watiPost($url, $token, $action, $params, $postData)
	{
		
		if(!empty($params))
			$action .= '?'.$params;

		//echo $url.'/api/v1/'.$action;
		//echo json_encode($postData);
		$client = new GuzzleHttp\Client();
		$response = $client->post($url.'/api/v1/'.$action,
		 [
		  'body' => json_encode($postData),	
		  'headers' => [
		    'Authorization' => $token,
		    'Content-Type' => 'text/json'
		  ],
		  /*[
		  	'body' => json_encode($postData)
		  ]*/
		 ]
		);
		

		$responseBody = $response->getBody();
		return $responseBody;
  }	
	
  public function sendMessage()
  {	
  	if($this->input->post()){
  		$post =	$this->input->post();
  		//echo '<pre>';print_r($post['templatesent']);echo '</pre>';die;
  		$wp_number = $post['phone'];
  		$messageText = $post['messageText'];
  		$template = $post['template'];
  		$templateName = $post['templateName'];
  		$user_name = $post['user_name'];
  		$token = $post['token'];
			$url = $post['url'];
  		$watiPost = array();
  		if($template == 1){
  			$params ='whatsappNumber='.$wp_number;
  			$action = 'sendTemplateMessage';

  			$parameters =  array( 
  								array('name'=> 'user_name','value'=> $user_name),
  								array('name'=> 'name','value'=> 'Ariel Formica'),
  							);
  			
  			$watiPost = array(
  				'template_name'=> $templateName,
  				'broadcast_name'=> 'forzagps',
  				'parameters' => $parameters
  			);
  			if($post['templatesent'] == true){
  				$this->WsCall('templatesent');
  			}

  		}else{
  			$params = 'messageText='.$messageText;
  			$action = 'sendSessionMessage/'.$wp_number;
  		}
  		$response = $this->watiPost($url,$token,$action,$params,$watiPost);
  		echo $response;

  	}
  }

  public function changeTicketStatus(){
  	  $response = $this->WsCall('changeticketstatus');
	  echo $response;die;
  }
  public function contactDetail(){
  	  $response = $this->WsCall('contactdetail');
      echo $response;die;
  }
  public function iamhere(){
  	  $response = $this->WsCall('iamhere');
      echo $response;die;
  }	
  public function addContact($url,$token,$wp_number,$name){	
      $params = array(
      							'name' => $name,
      							'customParams' => [ array("name" => "Name","value" => $name) ]
      						);
      $response = $this->watiPost($url,$token,"addContact/$wp_number",'',$params);
      $response = json_decode($response);
      //print_r($response);
  }
 		
 		public function sendMessageWithoutPost($url,$number,$template=1,$token,$name){

  		//echo '<pre>';print_r($post['templatesent']);echo '</pre>';die;
  		$wp_number = $number;
  		$user_name = $name;
  		$token = $token;

  		$watiPost = array();
  		
  			$params ='whatsappNumber='.$wp_number;
  			$action = 'sendTemplateMessage';

  			$parameters =  array( 
  								array('name'=> 'user_name','value'=> $user_name),
  								array('name'=> 'name','value'=> 'Ariel Formica'),
  							);
  			
  			$watiPost = array(
  				'template_name'=> 'mensaje_bienvenida',
  				'broadcast_name'=> 'forzagps',
  				//'parameters' => $parameters
  			);
  		
  		$response = $this->watiPost($url,$token,$action,$params,$watiPost);
  		//echo $response;
 		}
}
