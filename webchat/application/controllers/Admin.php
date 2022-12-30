<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

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
			if(isset($id) && !empty($id))
			{	$this->load->view('admin/index');  }
			else
			{	redirect(base_url('admin/login'));  }
	}
	public function set_login()
	{
		$name = $this->input->post("username");
		$password = $this->input->post("password");
		$data = array("user_name"=>$name,"password"=>$password);
		$result =$this->userdata->admin_login($data);
		if(isset($result) && !empty($result))
		{
			$session_id = $this->session->set_userdata("session_id",$result[0]['id']);
			$session_name = $this->session->set_userdata("session_name",$result[0]['user_name']);
			redirect(base_url('admin/index'));
		}else{
            $this->session->set_flashdata('error_msg',"Login username and password not matched."); 
			redirect(base_url('admin/login'));
		}
	}
	public function login()
	{
		$id = $this->session->userdata("session_id");
		$name = $this->session->userdata("session_name");
			if(!isset($id) && empty($id))
			{	$this->load->view('admin/login');  }
			else
			{	redirect(base_url('admin/index'));  }
	}
	public function logout()
	{
		$this->session->unset_userdata("session_id");
		$this->session->unset_userdata("session_id");
		redirect(base_url('admin/login'));
	}
	public function save_tbl_group_content()
	{
		$this->load->helper('string');

		  $name = $this->input->post("name");

		  $icon = time().$_FILES['icon']['name'];
		  $file['allowed_types'] = 'jpg|jpeg|png';
          $file['upload_path'] = 'assets/image_upload';
          $file['file_name'] = $icon;
    	  $this->load->library('upload',$file);
    	  $image = $this->upload->do_upload('icon');
          if(!$image) {	echo "file type error"; die; }
		  $file_data = $this->upload->data();
		  $db_file = $file_data['file_name'];

		$last_connection = $this->input->post("last_connection");
		$description = $this->input->post("description");
		$description_link = $this->input->post("description_link");

		  $unread_image = time().$_FILES['unread_image']['name'];
		  if(isset($_FILES['unread_image']['name']) && !empty($_FILES['unread_image']['name']))	
		  {
		  	//var_dump($_FILES['unread_image']);
			  $file['allowed_types'] = 'jpg|jpeg|png';
	          $file['upload_path'] = 'assets/image_upload';
	          $file['file_name'] = $unread_image;
	    	  $this->load->library('upload',$file);
	    	  $image = $this->upload->do_upload('unread_image');
	          if(!$image) {	echo "file type error"; die; }
			  $file_data = $this->upload->data();
			  $db_file_unread = $file_data['file_name'];
		}else{
		      $db_file_unread = "";
		}
		$unread_text = $this->input->post("unread_text");
		$unread_link = $this->input->post("unread_link");

		$data = array("Gc_name"=>$name,"Gc_icon"=>$db_file,"Gc_last_connection"=>$last_connection,"Gc_description"=>$description,"Gc_description_link"=>$description_link,"gc_unread_image"=>$db_file_unread,"Gc_unread_text"=>$unread_text,"Gc_unread_link"=>$unread_link);
		
		$result =$this->userdata->save_tbl_group_content($data);
		if($result)
		{	echo json_encode("success");die; }
		else
		{  echo json_encode("error"); die; }

	}	
	public function tbl_group_content_fetch()
	{	
		$data = $this->userdata->tbl_group_content_fetch();
		$new_data=array();
		foreach ($data as $value) {

			$value['action'] = "<a class='glyphicon glyphicon-edit edit' href='".base_url('admin/edit')."?id=".$value['gc_id']."'>Edit</a> ";
 			$value['Gc_icon'] ="<img src=".base_url()."/assets/image_upload/".$value['Gc_icon']." height='100' width='100' >";
			$new_data[]=$value;
		}
	  $result = ["sEcho" => 1,
          "iTotalRecords" => count($new_data),
          "iTotalDisplayRecords" => count($new_data),
          "aaData" => $new_data ];

		echo json_encode($result);
		die();
	}
	public function user_list()
	{
		$data = $this->userdata->tbl_group_content_fetch();
	    echo json_encode($data);
		die();
	}
	public function user_chat_list()
	{
		$data = $this->userdata->tbl_group_content_fetch();
	    echo json_encode($data);
		die();
	}
	public function user_list_search()
	{
		$search = $this->input->post('search');
		$data = $this->userdata->user_list_search($search);
	    echo json_encode($data);
		die();
	}
	public function get_data_by_user()
	{
		$id = $this->input->post('id');
		$data = $this->userdata->get_data_by_user($id);
	    echo json_encode($data);
		die();
	}

	public function edit()
	{
		$this->load->view('admin/edit');
	}
	public function edit_data_by_id()
	{
		$id = $this->input->post('id');
		$data = $this->userdata->edit_data_by_id($id);
	    echo json_encode($data);
		die();
	}
	public function update()
	{
		$this->load->helper('string');
		  $id = $this->input->post("id");

		  $name = $this->input->post("name");
		 
		  if(isset($_FILES['icon']['name']) && !empty($_FILES['icon']['name']))	
		  {
		  $icon = time().$_FILES['icon']['name'];
		  $file['allowed_types'] = 'jpg|jpeg|png';
          $file['upload_path'] = 'assets/image_upload';
          $file['file_name'] = $icon;
    	  $this->load->library('upload',$file);
    	  $image = $this->upload->do_upload('icon');
          if(!$image) {	echo "file type error"; die; }
		  $file_data = $this->upload->data();
		  $db_file = $file_data['file_name'];
		  }else{
		  $db_file = $this->input->post('icon_old');
		  }
		$last_connection = $this->input->post("last_connection");
		$description = $this->input->post("description");
		$description_link = $this->input->post("description_link");

		  $unread_image = time().$_FILES['unread_image']['name'];
		  if(isset($_FILES['unread_image']['name']) && !empty($_FILES['unread_image']['name']))	
		  {
		  	//var_dump($_FILES['unread_image']);
			  $file['allowed_types'] = 'jpg|jpeg|png';
	          $file['upload_path'] = 'assets/image_upload';
	          $file['file_name'] = $unread_image;
	    	  $this->load->library('upload',$file);
	    	  $image = $this->upload->do_upload('unread_image');
	          if(!$image) {	echo "file type error"; die; }
			  $file_data = $this->upload->data();
			  $db_file_unread = $file_data['file_name'];
		}else{
		      $db_file_unread = $this->input->post('old_unread_image');
		}
		$unread_text = $this->input->post("unread_text");
		$unread_link = $this->input->post("unread_link");

		$data = array("Gc_name"=>$name,"Gc_icon"=>$db_file,"Gc_last_connection"=>$last_connection,"Gc_description"=>$description,"Gc_description_link"=>$description_link,"gc_unread_image"=>$db_file_unread,"Gc_unread_text"=>$unread_text,"Gc_unread_link"=>$unread_link);
		//echo "<pre>";print_r($data);die;
		$result =$this->userdata->update($data,$id);
		if($result)
		{	redirect(base_url('admin/index')); }
		else
		{  echo "error"; }
	}


}
?>
