<?php
    require_once 'Model.php';
    class Book extends Model{
        protected static $table='books'; // Define the table name
        public $id;
        public $sku;
        public $title;
        public $author;
        public $genre;
        public $year_published;
        public $price;
        public $currency;
        public $stock;
        public $created_at;

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

        //save is used para ma update sa database ung value
        public function save(){
            $data = [
                'sku'=>$this->sku,
                'title'=>$this->title,
                'author'=>$this->author,
                'genre'=>$this->genre,
                'year_published'=>$this->year_published,
                'price'=>$this->price,
                'currency'=>$this->currency,
                'stock'=>$this->stock,
                'created_at'=>$this->created_at,
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

        public static function countBooks(){
            $books = parent::all();
            return $books ? count($books) : 0;
        }

        public static function booksAdded($time){
            $lastAdded = parent::findday($time);
            return $lastAdded ? $lastAdded : 0; 
        }

        public static function booksAddedByYear($year_published) {
        try{
            $sql = "SELECT * FROM " . static::$table . " WHERE year_published = :year_published";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':year_published', $year_published, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(); 

            return $result ? array_map(fn($data) => new self($data), $result) : []; 
        }catch (PDOException $e) {
            die("Error fetching data: " . $e->getMessage());
        }
    }

    }

?>
