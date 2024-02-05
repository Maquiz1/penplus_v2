const cardiovascular = document.getElementById("cardiovascular");
const cardiovascular_date = document.getElementById("cardiovascular_date");

function showElement() {
  if (cardiovascular.value === "1") {
    cardiovascular_date.style.display = "block";
  } else {
    cardiovascular_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", cardiovascular.value);
}

// Check if there's a previously selected value in localStorage
const cardiovascularValue = localStorage.getItem("selectedValue");

if (cardiovascularValue) {
  new_referrals.value = cardiovascularValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
cardiovascular.addEventListener("change", showElement);
