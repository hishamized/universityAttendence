function toggleAddStaffForm(id){
   var form = document.getElementById(id);
   if(form.style.display == "none"){
      form.style.display = "block";
   } else {
    form.style.display = "none";
   }
}

  // Function to open the edit staff modal and populate form fields
  function openEditStaffModal(staffId, username, fullName, email, phoneNumber, designation) {
    $('#editStaffId').val(staffId);
    $('#editUsername').val(username);
    $('#editFullName').val(fullName);
    $('#editEmail').val(email);
    $('#editPhoneNumber').val(phoneNumber);
    $('#editDesignation').val(designation);
    $('#editStaffModal').modal('show');
}

// When the document is ready
$(document).ready(function() {
    // Add event listener to all edit buttons
    $('.btn-edit').click(function() {
        // Get the staff details from the row
        var staffId = $(this).closest('tr').data('staff-id');
        var username = $(this).closest('tr').find('.username').text();
        var fullName = $(this).closest('tr').find('.full_name').text();
        var email = $(this).closest('tr').find('.email').text();
        var phoneNumber = $(this).closest('tr').find('.phone_number').text();
        var designation = $(this).closest('tr').find('.designation').text();

        // Open the edit staff modal and populate form fields
        openEditStaffModal(staffId, username, fullName, email, phoneNumber, designation);
    });
});

function closeStaffEditModal(){
    $('#editStaffModal').modal('hide');
}

function openDeleteModal(classId) {
    document.getElementById("deleteStaffId").value = classId;
    var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
    deleteModal.show();
  }
  
  function confirmDelete() {
    document.getElementById("deleteForm").submit();
  }