<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper(['url', 'form', 'api']);
        $this->load->library(['form_validation']);
    }

    public function index()
    {
        $this->load->view('templates/header');
        $this->load->view('users/index');
        $this->load->view('users/add_modal');
        $this->load->view('users/edit_modal');
        $this->load->view('templates/footer');
    }

    public function get_users()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $draw = $this->input->get('draw');
        $start = $this->input->get('start');
        $length = $this->input->get('length');
        $search = $this->input->get('search')['value'];
        $order_column = $this->input->get('order')[0]['column'];
        $order_dir = $this->input->get('order')[0]['dir'];

        $columns = ['id', 'name', 'email', 'phone', 'username'];
        $order_by = $columns[$order_column];

        $total_count = $this->User_model->get_total_users();
        $filtered_count = $this->User_model->get_filtered_users_count($search);

        $users = $this->User_model->get_users($start, $length, $search, $order_by, $order_dir);

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $total_count,
            'recordsFiltered' => $filtered_count,
            'data' => $users
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function get_user($id)
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $user = $this->User_model->get_user_by_id($id);

        if ($user) {
            $response = [
                'status' => 'success',
                'user' => $user
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'User not found'
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function add_user()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|trim');

        if ($this->form_validation->run() === FALSE) {
            $response = [
                'status' => 'error',
                'message' => validation_errors()
            ];
        } else {
            $user_data = [
                'email' => $this->input->post('email'),
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'name' => [
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname')
                ],
                'phone' => $this->input->post('phone')
            ];

            $result = $this->User_model->add_user($user_data);

            if ($result) {
                $response = [
                    'status' => 'success',
                    'message' => 'User added successfully',
                    'user' => $result
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to add user'
                ];
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function update_user()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $user_id = $this->input->post('user_id');

        $this->form_validation->set_rules('firstname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $response = [
                'status' => 'error',
                'message' => validation_errors()
            ];
        } else {
            $user_data = [
                'email' => $this->input->post('email'),
                'username' => $this->input->post('username'),
                'name' => [
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname')
                ],
                'phone' => $this->input->post('phone')
            ];

            $result = $this->User_model->update_user($user_id, $user_data);

            if ($result) {
                $response = [
                    'status' => 'success',
                    'message' => 'User updated successfully',
                    'user' => $result
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to update user'
                ];
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
