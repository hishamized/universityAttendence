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


function printAttendanceTable(printDataJson) {
  var container = document.getElementById("attendance_container");
  var containerStyle = window.getComputedStyle(container);
  var tableHTML = container.outerHTML;

  var printWindow = window.open("", "_blank");
  var title = printDataJson['subject'] + " Attendance" + " - " + printDataJson['date'];
  printWindow.document.write(`
      <html>
          <head>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
              <style>
                  ${containerStyle.cssText}
                  @media print {
                      .btn {
                          display: none;
                      }
                  }
                  
              </style>
              <title> ${title} </title>
          </head>
          <body>
                <div class="container my-4">
                <h1 class="text-center"> ${printDataJson['subject']} </h1>
                <h2 class="text-center"> ${printDataJson['date']} </h2>
                <h3 class="text-center"> ${printDataJson['time']} </h3>
                </div>
              ${tableHTML}
          </body>
      </html>
  `);

  printWindow.document.close();
  printWindow.print();
}

