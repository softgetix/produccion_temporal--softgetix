<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('vendor/autoload.php');
use GuzzleHttp\Post\PostFile;

session_start();
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

class WatiApi extends CI_Controller {

	protected $token;
	protected $wati_url;

	function __construct() 
	{
    parent::__construct();
    $this->getWhatsAppCredentials();
  	
  }

	public function index()
	{
		$data['us_id'] = isset($_GET['us_id']) ? $_GET['us_id'] : '';
		$gmt = isset($_GET['gmt']) ? $_GET['gmt'] : '';
		$data['gmt'] = $gmt;
		
		$this->load->view('WPApi/welcome_message',$data);
	}

	public function getWhatsAppCredentials(){

		if(!empty($_SESSION['wati_token']) && !empty($_SESSION['wati_url'])){
  		$this->token = $_SESSION['wati_token'];
  		$this->wati_url = $_SESSION['wati_url'];
  		//echo $this->token;
  	}else{
	   	$response = $this->WsCall('whatsappcredentials',1);
	   	$rs_array = json_decode($response);
	   //	echo '<pre>';print_r($_SESSION);echo '</pre>';	
	   	if($rs_array->transaction_status == 'ok'){
	   	  $credentials = $rs_array->credentials;
	   	  $this->token = $credentials[0]->token;
	   	  $this->wati_url = $credentials[0]->url;
	   	  $_SESSION['wati_token'] =  $this->token;
	   	  $_SESSION['wati_url'] =  $this->wati_url;
   	}
   }

	}


	public function WsCall($action, $cred=0){
		
		$wsUrl = 'http://localhost/gateway/wswati.php';	

		if($cred == 1) $wsUrl = 'http://localhost/gateway/wswati.php';	
		
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
			  	//echo $url;
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

			  case 'getMessages':
			  $contactPhoneNumber = isset($_POST['wp_number']) ? $_POST['wp_number'] : '';	
			  $url .= '&contactPhoneNumber='.$contactPhoneNumber;
				$page_number = isset($_POST['page_number']) ? $_POST['page_number'] : '';
			  $url .= '&pageSize=10&pageNumber='.$page_number;
			  //echo $url;
			  case 'whatsappcredentials':
			  $url;

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
			$token = $this->token;
			$url = $this->wati_url;
			$name = $post['name'];
			
			$params = '&pageSize=10&'.'pageNumber='.$page_number;
		//	$response = $this->WatiCall($url,$token,"getMessages/$wp_number",$params);
			$response = $this->WsCall("getMessages");
			$response_array = json_decode($response);
			//echo '<pre>';print_r($response_array);echo '</pre>';die;
			if(!empty($response_array->info) && $response_array->info == 'Invalid WhatsApp Phone Number'){
          $this->addContact($url,$token,$wp_number,$name);      
      } 

      if (!empty($response_array->info) && $response_array->info == 'Invalid Conversation') {
       	$this->sendMessageWithoutPost($url,$wp_number,'',$token,$name);
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

    	$html = $this->load->view('WPApi/messages',$data, true);	
    	//echo $html;die;
    //$new_message_status = $this->WsCall('NewMessageStatus');
    	$new_message_status = [];
    	//var_dump($new_message_status);
    	$response = array('html'=>$html,'new_message_status' => '');
    	echo json_encode($response);

     }	
	}

	public function getMedia()
	{
		if($this->input->post()){
			$post = $this->input->post();
			$fileName = $post['fileName'];
			$token = $this->token;
			$url = $this->wati_url;
			$extension = $post['extension'];
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
			}elseif($fileType == 'document'){
							$mime = "application/$extension";

							switch ($extension) 
							{
								  case 'xlsx':
								   $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
								    break;
								 
								   default:
								   $mime = "application/$extension";
							}
						  $file = 'data:' . $mime . ';base64,' . $base64;	
							$donwload = "<a href='$file' download>Download file</a>"; //basename()
							echo $donwload;
			}else{
						$mime = "$fileType/$extension";
			      $file = 'data:' . $mime . ';base64,' . $base64;	
						$donwload = "<a href='$file' download>Download file</a>";
						echo $donwload;
			}
	      	//echo '<pre>';print_r($img);echo '</pre>';die('adsa');
      }	
	}
  public function watiPost($url, $token, $action, $params, $postData)
	{
		
		if(!empty($params))
			$action .= '?'.$params;

		//echo $url.'/'.$action;die;
		//echo json_encode($postData);die;
		$client = new GuzzleHttp\Client();
		$response = $client->post($url.'/api/v1/'.$action,
		 [
		  'body' => json_encode($postData),	
		  'headers' => [
		    'Authorization' => $token,
		    'Content-Type' => 'text/json'
		  ],
		 /* [
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
  		//echo '<pre>';print_r($post);echo '</pre>';die;
  		$wp_number = $post['phone'];
  		$messageText = $post['messageText'];
  		$template = $post['template'];
  		$templateName = $post['templateName'];
  		$user_name = $post['user_name'];
  		$token = $this->token;
			$url = $this->wati_url;

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
  			$params = 'messageText='.urlencode($messageText);
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

 	public function sendFiles()
  {	
  	if($this->input->post()){
  		$post =	$this->input->post();
  		$wp_number = $post['phone'];
  		$token = $this->token;
			$url = $this->wati_url;
			if(!empty($_FILES)){
			 $files = $_FILES['files'];  
			 $name = $files['name']; 
			 $tmp = $files['tmp_name'];
			 $type = $files['type'];
			 //echo '<pre>';print_r($files);echo '</pre>';die;
			 //$tmp_file = file_get_contents($tmp_name);
			 //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($tmp_file);
 			 
 			 $action = 'sendSessionFile/'.$wp_number;
 			  $client = new GuzzleHttp\Client();
				$path = FCPATH.'/temp/';
				$final_file = rand(1000,1000000).$name;
				$path = $path.strtolower($final_file); 

				if(move_uploaded_file($tmp,$path)){

				  chown($path, 'localizartcodigo');
					chmod($path, 0777);
					 $response = $client->post( $url.'/api/v1/'.$action, [
						    'body' => [
						        'name' => $files['name'],
						        'file' => new PostFile('file', fopen($path, 'r') )
						    ],
						   'headers' => [
							 'Authorization' => $token
							 ],
						]);
					  $responseBody = $response->getBody();
					  echo $responseBody;
					  unlink($path);
				}
  		}
  	}
  }
   public function sendRecording()
  {	
  	if($this->input->post()){
  		$post =	$this->input->post();
  		$wp_number = $post['wp_number'];
  		$token = $this->token;
			$url = $this->wati_url;
			//echo '<pre>';print_r($post);echo '</pre>';
		//	echo '<pre>';print_r($_FILES);echo '</pre>';die;
			if(!empty($_FILES)){
			 $files = $_FILES['files'];  
			 $name = $files['name']; 
			 $tmp = $files['tmp_name'];
			 $type = $files['type'];
			 //echo '<pre>';print_r($files);echo '</pre>';die;
			 //$tmp_file = file_get_contents($tmp_name);
			 //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($tmp_file);
 			 
 			 $action = 'sendSessionFile/'.$wp_number;
 			  $client = new GuzzleHttp\Client();
				$path = FCPATH.'/temp/';
				$final_file = rand(1000,1000000).$name.'.mp3';
				$path = $path.strtolower($final_file); 

				if(move_uploaded_file($tmp,$path)){

				  chown($path, 'localizartcodigo');
					chmod($path, 0777);
					 $response = $client->post( $url.'/api/v1/'.$action, [
						    'body' => [
						        'name' => $files['name'],
						        'file' => new PostFile('file', fopen($path, 'r') )
						    ],
						   'headers' => [
							 'Authorization' => $token
							 ],
						]);
					  $responseBody = $response->getBody();
					  echo $responseBody;
					  unlink($path);
				}
  		}
  }				
}

}
