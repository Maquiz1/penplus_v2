const foot_exam = document.getElementById("foot_exam");
const foot_exam_finding1 = document.getElementById("foot_exam_finding1");

function showElement() {
  if (foot_exam.value === "1") {
    foot_exam_finding1.style.display = "block";
  } else {
    foot_exam_finding1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", foot_exam.value);
}

// Check if there's a previously selected value in localStorage
const foot_examValue = localStorage.getItem("selectedValue");

if (foot_examValue) {
  foot_exam.value = foot_examValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
foot_exam.addEventListener("change", showElement);
