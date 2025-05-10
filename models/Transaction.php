<?php
require_once 'Model.php';
require_once 'User.php';
require_once 'Book.php';

class Transaction extends Model {
    protected static $table = 'transaction'; 

    public $id;
    public $user_id;
    public $book_id;
    public $borrow_date;
    public $due_date;
    public $return_date;
    public $status; 
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

    public static function getOverdue() {
        $result = parent::where('status', '=', 'overdue');
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function getUserId($userId) {
        $result = parent::where('user_id', '=', $userId);
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function getBookId($bookId) {
        $result = parent::where('book_id', '=', $bookId);
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function getBorrowedBooks() {
        $result = parent::where('status', '=', 'borrowed');
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

    public static function getOverdueReturns() {
        $result = parent::where('status', '=', 'overdue');
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }



    public static function borrowBook($userId, $bookId, $borrowDate, $dueDate) {
        try {
            $data = [
                'user_id' => $userId,
                'book_id' => $bookId,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'status' => 'borrowed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $transaction = self::create($data);
            
            $sql = "UPDATE books SET available_copies = available_copies - 1 WHERE id = :book_id AND available_copies > 0";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':book_id', $bookId);
            $stmt->execute();

            return $transaction;
        } catch (PDOException $e) {
            die("Error borrowing book: " . $e->getMessage());
        }
    }

    // Return a book
    public static function returnBook($transactionId, $returnDate) {
        try {

            $sql = "UPDATE " . static::$table . " 
                    SET return_date = :return_date, status = 'returned', updated_at = NOW() 
                    WHERE id = :transaction_id AND (status = 'borrowed' OR status = 'overdue')";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':return_date', $returnDate);
            $stmt->bindValue(':transaction_id', $transactionId);
            $stmt->execute();


            $sql_book = "SELECT book_id FROM " . static::$table . " WHERE id = :transaction_id";
            $stmt_book = self::$conn->prepare($sql_book);
            $stmt_book->bindValue(':transaction_id', $transactionId);
            $stmt_book->execute();
            $bookId = $stmt_book->fetchColumn();

            $sql_update = "UPDATE books SET available_copies = available_copies + 1 WHERE id = :book_id";
            $stmt_update = self::$conn->prepare($sql_update);
            $stmt_update->bindValue(':book_id', $bookId);
            $stmt_update->execute();

            return true;
        } catch (PDOException $e) {
            die("Error returning book: " . $e->getMessage());
        }
    }


    public static function calculatePenalty($transactionId) {
        try {
            $sql = "SELECT due_date FROM " . static::$table . " 
                    WHERE id = :transaction_id AND status = 'overdue'";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':transaction_id', $transactionId);
            $stmt->execute();
            $row = $stmt->fetch();

            if (!$row) {
                return 0;
            }

            $dueDate = strtotime($row['due_date']);
            $currentDate = strtotime(date('Y-m-d H:i:s'));
            $overdue = ($currentDate - $dueDate) / (60 * 60 * 24); 

            return $overdue > 0 ? floor($overdue) * 50 : 0;
        } catch (PDOException $e) {
            die("Error calculating penalty: " . $e->getMessage());
        }
    }

    public static function updateOverdue($currentDate) {
        try {
            $sql = "UPDATE " . static::$table . " 
                    SET status = 'overdue' WHERE status = 'borrowed' AND due_date < :current_date";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(':current_date', $currentDate);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Error detecting overdue transactions: " . $e->getMessage());
        }
    }

}
?>
