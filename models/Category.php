<?php
require_once 'Model.php';

class Category extends Model {
    protected static $table = 'book_categories';

    public $id;
    public $category_name;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public static function all() {
        $result = parent::all();
        return $result ? array_map(fn($data) => new self($data), $result) : [];
    }
}
?>
