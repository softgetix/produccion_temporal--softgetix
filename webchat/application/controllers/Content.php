<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model("userdata");
	    $this->load->library('session');
	}

	public function index()
	{
			$id = $this->session->userdata("session_id");
		    $name = $this->session->userdata("session_name");
		    $data['user'] = $this->userdata->tbl_group_content_fetch();
			if(isset($id) && !empty($id))
			{	$this->load->view('admin/content',$data);  }
			else
			{	redirect(base_url('admin/login'));  }
	}
    public function tbl_content_fetch()
	{	
		$data = $this->userdata->tbl_content_fetch();
		$new_data=array();
		$i = 1;
		foreach ($data as $value) {

			$value['action'] = "<button href='#' class='btn btn-delete delete delete_message' data-value=".$value['ct_id'].">Delete</button> ";
			$value['number'] = $i;
			if(!empty($value['ct_image']))
			{
 			$value['Gc_icon'] ="<img src=".base_url()."/assets/image_upload/".$value['ct_image']." height='100' width='100' >";
	 		}else{
	 			$value['Gc_icon'] = "<img src=".base_url("/assets/image_upload/pp.png")." height='100' width='100'>";
	 		}
			$new_data[]=$value;
			$i++;
		}
	  $result = ["sEcho" => 1,
          "iTotalRecords" => count($new_data),
          "iTotalDisplayRecords" => count($new_data),
          "aaData" => $new_data ];

		echo json_encode($result);
		die();
	}
	public function save_tbl_content()
	{
		$this->load->helper('string');
		date_default_timezone_set('Asia/Kolkata'); 

		$name = $this->input->post("name");
		$read_text = $this->input->post("read_text");
		$read_link = $this->input->post("read_link");
		$date = date('Y-m-d h:i:s');

		  $read_image = time().$_FILES['read_image']['name'];
		  if(isset($_FILES['read_image']['name']) && !empty($_FILES['read_image']['name']))	
		  {
		  	//var_dump($_FILES['unread_image']);
			  $file['allowed_types'] = 'jpg|jpeg|png';
	          $file['upload_path'] = 'assets/image_upload';
	          $file['file_name'] = $read_image;
	    	  $this->load->library('upload',$file);
	    	  $image = $this->upload->do_upload('read_image');
	          if(!$image) {	echo "file type error"; die; }
			  $file_data = $this->upload->data();
			  $db_file_unread = $file_data['file_name'];
		}else{
		      $db_file_unread = "";
		}

		$data = array("ct_gc_id"=>$name,"ct_text"=>$read_text,"ct_image"=>$db_file_unread,"ct_link"=>$read_link,"ct_message_date"=>$date);
		// echo "<pre>";print_r($data);die;
		$result =$this->userdata->save_content($data);
		if($result)
		{	echo json_encode("success");die; }
		else
		{  echo json_encode("error"); die; }

	}	

	public function delete_message()
	{
		$id = $this->input->post('id');
		$result =$this->userdata->delete_message($id);
		if($result)
		{	echo json_encode("success");die; }
		else
		{  echo json_encode("error"); die; }
	}
}


?>
