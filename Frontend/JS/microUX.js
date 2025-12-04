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

  // Add a verification prompt when a user tries to edit an input

  const editButtons = document.querySelectorAll(".edit");

  editButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      const confirmEdit = confirm("Are you sure you want to edit this file?");
      if (!confirmEdit) {
        event.preventDefault();
      }
    });
  });

  // Dark mode
  const darkModeToggle = document.querySelector("#darkModeBtn");
  darkModeToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");

    // If current icon is moon, change it to sun
    if (darkModeToggle.classList.contains("fa-moon")) {
      darkModeToggle.classList.remove("fa-moon");
      darkModeToggle.classList.add("fa-sun");
    } else {
      // If current icon is sun, change it to moon
      darkModeToggle.classList.remove("fa-sun");
      darkModeToggle.classList.add("fa-moon");
    }
  });
});
