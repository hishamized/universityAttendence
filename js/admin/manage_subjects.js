function editSubject(button){
    var rowId = button.getAttribute('data-row-id');

    var subjectRow = document.getElementById(rowId);


    var editSubjectForm = document.getElementById('editSubjectForm');
    
    if(editSubjectForm.style.display === 'none'){
        editSubjectForm.style.display = 'block';
    } else {
        editSubjectForm.style.display = 'none';
    }
    const subjectId = subjectRow.cells[0].innerText;
    const subjectName = subjectRow.cells[1].innerText;
    const semester = subjectRow.cells[5].innerText;
    const creditsCount = subjectRow.cells[6].innerText;
    const assignmentCount = subjectRow.cells[7].innerText;
    const textBookAssigned = subjectRow.cells[8].innerText;

    document.getElementById('editSubjectId').value = subjectId;
    document.getElementById('editSubjectName').value = subjectName;
    document.getElementById('editSemester').value = semester;
    document.getElementById('editCreditsCount').value = creditsCount;
    document.getElementById('editAssignmentCount').value = assignmentCount;
    document.getElementById('editTextbookAssigned').value = textBookAssigned;

    window.scrollTo(0, subjectRow.offsetTop);
    
}

function cancelEditSubject(formId){
    formId.style.display = 'none';
}
function toggleAddForm() {
    var form = document.getElementById('subjectForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function openDeleteModal(classId) {
    document.getElementById("deleteSubjectId").value = classId;
    var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
    deleteModal.show();
  }
  
  function confirmDelete() {
    document.getElementById("deleteForm").submit();
  }

