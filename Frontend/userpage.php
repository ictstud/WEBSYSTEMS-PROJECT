<?php 
    include "../BackEnd/Controller.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="Images/filestacker_logo.png" type="image/x-icon">
    <title>FileStacker | A Digital Archive</title>
</head>
    <!-- HOMEPAGE SPECIFICALLY FOR USERS -->
<body>
    <!-- NAVIGATION BAR -->
    <nav class="navbar">
    <div class="navbar-left">
    <img src="Images/bsu_logo.png" alt="Logo" class="logo"/>
    <img src="Images/filestacker_logo.png" alt="Logo" class="logo"/>
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

      <!-- FORM THAT SHOWS WHEN U CLICK SUBMIT A FILE -->
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

            <div class="form-row">
                <label for="file">File:</label>
                <input type="file" name="file" id="file">
            </div>

            <button type="submit" class="btn">Submit File</button>
        </form>
    </section>

        <!-- SEARCH BAR -->
    <section  id="searchBar" class="searchBar" style="display: none;">
        <form action="files_table.sql" method="get">
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
        <tbody id="files-table">
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

              <td><button class="see-file-btn">OPEN FILE</button></td>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>
    
    <script src="users.js"></script>
    <!-- <script src="Users/filesData.js"></script> = as of 8:56pm i was having an error where the table wasnt showing up but when i commented this it worked--> 
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

    // Alert to confirm successful file submission
     document.addEventListener('DOMContentLoaded', function() {
         const submitForm = document.querySelector('section.submit-file form');
         if (submitForm) {
             submitForm.addEventListener('submit', function() {
                 alert('File has been successfully added!');
             });
         }
     });
</script>
</html>