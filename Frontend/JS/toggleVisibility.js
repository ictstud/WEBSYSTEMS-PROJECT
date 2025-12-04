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
