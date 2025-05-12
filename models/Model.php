<?php
    class Model{
        //encapsulation
        protected static $conn; // Database connection
        
        protected static $table; 

     
        public static function setConnection($conn){
            self::$conn=$conn;
        }

        // select all
        public static function all(){
            try{
                $sql="SELECT * FROM ".static::$table; // SQL query to select all rows
                $result= self::$conn->query($sql); // access query method and pass sql
                
                $rows = $result->fetchAll(); // PDO fetches result 
                return count($rows)>0 ? $rows : null; // Return rows if may laman

            }catch(PDOException $e){
                die("Error fetching data". $e->getMessage());
            }
        }

        // Find row by id
        public static function find($id){
            try{
                $sql="SELECT * from ". static::$table." WHERE id=:id"; // SQL query with placeholder
                $stmt=self::$conn->prepare($sql); // PDO prepare statement
                $stmt->bindParam(':id',$id); // PDO bind value to the placeholder
                $stmt->execute(); // PDO execute prepared statement
                return $stmt ->fetch() ?? 0;
            }catch(PDOException $e){
                die("Error fetching data". $e->getMessage()); 
            }
        }

        // Create a new row in the table
        public static function create (array $data){
            try{
                $columns= implode(",", array_keys($data)); // take column names from data array and joins them seperated by comma
                $values = implode(",", array_map(fn($key)=>":$key", array_keys($data))); // placeholders
                $sql="INSERT INTO ". static::$table . " ($columns) VALUES ($values)"; // SQL insert query

                $stmt= self::$conn->prepare($sql); 
                foreach($data as $key=>$value){
                    $stmt->bindValue(":$key", $value); // PDO bind value to the placeholder
                }

                $stmt->execute(); // PDO execute prepared statement
                $id=self::$conn->lastInsertID(); // Get last inserted ID since auto increment ang id

                return self::find($id); // Return the created row

            }catch(PDOException $e){
                die("Error fetching data". $e->getMessage());
            }
        }
        
        // Update update row by id
        public static function updateById($id, array $data){
            try{
                $set = implode(", ", array_map(fn($key)=>"$key = :$key", array_keys($data))); 
                $sql = "UPDATE ". static::$table . " SET $set where id=:id"; // SQL update query

                $stmt=self::$conn->prepare($sql);
                foreach($data as $key=>$value){
                    $stmt->bindValue(":$key", $value);
                }

                $stmt->bindValue(':id', $id);

                $stmt->execute(); 
                return self::find($id); // Return the updated row

            }catch(PDOException $e){ 
                die("Error fetching data". $e->getMessage());
            }
        }

        // Delete row by id
        public static function deleteById($id){
            try{
                $sql = "DELETE FROM ". static::$table . " WHERE id =:id"; // SQL delete query
                $stmt=self::$conn->prepare($sql);
                $stmt->bindValue(':id', $id);

                $stmt->execute(); // PDO execute prepared statement
                return $stmt->execute(); // Return execution result

            }catch(PDOException $e){
                die("Error fetching data". $e->getMessage());
            }
        }

        // Count row by day
        public static function findday($day) {
            try {
                $sql = "SELECT COUNT(*) AS count FROM " . static::$table . " WHERE created_at >= :day"; // SQL query
                $stmt = self::$conn->prepare($sql);
                $stmt->bindValue(':day', $day); // PDO Bind day parameter
                $stmt->execute(); // PDO execute prepared statement
                $row = $stmt->fetch(); 
        
                return $row['count'] ?? 0; 
            } catch (PDOException $e) {
                die("Error fetching data: " . $e->getMessage());
            }
        }

    }