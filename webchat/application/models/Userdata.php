<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userdata extends CI_Model{

		public function __construct()
		{
			$this->load->database();
		}

		public function admin_login($data)
		{
			$this->db->where($data);
			$qu = $this->db->get('user');
			$result = $qu->result_array();
		    return $result;
		}
		public function save_tbl_group_content($data)
		{
			$qu = $this->db->insert('tbl_group_content',$data);
			return $qu;
		}
		public function tbl_group_content_fetch()
		{
				$this->db->order_by("Gc_name", "asc");
			$qu = $this->db->get('tbl_group_content');
			$data = $qu->result_array();
			return $data;
		}
		public function user_chat_list()
		{
			      $this->db->where('ct_gc_id',$id);
			$qu = $this->db->get('tbl_content_table');
			$data = $qu->result_array();
			return $data;
		} 
		public function user_list_search($search)
		{
			$this->db->like('Gc_name',$search);
			$this->db->order_by("Gc_name", "asc");
			$qu = $this->db->get('tbl_group_content');
			$data = $qu->result_array();
			return $data;
		}
		public function get_data_by_user($id)
		{
            $this->db->where('gc_id',$id);
            $this->db->select('tbl_group_content.*, tbl_content_table.*')
       				  ->from('tbl_group_content')
        			  ->join('tbl_content_table', 'tbl_group_content.gc_id = tbl_content_table.ct_gc_id','left');
			$qu = $this->db->get();
			$data = $qu->result_array();
			return $data;
		}
		public function edit_data_by_id($id){
			$this->db->where('gc_id',$id);
			$qu = $this->db->get('tbl_group_content');
			$data = $qu->result_array();
			return $data;
		}
		public function update($data,$id){
			$this->db->where('gc_id',$id);
			$result = $this->db->update('tbl_group_content', $data);
			return $result;
		}

		public function save_content($data)
		{
			$qu = $this->db->insert('tbl_content_table',$data);
			return $qu;
		}
		public function tbl_content_fetch()
		{
			$this->db->order_by("ct_message_date", "asc");
			$this->db->select('tbl_group_content.gc_id,tbl_group_content.Gc_name, tbl_content_table.*')
       				  ->from('tbl_content_table')
        			  ->join('tbl_group_content', 'tbl_group_content.gc_id = tbl_content_table.ct_gc_id');
			$qu = $this->db->get();
			$data = $qu->result_array();
			return $data;
		}
		public function delete_message($id)
		{
			$this->db->where('ct_id',$id);
			$data = $this->db->delete('tbl_content_table');
			return $data;
		}

}