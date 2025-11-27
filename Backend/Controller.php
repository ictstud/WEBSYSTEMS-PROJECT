<?php
class Controller {

    private $connection;

    public function __construct(){
        $this->connection = $this->connection();
    }

    public function readall() {
        $sql = "SELECT * FROM files_table";

        $stmt = $this->connection->prepare($sql);
        $stmt ->execute();

        $result = $stmt ->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function connection(){
        //connection logic

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
            define('DB_NAME','test');
        }

        $this->connection=new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($this->connection->connect_error){
            die("connection faiiled:".$this->connection->connect_error);
        }
        echo"<script>console.log('There was a connection');</script>";
        return $this->connection;
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

    public function update() {
        $id = $_POST['ID'];
        $last_name = $_POST['new_last_name'];
        $first_name = $_POST['new_first_name'];
        $file_name = $_POST['new_file_name'];
        $date_issued = $_POST['new_date_issued'];

        echo $last_name, $first_name, $file_name, $date_issued;

        if($id){
            $sql = "UPDATE files_table SET first_name=?, last_name=?, file_name=?, date_issued=? WHERE ID = ?";
            $stmt = $this->connection->prepare($sql);

            if($stmt){
                $stmt->bind_param("ssiss", $first_name, $last_name, $id, $file_name, $date_issued);

                if($stmt->execute()){
                    $location="../Frontend/homepage.php";
                    header("location:$location");
                }
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

    public function create() {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $file_name = $_POST['file_name'];
        $date_issued = $_POST['date_issued'];

        $sql = "INSERT INTO files_table (last_name, first_name, file_name, date_issued) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ssss", $last_name, $first_name, $file_name, $date_issued);

        if ($stmt->execute()){
            header("Location: ../Frontend/homepage.php?success=1");
            exit();
        } else {
            echo "Error creating user: " . $stmt->error;
        }
    }
}
    

$controller = new Controller();
$controller -> actionreader();
?>