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
    <link href="about_us.html">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
    <!-- A website for the registrar/administration where signing in links all pre-existing files related to them -->
     
    <!-- TO ADD: -->
    <!-- Add an admin with special features (to edit and delete) -->
    <!-- Only show the form whhen they press  the button to add a file -->
    <!-- Add a search function -->
    <!-- // TODO: set up local storage -->
    <!-- // TODO: fix php thingymaajig -->

    <!-- CSS TODO -->
    <!-- Home page will contain header bar - options are to add a file or search a file -->
     
     <nav>
        <ul>
            <li><a href="homepage.php"><img src="bsu_logo.png"  alt="BSU Logo" style="height: 60px;"></a></li>
            <p>The Digital Archives</p>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="about_us.html">About</a></li>
        </ul>
    </nav>
<div class="container">
     <header>
        <h1>WELCOME TO THE DIGITAL ARCHIVES</h1>
        <h2>What would you like to do?</h2>

        <button class="btn" id="showForm">Submit a file</button>
        <button class="btn" id="showSearch">Search a file</button>
     </header>

     <section class="submit-file" id="submitFile" style="display: none;">
        <fieldset>
        <form action="../BackEnd/Controller.php?method_finder=create"  method="post">
            <label for="last_name">Last Name: </label> 
            <input type="text" name="last_name" id="lastName"> 
    
            <label for="first_name">First Name: </label> 
            <input type="text" name="first_name" id="firstName"> 
    
            <label for="file_name">File Name: </label> 
            <input type="text" name="file_name" id="fileName"> 

            <label for="date_issued">Date Issued: </label> 
            <input type="text" name="date_issued" id="fileName"> 
        <input class="btn" type="submit" value="Submit" name="submit_button">
        </form>
        </fieldset>
    </section>

    <section  id="searchBar" style="display: none;">
        <form action="search_results.php" method="get" style="display: inline;">
            <input type="text" name="query" placeholder="Search files..." required>
            <button type="submit">Search</button>
        </form>
    </section>
</div>

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
                <td>

                    <!--update-->
                    <form action="../BackEnd/Controller.php?" method="get" style="display:inline;">
                        <input type="hidden" name="method_finder" value="edit">
                        <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                    <button type="submit">EDIT</button>
                    </form>

                    <!--delete-->
                    <form action="../BackEnd/Controller.php?" method="get" style="display:inline;">
                        <input type="hidden" name="method_finder" value="delete">
                        <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                        <button type="submit">DELETE</button>
                    </form>
                </td>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <script src="users.js"></script>
    <script>
        const firstName;
        const lastName;
        const fileName;
        const dateIssued;
    </script>
</body>
<script>
    // Javascript to show and hide the form
        const showForm = document.getElementById("showForm");
        const submitFile = document.getElementById("submitFile");

        showForm.addEventListener("click", () => {
            if (submitFile.style.display === "none") {
                submitFile.style.display = "block";
            } else {
                submitFile.style.display = "none";
            }
        });

    // Javascript to show and hide the search Bar
        const showSearch = document.getElementById("showSearch");
        const searchBar = document.getElementById("searchBar");

        showSearch.addEventListener("click", () => {
            if (searchBar.style.display === "none") {
                searchBar.style.display = "block";
            } else {
                searchBar.style.display = "none";
            }
        });
</script>
</html>