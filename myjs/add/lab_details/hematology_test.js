const hematology_test = document.getElementById("hematology_test");
const hematology_test_hides = document.getElementById("hematology_test_hides");

function showElement() {
  if (hematology_test.value === "1") {
    hematology_test_hides.style.display = "block";
  } else {
    hematology_test_hides.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hematology_test.value);
}

// Check if there's a previously selected value in localStorage
const hematology_testValue = localStorage.getItem("selectedValue");

if (hematology_testValue) {
  hematology_test.value = hematology_testValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hematology_test.addEventListener("change", showElement);
