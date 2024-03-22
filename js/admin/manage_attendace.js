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

function fetchStudents(class_id) {
  var classSelect = document.getElementById(class_id);
  var class_id = classSelect.value;
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "ajax_fetch_students.php?action=fetch_students&class_id=" + class_id,
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          console.log(xhr.responseText);
          var students = JSON.parse(xhr.responseText);
          populateStudents(students);
        } catch (error) {
          console.error("Error parsing JSON response:", error);
        }
      } else {
        console.error("Error fetching students: " + xhr.status);
      }
    }
  };
  xhr.send();
}

function populateStudents(students) {
    
    var container = document.getElementById("attendanceContainer");

    
    var table = document.createElement("table");
    table.classList.add("table", "table-striped");

    
    var thead = document.createElement("thead");
    var headerRow = document.createElement("tr");

    
    var headers = ["ID", "Name", "Roll Number", "University Enroll", "Status"];

    
    headers.forEach(function(headerText) {
        var headerCell = document.createElement("th");
        headerCell.textContent = headerText;
        headerRow.appendChild(headerCell);
    });

    
    thead.appendChild(headerRow);

    
    table.appendChild(thead);

    
    var tbody = document.createElement("tbody");

    var studentCount = students.length;
    var counter = 0;

    
    students.forEach(function(student) {
        counter = counter + 1;
        
        var row = document.createElement("tr");

        
        var idCell = document.createElement("td");
        idCell.textContent = student.id;
        row.appendChild(idCell);

        var nameCell = document.createElement("td");
        nameCell.textContent = student.name;
        row.appendChild(nameCell);

        var rollNumberCell = document.createElement("td");
        rollNumberCell.textContent = student.roll_number;
        row.appendChild(rollNumberCell);

        var universityEnrollCell = document.createElement("td");
        universityEnrollCell.textContent = student.university_enroll;
        row.appendChild(universityEnrollCell);

        var statusCell = document.createElement("td");
        var statusInput = document.createElement("input");
        
        statusInput.style.backgroundColor = "green";
        statusInput.style.color = "white";
        statusInput.style.border = "none";
        statusInput.style.padding = "10px 0px";
        statusInput.style.borderRadius = "5px";
        statusInput.style.cursor = "pointer";
        statusInput.style.fontWeight = "bold";
        statusInput.style.fontSize = "16px";
        statusInput.style.textAlign = "center";
        statusInput.style.maxWidth = "100px";

        statusInput.type = "text";
        statusInput.value = "present"; 
        statusInput.dataset.studentId = student.id; 
        statusInput.setAttribute("name", "student" + counter); 

        
        var studentIdInput = document.createElement("input");
        studentIdInput.type = "hidden";
        studentIdInput.name = "student_ids[]";
        studentIdInput.value = student.id;
        row.appendChild(studentIdInput);

        
        statusInput.addEventListener("click", function() {
            switch (this.value) {
                case "present":
                    this.value = "absent";
                    this.style.backgroundColor = "red";
                    break;
                case "absent":
                    this.value = "on_leave";
                    this.style.backgroundColor = "yellow";
                    break;
                case "on_leave":
                    this.value = "present";
                    this.style.backgroundColor = "green";
                    break;
            }
        });

        
        statusCell.appendChild(statusInput);

        
        row.appendChild(statusCell);

        
        tbody.appendChild(row);
    });

    
    table.appendChild(tbody);

    
    container.appendChild(table);

    console.log("Student count: " + studentCount);
    document.getElementById("studentCount").value = studentCount;
}


function findTeacher(subject_id, teacher_id) {
  var subjectSelect = document.getElementById(subject_id);
  var teacherSelect = document.getElementById(teacher_id);
  if (subjectSelect.value != "default") {
    teacherSelect.disabled = false;
  } else {
    teacherSelect.disabled = true;
  }

  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "ajax_fetch_staff.php?action=fetch_staff&subject_id=" + subjectSelect.value,
    true
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          console.log(xhr.responseText);
          var teachers = JSON.parse(xhr.responseText);
          populateTeachers(teachers);
        } catch (error) {
          console.error("Error parsing JSON response:", error);
        }
      } else {
        console.error("Error fetching teachers: " + xhr.status);
      }
    }
  };
  xhr.send();
}

function populateTeachers(teachers) {
  var teacherSelect = document.getElementById("selectTeacher");
  teacherSelect.innerHTML = '<option value="default">Select Teacher</option>';
  teachers.forEach(function (teacher) {
    var option = document.createElement("option");
    option.value = teacher.id;
    option.textContent = teacher.name;
    teacherSelect.appendChild(option);
  });
}
