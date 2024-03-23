function findSubjects(class_id, subject_id) {
    var classSelect = document.getElementById(class_id);
    var subjectSelect = document.getElementById(subject_id);
    if (classSelect.value != "default") {
      subjectSelect.disabled = false;
    } else {
      subjectSelect.disabled = true;
    }
  
    var xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "ajax_fetch_subjects.php?action=fetch_subjects&class_id=" +
        classSelect.value,
      true
    );
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          try {
            console.log(xhr.responseText);
            var subjects = JSON.parse(xhr.responseText);
            populateSubjects(subjects);
          } catch (error) {
            console.error("Error parsing JSON response:", error);
          }
        } else {
          console.error("Error fetching subjects: " + xhr.status);
        }
      }
    };
    xhr.send();
  }
  
  function populateSubjects(subjects) {
    var subjectSelect = document.getElementById("selectSubject");
    subjects.forEach(function (subject) {
      var option = document.createElement("option");
      option.value = subject.id;
      option.textContent = subject.name;
      subjectSelect.appendChild(option);
    });
  }


  function ajaxEditAttendence(attendanceId) {
    
    var form = document.getElementById('editForm' + attendanceId);

    
    var studentName = document.getElementById('name' + attendanceId).value;
    
    
    var formData = new FormData(form);

    
    fetch('ajax_edit_attendance.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            return response.text();
        }
        throw new Error('Network response was not ok.');
    })
    .then(data => {
        
        console.log(data);
        
        alert("Attendence updated successfully for " + studentName);
    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}
