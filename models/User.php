<?php
    require_once 'Model.php';
    class User extends Model{
        protected static $table = 'users'; 
        
        public $id;
        public $name;
        public $email;
        public $phone_number; 
        public $password;
        public $role;
        public $account_status; 
        public $created_at;
        public $updated_at;


        public function __construct(array $data = []){
            foreach($data as $key => $value){
                if(property_exists($this, $key)){
                    $this->$key = $value;
                }
            }
            
        }


        public static function all(){
            $result= parent::all();
            return $result ? array_map(fn($data) => new self($data), $result) : null ;
        }


        public static function find($id){
            $result = parent::find($id);
            return $result ? new self($result): null;
        }

        public static function create(array $data){
            $result = parent::create($data);
            return $result ? new self($data) : null;
        }


        public function update(array $data){
            $result = parent::updateById($this->id,$data);
            if($result){
                foreach($data as $key => $value){
                    if(property_exists($this, $key)){
                        $this->$key = $value;
                    }
                }
                return true;
            }else{
                return false;
            }        
        }


        public function save(){
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'password' => $this->password,
                'role' => $this->role,
                'account_status' => $this->account_status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ];
            $this->update($data);
        }

  
        public function delete(){
            $result = parent::deleteById($this->id);
            if($result){
                foreach($this as $key => $value){
                    unset($this->$key);
                }
                return true;
            }else{
                return false;
            }
        }

    
        public static function countUsers(){
            $users = parent::all();
            return $users ? count($users) : 0;   
        }
        
        public static function usersAdded($time){
            $lastAdded = parent::findday($time);
            return $lastAdded ? $lastAdded : 0; 
        }


        public static function findEmail($email) {
            $result = parent::where('email', '=', $email);
            return $result ? new self($result[0]) : null;
        }

        public static function findPhone($email) {
            $result = parent::where('phone_number', '=', $email);
            return $result ? new self($result[0]) : null;
        }

        public static function findByStatus($status) {
            $result = parent::where('account_status', '=', $status);
            return $result ? array_map(fn($data) => new self($data), $result) : [];
        }



        public function deactivate() {
            $data = ['account_status' => 'inactive'];
            $result = $this->update($data); 
            if ($result) {
                $this->account_status = 'inactive'; 
                return true;
            }
            return false;
        }

  
        public function reactivate() {
            $data = ['account_status' => 'active'];
            $result = $this->update($data); 
            if ($result) {
                $this->account_status = 'active'; 
                return true;
            }
            return false;
        }

        public static function findByRole($role) {
            $result = parent::where('role', '=', $role);
            return $result ? array_map(fn($data) => new self($data), $result) : null;
        }

        
    }
?>

