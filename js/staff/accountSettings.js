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
  
  function submitForm() {
      // Validate admin password and submit form
      var passwordInput = document.getElementById('adminPassword').value;
      if (passwordInput !== '') {
        document.getElementById('deleteForm').submit();
      } else {
        alert('Please enter your admin password.');
      }
    }
  
    function openDeleteModal() {
      $('#deleteModal').modal('show');
  }
  