const form = document.querySelector("#signupForm");
const usernameInput = document.querySelector("#username");
const emailInput = document.querySelector("#email");
const passwordInput = document.querySelector("#password");

const errorsContainer = document.querySelector("#signupErrors");

// Generic JSON POST helper to avoid redundant fetch code
async function apiPost(url, body) {
  try {
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body),
    });
    return await res.json();
  } catch (err) {
    return { ok: false, error: "Network error" };
  }
}

const checkUserAvailability = (username, email) =>
  apiPost("../Backend/check_user.php", { action: "check", username, email });
const registerUser = (username, email, password) =>
  apiPost("../Backend/check_user.php", {
    action: "create",
    username,
    email,
    password,
  });

function showErrors(arr) {
  if (!errorsContainer) return;
  if (!arr || arr.length === 0) {
    errorsContainer.innerHTML = "";
    return;
  }
  errorsContainer.innerHTML = arr
    .map((e) => `<article>${e}</article>`)
    .join("");
}

if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const username = usernameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value;

    const check = await checkUserAvailability(username, email);
    if (!check.ok) {
      showErrors([check.error || "Unknown error"]);
      return;
    }

    const validationErrors = [];
    if (check.username_exists) validationErrors.push("Username already taken");
    if (check.email_exists) validationErrors.push("Email already registered");
    if (password.length < 6)
      validationErrors.push("Password must be at least 6 characters");

    if (validationErrors.length) {
      showErrors(validationErrors);
      return;
    }

    // No validation errors — create user on server
    const created = await registerUser(username, email, password);
    if (created && created.ok) {
      // Redirect to login after successful signup
      window.location.href = created.redirect_to || "../Frontend/login.html";
    } else {
      showErrors([created.error || "Could not create account"]);
    }
  });
}

// --- Login error handling and rediretion (if on login page) ---
const loginForm = document.querySelector("#loginForm");
const loginUsername = document.querySelector("#email");
const loginPassword = document.querySelector("#password");
const loginErrors = document.querySelector("#loginErrors");

const doLogin = (username, password) =>
  apiPost("../Backend/login.php", { username, password });

if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const username = loginUsername.value.trim();
    const password = loginPassword.value;
    loginErrors.innerHTML = "";

    const resp = await doLogin(username, password);
    if (!resp.ok) {
      loginErrors.innerHTML = `<article>${
        resp.error || "Login failed"
      }</article>`;
      return;
    }

    // Redirect based on if the account is an admin or not
    const isAdmin = resp.isAdmin ? true : false;
    if (isAdmin) {
      window.location.href = "../Frontend/homepage.php";
    } else {
      window.location.href = "../Frontend/userpage.php";
    }
  });
}
