<?php 
use Restserver \Libraries\REST_Controller ; 
Class Branches extends REST_Controller{
    
    public function __construct(){     
        header('Access-Control-Allow-Origin: *');         
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");         
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");         
        parent::__construct();         
        $this->load->model('BranchesModel');         
        $this->load->library('form_validation');     
    }     
    
    public function index_get(){         
        return $this->returnData($this->db->get('branches')->result(), false);     
    }     
    
    public function index_post($id = null){         
        $validation = $this->form_validation;         
        $rule = $this->BranchesModel->rules();
        if($id == null){
        array_push($rule,[                     
            'field' => 'name',                     
            'label' => 'name',                     
            'rules' => 'required'                 
        ],  
        [                     
            'field' => 'address',                     
            'label' => 'address',                     
            'rules' => 'required'                 
        ], 
        [                     
            'field' => 'phoneNumber',                     
            'label' => 'phoneNumber',                     
            'rules' => 'required|is_unique[branches.phoneNumber]'                 
        ]    
        );
        }else
        {
            array_push($rule,[                     
                'field' => 'phoneNumber',                     
                'label' => 'phoneNumber',                     
                'rules' => 'required'                 
            ]);
        }            
        $validation->set_rules($rule);         
        if (!$validation->run()) {             
            return $this->returnData($this->form_validation->error_array(), true);         
        }         
        $branche = new BranchesData();         
        $branche->name = $this->post('name');         
        $branche->address = $this->post('address');           
        $branche->phoneNumber = $this->post('phoneNumber');         
        if($id == null){             
            $response = $this->BranchesModel->store($branche); 
        }else{             
            $response = $this->BranchesModel->update($branche,$id);         
        }         
        return $this->returnData($response['msg'], $response['error']);     
    }    

    public function index_delete($id = null){
        if($id == null){             
            return $this->returnData('Parameter Id Tidak Ditemukan', true);         
        }         
        $response = $this->BranchesModel->destroy($id);         
        return $this->returnData($response['msg'], $response['error']);     
    }     
            
    public function returnData($msg,$error){         
        $response['error']=$error;         
        $response['message']=$msg;         
        return $this->response($response);     
    } 
} 

Class BranchesData{     
    public $name;      
    public $address; 
    public $phoneNumber; 
} 
 