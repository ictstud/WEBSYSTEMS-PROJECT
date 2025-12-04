// Pressing the logo will direct user to homepage or userpage based on login status (admin or not)
document.getElementById("logo").addEventListener("click", () => {
  fetch("/Backend/SessionCheck.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.isAdmin) {
        window.location.href = "homepage.php";
      } else {
        window.location.href = "userpage.php";
      }
    })
    .catch((error) => console.error("Error:", error));
});
