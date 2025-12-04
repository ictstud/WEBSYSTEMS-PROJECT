<?php 
    include "../BackEnd/Controller.php";
     // If visiting with editID, fetch the record to show inline update form - COPILOT
    $editData = null;
    $controller = new Controller();
    if (!empty($_GET['editID'])) {
        $editId = (int) $_GET['editID'];
        $editData = $controller->update_take_data($editId);
    }
?>
<!------------- HOMEPAGE SPECIFICALLY FOR USERS ------------->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="Images/filestacker_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>FileStacker | A Digital Archive</title>
</head>
<body>
    <!-- NAVIGATION BAR -->
    <nav class="navbar">
    <div class="navbar-left">
    <img src="Images/bsu_logo.png" alt="Logo" class="logo"/>
    <a href="userpage.php" id="redirectLinkLogo"><img src="Images/filestacker_logo.png" alt="Logo" class="logo"/></a>
    <span class="site-title">FileStacker | A Digital Archive</span>
    </div>
    <div class="navbar-right">
        <a href="homepage.php">Home</a>
        <a href="about_us.html">About</a>
        <a href="login.html">Log Out</a>
    </div>
    </nav>

<div class="container">
    <header>
        <h1>Welcome to FileStacker!</h1>
        <hr>
        <h2>What would you like to do?</h2>
        <button class="btn" id="showForm">
            <i class="fa-solid fa-file-arrow-up"></i>Submit a file
        </button>
        <button class="btn" id="showSearch">
            <i class="fa-solid fa-magnifying-glass"></i>Search a file
        </button>
        
        <!-- FOR Sort BUTTON - Toggle between sorted and unsorted -->
        <?php
            $isSorted = isset($_GET['sort']) && $_GET['sort'] == 'date';
            $sortLabel = $isSorted ? 'Unsort' : 'Sort by Date Issued';
        ?>
        <button class="sort-btn" id="sortBtn" style="margin: 20px 0;">
           <i class="fa-solid fa-calendar-days"></i>
           <i class="fa-solid fa-sort"></i><?= htmlspecialchars($sortLabel) ?>
        </button>
        <script>
            document.getElementById('sortBtn').addEventListener('click', function() {
                const url = new URL(window.location);
                if (url.searchParams.has('sort')) {
                    url.searchParams.delete('sort');
                } else {
                    url.searchParams.set('sort', 'date');
                }
                window.location.href = url.toString();
            });
        </script>
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

    <!-- Dec. 3, 2025: All memebers agreed to put EDIT function in userpage.php -->
    <!-- FORM THAT SHOWS WHEN YOU CLICK EDIT -->
    <?php if ($editData): ?>
        <section id="inlineUpdate" id="submitFile" class="submit-file" style="display: block;">
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
                <div class="form-row">
                    <label for="file">File:</label>
                    <input type="file" name="file" id="file">
                </div>
                <button type="submit" class="btn">Save Changes</button>
                <a href="homepage.php" class="btn-cancel" style="text-decoration: none;">Cancel</a>
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
    <!-- SEARCH BAR (CHANGES) -->
    <section id="searchBar" class="searchBar" style="display: none;">
        <form action="homepage.php" method="get">
            <select name="category" required class="category-select">
                <option value="">Search by...</option>
                <option value="ID">ID</option>
                <option value="last_name">Last Name</option>
                <option value="first_name">First Name</option>
                <option value="file_name">File Name</option>
                <option value="date_issued">Date Issued</option>
            </select>

            <input type="text" name="keyword" placeholder="Enter search keyword..." required>

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
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
                // FOR SORTING
                $controller = new Controller();

                if (isset($_GET['sort']) && $_GET['sort'] == 'date') {
                // checks if the link has a SORT value
                $users = $controller->readall_sorted_by_date();
                } 
                else {
                $users = $controller->readall();
                }
                // this is for when the user wants to sort the data (by date) or not

                foreach($users as $user):
                // loops through each result/row in the table    
            ?>
            <tr>
              <td><?=htmlspecialchars($user['ID'])?></td>
              <td><?=htmlspecialchars($user['last_name'])?></td>
              <td><?=htmlspecialchars($user['first_name'])?></td>
              <td><?=htmlspecialchars($user['file_name'])?></td>
              <td><?=htmlspecialchars($user['date_issued'])?></td>
              <td>
            
               <!--update (server-side inline edit) COPILOT-->
                <a href="homepage.php?editID=<?= htmlspecialchars($user['ID'])?>" class="edit-link">EDIT</a>

              <button class="see-file-btn">OPEN FILE</button>
            </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>
    
    <script src="JS/users.js"></script>
    <script src="JS/filesData.js"></script>
    <script src="JS/toggleVisibility.js"></script>
    <script src="JS/microUX.js"></script>
</body>
</html>