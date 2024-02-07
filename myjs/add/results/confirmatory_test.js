const confirmatory_test = document.getElementById("confirmatory_test");
const confirmatory_test_type = document.getElementById(
  "confirmatory_test_type"
);

function showElement() {
  if (confirmatory_test.value === "1") {
    confirmatory_test_type.style.display = "block";
  } else {
    confirmatory_test_type.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", confirmatory_test.value);
}

// Check if there's a previously selected value in localStorage
const confirmatory_testValue = localStorage.getItem("selectedValue");

if (confirmatory_testValue) {
  confirmatory_test.value = confirmatory_testValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
confirmatory_test.addEventListener("change", showElement);
