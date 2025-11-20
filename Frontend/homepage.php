<?php 
    include "../BackEnd/Controller.php";

    $connect = new Controller();
    $connect->connection();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- A website for the registrar/administration where signing in links all pre-existing files related to them -->
     
    <!-- TO ADD: -->
    <!-- Add an admin with special features (to edit and delete) -->
    <!-- Add an ABout Us Section -->
    <!-- Only show the form whhen they press  the button to add a file -->
    <!-- Add a search function -->
    <!-- // TODO: set up local storage -->
    <!-- // TODO: fix php thingymaajig -->

    <!-- CSS TODO -->
    <!-- Home page will contain header bar - options are to add a file or search a file -->
     
    <!-- TESTING???? -->
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
        <?php
        include "../Assets/Components/table.php";
        ?>
    </table>

    <script src="users.js"></script>
    <script>
        const firstName;
        const lastName;
        const fileName;
        const dateIssued;

    </script>
</body>
</html>