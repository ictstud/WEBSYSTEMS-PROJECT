<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digial Archives | Log In</title>
</head>
<body>
    <h1>Log In</h1>

    <form action="homepage.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password">

        <input type="submit" value="Log In" id="logInBtn">
    </form>

    <script src="Users/users.js"></script>
    <script>
        const logInBtn = document.querySelector('#logInBtn');

        logInBtn.addEventListener('click', (e) => {
            // loop through every user to see if username and password matches
            for (user of users) {
                // Compare every user to the account that just logged in
                if (user.username == username.value && user.password == password.value) {
                    // call the function to set account that just logged in as the current user
                    setAsCurrentAcc(currentUser);
                    console.log(users);
                    continue;
            } else {
                alert("Username or password is incorrect!");
                break;
            };
        };
    });
    </script>
</body>
</html>