<?php
class Controller {

    private $connection;

    // public function __construct(){
    //     $this->connection = $this->connection();
    // }

    //added by chatgpt
    public function __construct(){
        $this->connection = $this->connectDB();
    }   


    public function readall() {
        $sql = "SELECT * FROM files_table";

        $stmt = $this->connection->prepare($sql);

        // to know what the error is - by chatgpt
        if (!$stmt) {
            die("Prepare failed: " . $this->connection->error);
        }

        $stmt ->execute();

        $result = $stmt ->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
  
    //connection logic - 'connection' renamed to 'connectDB'
    public function connectDB(){
        if(!defined('DB_HOST')){
            define('DB_HOST','localhost');
        }
        if(!defined('DB_USER')){
            define('DB_USER','root');
        }
        if(!defined('DB_PASS')){
            define('DB_PASS','');
        }
        if(!defined('DB_NAME')){
            define('DB_NAME','database');
        }

        //old code:
        // $this->connection=new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // if($this->connection->connect_error){
        //     die("connection failed:".$this->connection->connect_error);
        // }
        // echo"<script>console.log('There was a connection');</script>";
        // return $this->connection;
        
        //suggested by chatgpt to fix our code daw
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    //create method finder
    public function actionreader() {
        if(isset($_GET['method_finder'])) {
            $action = $_GET['method_finder'];
            if ($action === 'create') {
                $this ->create ();
            }
            elseif($action === 'delete'){
                $this->delete();
            }
            elseif($action === 'edit'){
                $this->edit();
            }
            elseif($action === 'update'){
                $this->update();
            }
        }
    } 

        // UPDATE (changes)
        public function update() {
        $id = $_POST['ID'];
        $last_name = $_POST['new_last_name'];
        $first_name = $_POST['new_first_name'];
        $file_name = $_POST['new_file_name'];

        // convert date
        $date_raw = $_POST['new_date_issued'];
        $date_issued = $this->convertToMMDDYYYY($date_raw);

        if ($date_issued === null) {
            die("Invalid date format.");
        }

        $sql = "UPDATE files_table SET first_name=?, last_name=?, file_name=?, date_issued=? WHERE ID = ?";
        $stmt = $this->connection->prepare($sql);

        if($stmt){
            $stmt->bind_param("ssssi", $first_name, $last_name, $file_name, $date_issued, $id);
            if($stmt->execute()){
                header("location: ../Frontend/homepage.php");
            }
        }
    }


    public function delete(){
       if(isset($_GET['ID'])){ //check the id
        //do the query
        $id = intval($_GET['ID']);
        $stmt = $this->connection->prepare("DELETE FROM files_table WHERE ID = ?");

        if($stmt){ //if stmt is correct??
            $stmt->bind_param("i", $id);
            if( $stmt->execute()){
                $location="../Frontend/homepage.php"; //head to our frontpage
                header("location: $location");
            }
        }
       }
    }

    public function edit() {
        $id = $_GET['ID'];
        $conversions = (int) $id;
        
        if($conversions != null) {
            $location = "../Frontend/update_page.php?ID=" .urlencode($conversions);
            header("location: $location");
        }else {
            echo "The ID does not exist";
        }
    }

    public function update_take_data($id){
        $query = "SELECT * FROM files_table WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        if($stmt){
            $stmt->bind_param("i",$id);
            //if na execute
            if($stmt->execute()){
                $result=$stmt->get_result();
                return $result->fetch_assoc();
            }
            else {
                echo "There is an error in the execution";
            }
        }
        else {
            echo "The statement is not correct";
        }
    }

    // CREATE (changes)
    public function create() {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $file_name = $_POST['file_name'];

        // SINGLE clean conversion function
        $formattedDate = $this->convertToMMDDYYYY($_POST['date_issued']);

        if ($formattedDate === null) {
            die("Invalid date format. Please enter a valid date.");
        }

        $sql = "INSERT INTO files_table (last_name, first_name, file_name, date_issued)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ssss", $last_name, $first_name, $file_name, $formattedDate);

        if ($stmt->execute()) {
            header("Location: ../Frontend/homepage.php?success=1");
            exit();
        } else {
            echo "Error creating record: " . $stmt->error;
        }
    }

    // SORT FOR TABLE
    public function readall_sorted_by_date() {
        $sql = "SELECT * FROM files_table ORDER BY STR_TO_DATE(date_issued, '%m/%d/%Y') ASC"; 
        // gets from files_table and sorts them by the date issues
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        // makes connection to database to run the query and execute it
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
        // takes what query returns and returns all rows as array
    }

    private function convertToMMDDYYYY($dateString) {
        $timestamp = strtotime($dateString);

        if ($timestamp === false) {
            return null; 
        }

        return date("m/d/Y", $timestamp); 
    }

}


$controller = new Controller();
$controller -> actionreader();
?>