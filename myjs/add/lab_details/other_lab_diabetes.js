const other_lab_diabetes = document.getElementById("other_lab_diabetes");
const specify_lab_diabetes = document.getElementById("specify_lab_diabetes");

function showElement() {
  if (other_lab_diabetes.value === "1") {
    specify_lab_diabetes.style.display = "block";
  } else {
    specify_lab_diabetes.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", other_lab_diabetes.value);
}

// Check if there's a previously selected value in localStorage
const other_lab_diabetesValue = localStorage.getItem("selectedValue");

if (other_lab_diabetesValue) {
  other_lab_diabetes.value = other_lab_diabetesValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
other_lab_diabetes.addEventListener("change", showElement);
