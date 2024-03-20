function enableBranches() {
    var selectCourse = document.getElementById('selectCourse');
    var selectBranch = document.getElementById('selectBranch');
    var course_id = selectCourse.value;
    selectBranch.disabled = false;

    if (selectCourse.value == 'default') {
        selectBranch.disabled = true;
        return; // Exit the function if course is not selected
    }

    // Fetch branches based on selected course_id using AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_fetch_branches.php?action=fetch_branches&course_id=' + course_id, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    console.log(xhr.responseText);
                    var branches = JSON.parse(xhr.responseText);
                    populateBranches(branches);
                } catch (error) {
                    console.error('Error parsing JSON response:', error);
                }
            } else {
                console.error('Error fetching branches: ' + xhr.status);
            }
        }
    };
    xhr.send();
}

function populateBranches(branches) {
    var branchSelect = document.getElementById('selectBranch');
    branchSelect.innerHTML = '<option value="default">Select Branch</option>';
    branches.forEach(function(branch) {
        var option = document.createElement('option');
        option.value = branch.id;
        option.textContent = branch.branch_name;
        branchSelect.appendChild(option);
    });
}

function enableSemesters(){
    var selectBranch = document.getElementById('selectBranch');
    var selectSemester = document.getElementById('selectSemester');
    selectSemester.disabled = false;
    if(selectBranch.value == 'default'){
        selectSemester.disabled = true;
    }
}
