let users = [
  {
    username: "JohnDoe",
    email: "johndoe@gmail.com",
    password: "johndoe21",
    isAdmin: true,
    currentAccount: false,
  },
];

const form = document.querySelector("form");
const username = document.querySelector("#username");
const password = document.querySelector("#password");

// For  setting the current user everytime someone signs in or logs in
function setAsCurrentAcc(currentUser) {
  for (user of users) {
    user.currentAccount = false;
  }
  currentUser.currentAccount = true;
}

// Check if account is an admin. If they are the admin, open the homepage where they can edit and delete (??)
function isAdminAccount(user) {
  if (user.isAdmin) {
    form.setAttribute("action", "homepage.php");
  } else {
    form.setAttribute("action", "userpage.php");
  }
}
