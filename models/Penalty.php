<?php
require_once 'Model.php';

class Penalty extends Model {
    protected static $table = 'penalties';

    public $id;
    public $user_id;
    public $transaction_id;
    public $penalty_amount;
    public $penalty_date;

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



    public static function recordPenalty(array $data) {
        $result = parent::create($data);
        return $result ? new self($data) : null;
    }

    public static function getPenalties($userId) {
        $result = parent::where('user_id', '=', $userId);
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }

}
?>
