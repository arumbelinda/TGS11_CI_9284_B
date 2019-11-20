<?php
use Restserver\Libraries\REST_Controller ;
Class Vehicle extends REST_Controller{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");
        
    parent::__construct();
        $this->load->model('VehicleModel');
        $this->load->library('form_validation');
        $this->load->helper(['jwt', 'authorization']); 
        }
    public function index_get()
    {
        $data = $this->verify_data();
        if($data)
        {
            return $this->returnData($this->db->get('vehicles')->result(), false);
        }
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
       
    }
    public function index_post($id = null)
    {

        $data = $this->verify_data();
        if($data)
        {
            $validation = $this->form_validation;
            $rule = $this->VehicleModel->rules();
        
            if($id == null)
            {
            array_push($rule,[
            'field' => 'merk',
            'label' => 'merk',
            'rules' => 'required'
            ],
            [
            'field' => 'type',
            'label' => 'type',
            'rules' => 'required'
            ],
            [
            'field' => 'licensePlate',
            'label' => 'licensePlate',
            'rules' => 'required|is_unique[vehicles.licensePlate]'
            ]
            );
            }
            else{
                    array_push($rule,
                    [
                        'field' => 'licensePlate',
                        'label' => 'licensePlate',
                        'rules' => 'required'
                    ],
                    [
                        'field' => 'created_at',
                        'label' => 'created_at',
                        'rules' => 'required'
                    ],
                    );
            }

            $validation->set_rules($rule);
            if (!$validation->run())
            {
                return $this->returnData($this->form_validation->error_array(), true);
            }
            date_default_timezone_get();
            $vehicle = new VehicleData();
            $vehicle->merk = $this->post('merk');
            $vehicle->type = $this->post('type');
            $vehicle->licensePlate = $this->post('licensePlate');
            $date = new DateTime();
            $vehicle->created_at = $date;
            if($id == null)
            {
                $response = $this->VehicleModel->store($vehicle);
            }
            else{
                $response = $this->VehicleModel->update($vehicle,$id);
            }
            return $this->returnData($response['msg'], $response['error']);
        }
        
        
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
    }    

    public function index_delete($id = null)
    {
        $data = $this->verify_data();

        if($data)
        {
            if ($id == null)
            {
                return $this->returnData('Parameter Id Tidak Ditemukan', true);
            }
            $response = $this->VehicleModel->destroy($id);
            return $this->returnData($response['msg'], $response['error']);
        }
        else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $this->response($response);
        }
       }
       public function returnData($msg, $error)
       {
           $response['error'] = $error;
           $response['message'] = $msg;
           return $this->response($response);
       }
   
       public function verify_data()
       {
           $headers = $this->input->request_headers();
   
           if(!empty($headers['Authorization']))
           {
               $token = $headers['Authorization'];
           }
           else
           {
               return false;
           }
   
   
           try {
           // Validate the token
           // Successfull validation will return the decoded user data else returns false
               $data = AUTHORIZATION::validateToken($token);
               $data2 = AUTHORIZATION::validateTimestamp($token);
   
               if ($data === false || $data2 === false) {
                   $status = parent::HTTP_UNAUTHORIZED;
                   $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                   return false;
               } 
               else 
               {
                   return $data;
               }
           }
           catch (Exception $e) 
           {
               // Token is invalid
               // Send the unathorized access message
               $status = parent::HTTP_UNAUTHORIZED;
               $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
               return false;
           }
       }
   }   
    Class VehicleData{
        public $merk;
        public $type;
        public $licensePlate;
        public $created_at;
       }   