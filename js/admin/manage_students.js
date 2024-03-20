function editStudent(id){
  var formContainer = document.getElementById('editStudentFormContainer');
  if(formContainer.classList.contains('hidden')){
    formContainer.classList.remove('hidden');
    window.scrollTo(0, formContainer.offsetTop);
  }else{
    formContainer.classList.add('hidden');
  }

  var form = document.getElementById('editStudentForm');
  var row = document.getElementById('studentRow'+id);

  const s_id = row.cells[1].innerText;
  const username = row.cells[2].innerText;
  const full_name = row.cells[3].innerText;
  const email = row.cells[4].innerText;
  const phone_number = row.cells[5].innerText;
  const address = row.cells[6].innerText;
  const session = row.cells[7].innerText;
  const university_enroll = row.cells[8].innerText;
  const registration_number = row.cells[9].innerText;
  const class_roll_number = row.cells[10].innerText;
  const library_card_number = row.cells[11].innerText;
  const validity = row.cells[12].innerText;
  const course = row.cells[13].innerText;
  const branch = row.cells[14].innerText;
  const class_id = row.cells[15].innerText;

  document.getElementById('editId').value = s_id;
  document.getElementById('editUsername').value = username;
  document.getElementById('editFullName').value = full_name;
  document.getElementById('editEmail').value = email;
  document.getElementById('editPhoneNumber').value = phone_number;
  document.getElementById('editAddress').value = address;
  document.getElementById('editSession').value = session;
  document.getElementById('editUniversityEnroll').value = university_enroll;
  document.getElementById('editRegistrationNumber').value = registration_number;
  document.getElementById('editClassRollNumber').value = class_roll_number;
  document.getElementById('editLibraryCardNumber').value = library_card_number;
  document.getElementById('editValidity').value = validity;
  document.getElementById('editCourse').value = course;
  document.getElementById('editBranch').value = branch;
  document.getElementById('editClassId').value = class_id;
}

function closeEditForm(){
  var formContainer = document.getElementById('editStudentFormContainer');
  formContainer.classList.add('hidden');
}

//Dom content loaded
document.addEventListener("DOMContentLoaded", function () {
  const toggleFormBtn = document.getElementById("toggleFormBtn");
  const studentForm = document.getElementById("studentForm");

  toggleFormBtn.addEventListener("click", function () {
    if (studentForm.classList.contains("hidden")) {
      studentForm.classList.remove("hidden");
    } else {
      studentForm.classList.add("hidden");
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
     // Get references to form fields
    const classSelect = document.getElementById('class_id');
    const sessionInput = document.getElementById('session');
    const courseInput = document.getElementById('course');
    const branchInput = document.getElementById('branch');

    // Add event listener to class select
    classSelect.addEventListener('change', function() {
        // Get selected option
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        
        // Update session, course, and branch fields using data attributes
        sessionInput.value = selectedOption.getAttribute('data-batch-year');
        courseInput.value = selectedOption.getAttribute('data-course-name');
        branchInput.value = selectedOption.getAttribute('data-branch-name');
    });
});

