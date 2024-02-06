const foot_exam_finding = document.getElementById("foot_exam_finding");
const foot_exam_other = document.getElementById("foot_exam_other");

function showElement() {
  if (foot_exam_finding.value === "2") {
    foot_exam_other.style.display = "block";
  } else {
    foot_exam_other.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", foot_exam_finding.value);
}

// Check if there's a previously selected value in localStorage
const foot_exam_findingValue = localStorage.getItem("selectedValue");

if (foot_exam_findingValue) {
  foot_exam_finding.value = foot_exam_findingValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
foot_exam_finding.addEventListener("change", showElement);
