<?php 
    include "../BackEnd/Controller.php";

    // $connect = new Controller();
    // $connect->connection(); - gpt said to remove this since were already connected to the constructor
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="about_us.html">
    <link rel="stylesheet" href="styles.css">
    <title>FileStacker | A Digital Archive</title>
</head>
<body>
    <!-- A website for the registrar/administration where signing in links all pre-existing files related to them -->
     
    <!-- TO ADD: -->
    <!-- Add an admin with special features (to edit and delete) -->
    <!-- Make search bar functionable -->
    <!-- TODO: set up local storage -->
     
<nav class="navbar">
  <div class="navbar-left">
    <img src="Images/bsu_logo.png" alt="Logo" class="logo"/>
    <span class="site-title">FileStacker | A Digital Archive</span>
  </div>
  <div class="navbar-right">
    <a href="homepage.php">Home</a>
    <a href="about_us.html">About</a>
  </div>
</nav>

<div class="container">
     <header>
        <h1>Welcome to FileStacker!</h1>
        <hr>
        <h2>What would you like to do?</h2>
        <button class="btn" id="showForm">Submit a file</button>
        <button class="btn" id="showSearch">Search a file</button>
     </header>

     <!-- Form -->
     <section class="submit-file" id="submitFile" style="display: none;">
        <form action="../BackEnd/Controller.php?method_finder=create"  method="post">
            <div class="form-row">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>

            <div class="form-row">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>

            <div class="form-row">
                <label for="file_name">File Name:</label>
                <input type="text" id="file_name" name="file_name" required>
            </div>

            <div class="form-row">
                <label for="date_issued">Date Issued:</label>
                <input type="text" id="date_issued" name="date_issued" required>
            </div>

            <button type="submit" class="btn">Submit File</button>
        </form>
    </section>

    <!-- Search Bar -->
    <section  id="searchBar" class="searchBar" style="display: none;">
        <form action="search_results.php" method="get">
            <input type="text" name="query" placeholder="Search files..." >
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
                <th>Actions</th>
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
                    <!-- update -->
                    <form action="../Backend/Controller.php?" method="get" style="display: inline;">
                        <input type="hidden" name="method_finder" value="edit">
                        <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                        <button type="submit" class="edit">EDIT</button>
                    </form>

                    <!--delete-->
                    <form action="../BackEnd/Controller.php?" method="get" style="display:inline;">
                        <input type="hidden" name="method_finder" value="delete">
                        <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                        <button type="submit"  class="delete">DELETE</button>
                    </form>
                </td>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <script src="users.js"></script>
    
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