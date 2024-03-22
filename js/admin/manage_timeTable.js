function toggleNewTimeTableForm(){
    var form = document.getElementById('newTimeTableForm');
    if(form.style.display === 'none'){
        form.style.display = 'block';
    }else{
        form.style.display = 'none';
    }
}

function enableSubjects(){
  selectClass = document.getElementById('classId');
  selectSubject = document.getElementById('subjectId');
  if(selectClass.value != 'default'){
    selectSubject.disabled = false;
  } else {
    selectSubject.disabled = true;
  }

  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'ajax_fetch_subjects.php?action=fetch_subjects&class_id=' + selectClass.value, true);
  
  // Event listener to handle changes in the XMLHttpRequest state
  xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
          console.log("Request sent. Waiting for response... ");
          if (xhr.status === 200) {
              console.log("Response received.");
              try {
                  console.log("Response:");
                  console.log(xhr.responseText);
                  var subjects = JSON.parse(xhr.responseText);
                  populateSubjects(subjects);
              } catch (error) {
                  console.error('Error parsing JSON response:', error);
              }
          } else {
              console.error('Error fetching subjects: ' + xhr.status);
          }
      }
  };
  
  xhr.send();
  
}

function populateSubjects(subjects){
var selectSubject = document.getElementById('subjectId');
selectSubject.innerHTML = '<option value="default">Select Subject</option>';
subjects.forEach(function(subject){
    var option = document.createElement('option');
    option.value = subject.id;
    option.textContent = subject.name;
    selectSubject.appendChild(option);
})
}


document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('button[data-timeTableId]');
    var timeTableIdToDelete = document.getElementById('timeTableIdToDelete');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var timeTableId = this.getAttribute('data-timeTableId');
            timeTableIdToDelete.value = timeTableId;
            $('#deleteConfirmationModal').modal('show');
        });
    });
});

function editTimeTable(id){
    const timeTableId = id;
    const row = document.querySelector(`tr[data-rowId="${timeTableId}"]`);
        // Populate form fields with row data
        document.getElementById('editTimeTableId').value = timeTableId;
        document.getElementById('editDay').value = row.cells[0].textContent.trim();
        document.getElementById('editStartTime').value = row.cells[1].textContent.trim();
        document.getElementById('editEndTime').value = row.cells[2].textContent.trim();
        document.getElementById('editDuration').value = row.cells[6].textContent.trim();

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
}

function calculateDuration(endTimeId, startTimeId, durationId) {
    var endTimeString = document.getElementById(endTimeId).value;
    var startTimeString = document.getElementById(startTimeId).value;

    var endTime = new Date('2000-01-01 ' + endTimeString);
    var startTime = new Date('2000-01-01 ' + startTimeString);

    var differenceMs = endTime - startTime;

    var differenceMinutes = Math.floor(differenceMs / (1000 * 60));

    document.getElementById(durationId).value = differenceMinutes;
}
