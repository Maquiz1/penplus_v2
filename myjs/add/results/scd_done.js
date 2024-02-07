const scd_done = document.getElementById("scd_done");
const scd_test1 = document.getElementById("scd_test1");
const confirmatory_test1 = document.getElementById("confirmatory_test1");

function showElement() {
  if (scd_done.value === "1") {
    scd_test1.style.display = "block";
    confirmatory_test1.style.display = "block";
  } else {
    scd_test1.style.display = "none";
    confirmatory_test1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", scd_done.value);
}

// Check if there's a previously selected value in localStorage
const scd_doneValue = localStorage.getItem("selectedValue");

if (scd_doneValue) {
  scd_done.value = scd_doneValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
scd_done.addEventListener("change", showElement);
