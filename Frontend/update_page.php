<?php
    $id = $_GET['ID'];
    $conversions = (int) $id;
    include "../Backend/Controller.php";

    $controller = new Controller();
    $users_get = $controller->update_take_data($conversions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="../BackEnd/Controller.php?method_finder=update" method="post">
        <input type="hidden" name="ID" value="<?= htmlspecialchars($users_get['ID'])?>">
        <label for="first_name">First Name:</label>
        <input type="text" name="new_first_name" value="<?= htmlspecialchars($users_get['first_name'])?>">
        <br>
        
        <label for="last_name">Last Name:</label>
        <input type="text" name="new_last_name" value="<?= htmlspecialchars($users_get['last_name'])?>">
        <br>

        <label for="file_name">File Name:</label>
        <input type="text" name="new_file_name" value="<?= htmlspecialchars($users_get['file_name'])?>">
        <br>

        <label for="date_issued">Date Issued:</label>
        <input type="text" name="new_date_issued" value="<?= htmlspecialchars($users_get['date_issued'])?>">
        <br>

        <input type="submit" value="submit">
    </form>
</body>
</html>