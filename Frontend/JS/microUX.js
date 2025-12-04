// Pressing the logo will direct user to homepage or userpage based on login status (admin or not)
document.addEventListener("DOMContentLoaded", () => {
  const logoLink = document.querySelector("#redirectLinkLogo");

  fetch("../Backend/login.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.isAdmin) {
        logoLink.setAttribute("href", "homepage.php");
      } else {
        logoLink.setAttribute("href", "userpage.php");
      }
    })
    .catch((error) => console.error("Error:", error));
});

// document.addEventListener("DOMContentLoaded", () => {
//   const logo = document.querySelector("#redirectLinkLogo");

//   fetch("/Backend/SessionCheck.php")
//     .then((response) => response.json())
//     .then((data) => {
//       if (data.isAdmin) {
//         logo.setAttribute("href", "homepage.php");
//       } else {
//         logo.setAttribute("href", "userpage.php");
//       }
//     })
//     .catch((error) => console.error("Error:", error));
// });
