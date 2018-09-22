<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signform extends CI_Controller {
	function __construct(){
		parent:: __construct();
		//$this->load->database();
		$this->load->model('form');
		$this->load->library('form_validation');
		$this->load->driver('cache', array('adapter' => 'file'));
		//$this->load->helper('url');
	}
	public function index(){
		$this->signup();
	}
	
	public function signup(){
		$this->load->view('signup_form');
	}
	
	public function load(){
		//$data = $this->form->selectData();
		//$data = $this->form->selectUserData(1);
		//pr($data);
		$data = $this->form->selectWedsData(11);
		pr($data);
		//$this->load->view('list', array('data'=>$data));
		//$this->load->view('signup_form', array('data'=>$data));
	}
	
	public function usersList(){
		$data = $this->form->getAllUsers();
		//pr($data);
		$this->load->view('users_list', array('data' => $data));
	}
	
	public function check_password($pwd){
		if($pwd == '123456')
			return false;
		else	
			return true;
	}
	public function editSignUp($iduser = 0){
		//$iduser = $this->input->get('id');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules('Name', 'Name', 'required', array('required' => 'Please enter name'));
		$this->form_validation->set_rules('Address', 'Address', 'required', array('required' => 'Please enter address'));
		$this->form_validation->set_rules('confPassword', 'Confirm Password', 'required', array('required' => 'Please enter Confirm Password'));
		$this->form_validation->set_rules('Password', 'Password', 'required|callback_check_password|matches[confPassword]', 
			array(
				'required' => 'Please enter password',
				'check_password' => 'Password should be strong'
			)
		);
		$this->form_validation->set_rules('dateOfBirth', 'Dateof Birth', 'required', array('required' => 'Please enter date of birth'));
		
		$this->form_validation->set_rules('primaryContact', 'Contact', 'required|is_unique[wg_users.contact2]', 
			array('required' => 'Please enter Contact no.')
		);
		
		$this->form_validation->set_rules('Email', 'Email Address', 'required|valid_email|is_unique[wg_users.email]', 
			array('required' => 'Please enter Email', 'valid_email' => 'Email should be valid')
		);
		if ($this->form_validation->run() == TRUE){
			//echo 'Validation Done';
			
			$data = array(
				'firstname' => $this->input->post('Name'),
				'email' => $this->input->post('Email'),
				'password' => $this->input->post('Password'),
				'address' => $this->input->post('Address'),
				'born' => $this->input->post('dateOfBirth'),
				'contact1' => $this->input->post('primaryContact'),
			);
			//pr($data);
			$id = $this->input->post('iduser');
			$this->form->saveSignUp($id, $data);
			//redirect('signform/usersList');
			
		} else {
			//return false;
		}
		
		$user_data = $this->form->get_user_by_iduser($iduser);
		//pr($user_data);
		$this->load->view('user_signupForm', array('data' => $user_data));
	}
	
	public function userSignupUpdate(){
		$data = array(
			'firstname' => $this->input->post('Name'),
			'email' => $this->input->post('Email'),
			'password' => $this->input->post('Password'),
			'address' => $this->input->post('Address'),
			'born' => $this->input->post('dateOfBirth'),
			'contact1' => $this->input->post('primaryContact'),
		);
		$iduser = $this->input->post('iduser');
		$this->form->saveSignUp($iduser, $data);
		redirect('signform/usersList');
	}
	
	public function delete_signup($iduser){
		$this->form->delete_user_by_iduser($iduser);
		$this->session->set_flashdata('success', 'Delete Record Sucessfully!');
		redirect('signform/usersList');
	}
	
	public function save(){
		$data = array(
			'name' => $this->input->post('Name'),
			'email' => $this->input->post('Email'),
			'password' => $this->input->post('Password')
		);
		$this->form->save($data);
		$this->session->set_flashdata('success', 'Data Sucessfully Inserted!');
		$this->session->set_flashdata('display', 'Congratulation!');
		redirect('signform/load');
	}
	
	public function save_form(){
		$dataform = array(
			'name' => $this->input->post('Name'),
			'email' => $this->input->post('Email'),
			'address' => $this->input->post('Address')
		);
		
		$this->form->save_form($dataform);
		$this->session->set_flashdata('success', 'Data successfully added!');
		redirect('signform/load');
	}
	
	public function file_form(){
		$this->form_validation->set_rules('picture', 'File', 'trim');
		if ($this->form_validation->run() == TRUE){
			$config['upload_path']          = './uploads/';
			$config['allowed_types']        = 'gif|jpg|png|pdf';
			$config['overwrite']        	= true;
			//$config['encrypt_name']        = true;
			$config['remove_spaces']         = true;
			//$config['max_size']             = 9000;
			//$config['max_width']            = 1024;
			//$config['max_height']           = 768;

			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('picture'))
			{
					//$error = array('error' => $this->upload->display_errors());
					pr($this->upload->display_errors());
			}
			else
			{
					pr($this->upload->data());
					//$data = array('upload_data' => $this->upload->data());

					//$this->load->view('upload_success', $data);
			}
		}	
		$this->load->view('file_form');
	}
	
	public function file_upload(){
		$this->load->view('file_upload');
	}
	
	public function show_filedata(){
			$config['upload_path']          = './uploads/';
			$config['allowed_types']        = 'gif|jpg|png|pdf|mp4|avi';
			$config['overwrite']        	= true;
			$config['remove_spaces']        = true;
			
			$this->load->library('upload', $config);
			//$this->upload->do_upload('picture');
		if($this->upload->do_upload('picture')){
			pr($this->upload->data());
			$data = $this->upload->data();
			echo "Congratulation! ".$this->input->post('firstname');
			//echo '  <img src="'.site_url().'uploads/'.$data['file_name'].'" width="100" height="200">';
			//echo '<video src="'.site_url().'uploads/'.$data['file_name'].'" width="300" height="200"></video>';
			echo ' <video poster="'.site_url().'uploads/web_copy.png'.'" width="320" height="240" controls autoplay>
				  <source src="'.site_url().'uploads/'.$data['file_name'].'" type="video/mp4">
				  <source src="'.site_url().'uploads/'.$data['file_name'].'" type="video/ogg">
				  Your browser does not support the video tag.
				</video> ';
		} else {
			pr($this->upload->display_errors());
		}
	}
	
	public function amuj(){
		$this->load->view('register_form');
	}
}
