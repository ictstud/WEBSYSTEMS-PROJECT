<?php 
    include "../BackEnd/Controller.php";

    // $connect = new Controller();
    // $connect->connection();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
     <header>
        <h1>Welcome to the Digital Archives</h1>
        <h2>What would you like to do?</h2>

        <button>Submit a file</button>
        <button>Search a file</button>
     </header>

     <section class="submit-file">
        <form action="../BackEnd/Controller.php?method_finder=create"  method="post">
        <fieldset>
            <label for="last_name">Last Name: </label>
            <input type="text" name="last_name" id="lastName">
    
            <label for="first_name">First Name: </label>
            <input type="text" name="first_name" id="firstName">
    
            <label for="file_name">File Name: </label>
            <input type="text" name="file_name" id="fileName">

            <label for="date_issued">Date Issued: </label>
            <input type="text" name="date_issued" id="fileName">
        </fieldset>

        <input type="submit" value="submit" name="submit_button">
    </form>
     </section>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>File Name</th>
                <th>Date Issued</th>
            </tr>
            
        </thead>
        <tbody>
            <?php
                $controller = new Controller();
                $users = $controller->readall();

                foreach($users as $user):   
            ?>
            <tr>
              <td><?=htmlspecialchars($user['ID'])?></td>
              <td><?=htmlspecialchars($user['last_name'])?></td>
              <td><?=htmlspecialchars($user['first_name'])?></td>
              <td><?=htmlspecialchars($user['file_name'])?></td>
              <td><?=htmlspecialchars($user['date_issued'])?></td>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <script src="users.js"></script>

</body>
</html>