const cardiac_surgery = document.getElementById("cardiac_surgery");
const cardiac_surgery_type = document.getElementById("cardiac_surgery_type");

function showElement() {
  if (cardiac_surgery.value === "1") {
    cardiac_surgery_type.style.display = "block";
  } else {
    cardiac_surgery_type.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", cardiac_surgery.value);
}

// Check if there's a previously selected value in localStorage
const cardiac_surgeryValue = localStorage.getItem("selectedValue");

if (cardiac_surgeryValue) {
  cardiac_surgery.value = cardiac_surgeryValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
cardiac_surgery.addEventListener("change", showElement);
