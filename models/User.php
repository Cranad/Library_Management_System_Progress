<?php
    require_once 'Model.php';
    class User extends Model{
        protected static $table = 'users'; // Define the table name
        
        public $id;
        public $first_name;
        public $last_name;
        public $email;
        public $password;
        public $role;
        public $status;
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
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
                'status' => $this->status,
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

        public static function findEmail($email){
            try {
                $sql = "SELECT * FROM " . static::$table . " WHERE email = :email";
                $stmt = self::$conn->prepare($sql);
                $stmt->bindValue(':email', $email); 
                $stmt->execute();
                $result = $stmt->fetch(); 

                return $result ? new self($result) : null; 
            } catch (PDOException $e) {
                die("Error fetching data: " . $e->getMessage());
            }
        }

        public static function usersAdded($time){
            $lastAdded = parent::findday($time);
            return $lastAdded ? $lastAdded : 0; 
        }

        // num of user based on status
        public static function userStatus($status){
            try {
                $sql = "SELECT COUNT(*) AS count FROM " . static::$table . " WHERE status = :status";
                $stmt = self::$conn->prepare($sql); 
                $stmt->bindValue(':status', $status); 
                $stmt->execute(); 
                $row = $stmt->fetch(); 

                return $row['count'] ?? 0; 
            } catch (PDOException $e) {
                die("Error fetching data: " . $e->getMessage());
            }
        }

        // Find user by email
        public static function findByEmail($email) {
            try {
                $sql = "SELECT * FROM " . static::$table . " WHERE email = :email"; 
                $stmt = self::$conn->prepare($sql); 
                $stmt->bindValue(':email', $email);
                $stmt->execute(); 
                $row = $stmt->fetch(); 

                return $row ?? null;
            } catch (PDOException $e) {
                die("Error fetching data: " . $e->getMessage()); 
            }
        }

        public static function findByStatus($status) {
            try {
                $sql = "SELECT * FROM " . static::$table . " WHERE status = :status"; 
                $stmt = self::$conn->prepare($sql); 
                $stmt->bindValue(':status', $status);
                $stmt->execute(); 
                $result = $stmt->fetchAll(); 

                return $result ? array_map(fn($data) => new self($data), $result) : []; 
            } catch (PDOException $e) {
                die("Error fetching data: " . $e->getMessage()); 
            }
        }
    }
?>

