<?php
require_once 'Model.php';

class Book extends Model { 
    protected static $table = 'books';

    public $id;
    public $title; 
    public $author;
    public $category; 
    public $published_year;
    public $total_copies; 
    public $available_copies; 
    public $created_at;
    public $updated_at; 

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
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

    public function save() {
        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'category' => $this->category,
            'published_year' => $this->published_year,
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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

    public static function booksAddedByYear($published_year) {
        $result = parent::where('published_year', '=', $published_year);
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function getAvailableBooks() {
        $result = parent::where('available_copies', '>', 0); // Use 'available_copies' instead of 'status'
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function findByCategory($categoryId) {
        $result = parent::where('category', '=', $categoryId);
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function CategoryName($categoryId) {
        try {
            $sql = "SELECT * FROM book_categories WHERE id = :category_id";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':category_id', $categoryId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row['category_name'] ?? 'Unknown';
        } catch (PDOException $e) {
            die("Error fetching category name: " . $e->getMessage());
        }
    }

}
?>
