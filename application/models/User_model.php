<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $api_url = 'https://fakestoreapi.com/users';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api');
    }

    public function get_total_users()
    {
        $users = $this->make_api_call($this->api_url, 'GET');
        return count($users);
    }

    public function get_filtered_users_count($search)
    {
        $users = $this->make_api_call($this->api_url, 'GET');

        if (empty($search)) {
            return count($users);
        }

        $search = strtolower($search);
        $filtered_users = array_filter($users, function ($user) use ($search) {
            $full_name = strtolower($user['name']['firstname'] . ' ' . $user['name']['lastname']);
            return (
                strpos($full_name, $search) !== false ||
                strpos(strtolower($user['email']), $search) !== false ||
                strpos(strtolower($user['phone']), $search) !== false ||
                strpos(strtolower($user['username']), $search) !== false
            );
        });

        return count($filtered_users);
    }

    public function get_users($start, $length, $search, $order_by, $order_dir)
    {
        $all_users = $this->make_api_call($this->api_url, 'GET');


        if (!empty($search)) {
            $search = strtolower($search);
            $all_users = array_filter($all_users, function ($user) use ($search) {
                $full_name = strtolower($user['name']['firstname'] . ' ' . $user['name']['lastname']);
                return (
                    strpos($full_name, $search) !== false ||
                    strpos(strtolower($user['email']), $search) !== false ||
                    strpos(strtolower($user['phone']), $search) !== false ||
                    strpos(strtolower($user['username']), $search) !== false
                );
            });


            $all_users = array_values($all_users);
        }


        usort($all_users, function ($a, $b) use ($order_by, $order_dir) {
            $result = 0;

            if ($order_by === 'name') {
                $a_val = strtolower($a['name']['firstname'] . ' ' . $a['name']['lastname']);
                $b_val = strtolower($b['name']['firstname'] . ' ' . $b['name']['lastname']);
            } elseif ($order_by === 'id') {
                $a_val = $a['id'];
                $b_val = $b['id'];
            } elseif (isset($a[$order_by]) && isset($b[$order_by])) {
                $a_val = strtolower($a[$order_by]);
                $b_val = strtolower($b[$order_by]);
            } else {
                return 0;
            }

            if ($a_val == $b_val) {
                $result = 0;
            } else {
                $result = ($a_val < $b_val) ? -1 : 1;
            }

            return ($order_dir === 'asc') ? $result : -$result;
        });


        $users = array_slice($all_users, $start, $length);

        $formatted_users = [];
        foreach ($users as $user) {
            $formatted_users[] = [
                'id' => $user['id'],
                'name' => $user['name']['firstname'] . ' ' . $user['name']['lastname'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'username' => $user['username'],
                'actions' => '
                    <button type="button" class="btn btn-sm btn-primary edit-user" data-id="' . $user['id'] . '">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-user" data-id="' . $user['id'] . '" data-name="' . $user['name']['firstname'] . ' ' . $user['name']['lastname'] . '">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                '
            ];
        }

        return $formatted_users;
    }

    private function make_api_call($url, $method, $data = null)
    {
        return make_api_request($url, $method, $data);
    }
}
