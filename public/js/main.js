const successAlert = document.getElementById("success-alert");

if (successAlert) {
  setTimeout(() => {
    successAlert.style.display = "none";
  }, 2000);
}

const deleteBtn = document.getElementById("delete-user");

if (deleteBtn) {
  deleteBtn.addEventListener("click", (e) => {
    if (confirm("Are you sure?")) {
      const id = e.target.getAttribute("data-id");

      fetch(`/user/delete/${id}`, {
        method: "DELETE",
      }).then((res) => (res.ok ? window.location.replace("/") : alert(res)));
    }
  });
}

const rememberCheck = document.getElementById("remember-me");
const inputEmail = document.getElementById("inputEmail");
const inputPassword = document.getElementById("inputPassword");

function remember() {
  if (rememberCheck.checked) {
    localStorage.setItem("email", inputEmail.value);
    localStorage.setItem("password", inputPassword.value);
  } else {
    localStorage.clear();
  }
}

(function init() {
  const email = localStorage.getItem("email");
  const password = localStorage.getItem("password");

  if (email && password) {
    inputEmail.value = email;
    inputPassword.value = password;
    rememberCheck.setAttribute("checked", true);
  }
})();
