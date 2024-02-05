const hepatitis_test = document.getElementById("hepatitis_test");
const hepatitis_date = document.getElementById("hepatitis_date");
const hepatitis_results = document.getElementById("hepatitis_results");

function showElement() {
  if (hepatitis_test.value === "1") {
    hepatitis_date.style.display = "block";
    hepatitis_results.style.display = "block";
  } else {
    hepatitis_date.style.display = "none";
    hepatitis_results.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hepatitis_test.value);
}

// Check if there's a previously selected value in localStorage
const hepatitis_testValue = localStorage.getItem("selectedValue");

if (hepatitis_testValue) {
  hepatitis_test.value = hepatitis_testValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hepatitis_test.addEventListener("change", showElement);
