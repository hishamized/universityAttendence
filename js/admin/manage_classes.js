function toggleClassForm() {
  var form = document.getElementById("classForm");
  if (form.style.display === "none") {
    form.style.display = "block";
  } else {
    form.style.display = "none";
  }
}

console.log("Script works!");
function closeEditModal() {
  $("#editModal").modal("hide");
}

function editClassModal(id) {
  const modal = document.getElementById("editModal");
  const isModalVisible = modal.classList.contains("show");

  // Check if the modal is not already visible
  if (!isModalVisible) {
    // Show the modal
    $("#editModal").modal("show");

    // Find the table row corresponding to the provided ID
    const tableRow = document.getElementById(`row-${id}`);

    // Extract data from table cells
    const classId = tableRow.cells[0].innerText; // Assuming class ID is in the first column
    const className = tableRow.cells[1].innerText; // Assuming class name is in the second column
    const semester = tableRow.cells[2].innerText; // Assuming semester is in the third column
    const courseId = tableRow.cells[3].innerText; // Assuming course ID is in the fourth column
    const branchId = tableRow.cells[4].innerText; // Assuming branch ID is in the fifth column
    const duration = tableRow.cells[6].innerText; // Assuming duration is in the sixth column
    const studentCount = tableRow.cells[7].innerText; // Assuming batch year is in the seventh column

    // Populate form fields with extracted data
    document.getElementById("classId").value = classId;
    document.getElementById("editClassName").value = className;
    document.getElementById("editSemester").value = semester;
    document.getElementById("editCourseId").value = courseId;
    document.getElementById("editBranchId").value = branchId;
    document.getElementById("editDuration").value = duration;
    document.getElementById("editStudentCount").value = studentCount;
  } else {
    // Hide the modal if it's already visible
    $("#editModal").modal("hide");
  }
}

function openDeleteModal(classId) {
  document.getElementById("deleteClassId").value = classId;
  var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
  deleteModal.show();
}

function confirmDelete() {
  document.getElementById("deleteForm").submit();
}
