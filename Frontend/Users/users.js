let users = [{
    username: "JohnDoe",
    email: "johndoe@gmail.com",
    password: "johndoe21",
    isAdmin: true,
    currentAccount: false
}];

const form = document.querySelector("form");
const username = document.querySelector('#username');
const password = document.querySelector('#password');

// For  setting the current user everytime someone signs in or logs in
function setAsCurrentAcc(currentUser) {
    for(user of users) {
        user.currentAccount = false;
    }
    currentUser.currentAccount = true;
}