const scd_test = document.getElementById("scd_test");
const scd_test_other = document.getElementById("scd_test_other");

function showElement() {
  if (scd_test.value === "96") {
    scd_test_other.style.display = "block";
  } else {
    scd_test_other.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", scd_test.value);
}

// Check if there's a previously selected value in localStorage
const scd_testValue = localStorage.getItem("selectedValue");

if (scd_testValue) {
  scd_test.value = scd_testValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
scd_test.addEventListener("change", showElement);
