<?php 
    include "../BackEnd/Controller.php";

    // $connect = new Controller();
    // $connect->connection(); - gpt said to remove this since were already connected to the constructor

    // If visiting with editID, fetch the record to show inline update form - COPILOT
    $editData = null;
    $controller = new Controller();
    if (!empty($_GET['editID'])) {
        $editId = (int) $_GET['editID'];
        $editData = $controller->update_take_data($editId);
    }
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
     <!-- ADMIN PAGE -->
    <!-- TO ADD: -->
    <!-- Make search bar functionable -->

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
        <form action="../BackEnd/Controller.php?method_finder=create"  method="post" id="submitFileForm">
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

     <!-- Inline update form (server-side include when ?editID=) - COPILOT-->
     <!-- FORM THAT SHOWS WHEN YOU CLICK EDIT -->
    <?php if ($editData): ?>
        <section id="inlineUpdate" id="submitFile" class="submit-file" style="display: block; margin-top: 20px;">
            <h2>Edit File #<?= htmlspecialchars($editData['ID']) ?></h2>
            <form action="../BackEnd/Controller.php?method_finder=update" method="post">
                <input type="hidden" name="ID" value="<?= htmlspecialchars($editData['ID']) ?>">
                <div class="form-row">
                    <label for="new_first_name">First Name:</label>
                    <input type="text" id="new_first_name" name="new_first_name" value="<?= htmlspecialchars($editData['first_name']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="new_last_name">Last Name:</label>
                    <input type="text" id="new_last_name" name="new_last_name" value="<?= htmlspecialchars($editData['last_name']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="new_file_name">File Name:</label>
                    <input type="text" id="new_file_name" name="new_file_name" value="<?= htmlspecialchars($editData['file_name']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="new_date_issued">Date Issued:</label>
                    <input type="text" id="new_date_issued" name="new_date_issued" value="<?= htmlspecialchars($editData['date_issued']) ?>" required>
                </div>
                <button type="submit" class="btn">Save Changes</button>
                <a href="homepage.php" class="btn" style="text-decoration: none;">Cancel</a>
            </form>
        </section>
        <script>
            // scroll to inline update form after reload - COPILOT
            document.addEventListener('DOMContentLoaded', function(){ 
                var el = document.getElementById('inlineUpdate');
                if (el) el.scrollIntoView({behavior:'smooth', block:'center'});
            });
        </script>
    <?php endif; ?>

    <!-- SEARCH BAR -->
    <section  id="searchBar" class="searchBar" style="display: none;">
        <form action="files_table.sql" method="get">
            <input type="text" name="query" placeholder="Search files..." >
            <button type="submit">Search</button>
        </form>
    </section>
    
</div>

<!-- TABLE DISPLAYING RECORDS -->
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

            if (isset($_GET['sort']) && $_GET['sort'] == 'date') {
            $users = $controller->readall_sorted_by_date();
            } 
            else {
            $users = $controller->readall();
            }

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
                    <button type="submit" class="edit">UPDATE</button>
                    </form>

                    <!--delete-->
                    <form action="../BackEnd/Controller.php?" method="get" style="display:inline;">
                        <input type="hidden" name="method_finder" value="delete">
                        <input type="hidden" name="ID" value="<?= htmlspecialchars($user['ID'])?>">
                        <button type="submit"  class="delete">DELETE</button>
                    </form>

                    <button class="see-file-btn">Open File</button>
                </td>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <script src="Users/filesData.js"></script>
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
  // Alert asking user to confirm before deleting a file
 document.addEventListener('DOMContentLoaded', function() {
     const deleteButtons = document.querySelectorAll('.delete');
     deleteButtons.forEach(function(button) {
         button.addEventListener('click', function(event) {
             const confirmDelete = confirm("Are you sure you want to delete this file?");
             if (!confirmDelete) {
                 event.preventDefault();
             }
         });
     });
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
</body>
</html>