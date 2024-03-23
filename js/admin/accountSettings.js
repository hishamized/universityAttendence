document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("editProfileBtn")
    .addEventListener("click", function () {
      var form = document.getElementById("editProfileForm");
      form.style.display =
        form.style.display === "none" || form.style.display === ""
          ? "block"
          : "none";
    });
});

//   Toggle hide and reveal change password form
document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("changePasswordBtn")
    .addEventListener("click", function () {
      var form = document.getElementById("changePasswordForm");
      form.style.display =
        form.style.display === "none" || form.style.display === ""
          ? "block"
          : "none";
    });
});

function hideEditForm(form) {
  form.style.display = "none";
}
