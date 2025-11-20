<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digial Archives | Sign Up</title>
</head>
<body>
    <h1>Sign Up</h1>

    <form action="homepage.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password">

        <input type="submit" value="Sign Up" id="signUpBtn">

        <h4>Don't have an account?</h4>
        <a href="login.php">Log In</a>
    </form>
    
    <script src="Users/users.js"></script>
    <script>
        const signUpBtn = document.querySelector('#signUpBtn');
        const email = document.querySelector('#email');
        
        signUpBtn.addEventListener('click', (e) => {
            // loop through every user to see if username already exists
            for(user of users) {
                // compare if current user natches one of the users already in the database
                if (user.username == username.value) {
                    alert("This username exists already!");
                    // prevents form from submitting data
                    e.preventDefault();
                    break;
                } else {
                    continue;
                }
            }

            // Adds user to users database
            users.push({username: username.value,
            email: email.value,
            password: password.value,
            isAdmin: false,
            currentAccount: true
            });

            
        });

    </script>
</body>
</html>