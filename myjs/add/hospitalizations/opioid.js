const opioid = document.getElementById("opioid");
const opioid_type = document.getElementById("opioid_type");
const opioid_dose = document.getElementById("opioid_dose");


function showElement() {
  if (opioid.value === "1") {
    opioid_type.style.display = "block";
        opioid_dose.style.display = "block";
  } else {
    opioid_type.style.display = "none";
        opioid_dose.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", opioid.value);
}

// Check if there's a previously selected value in localStorage
const opioidValue = localStorage.getItem("selectedValue");

if (opioidValue) {
  opioid.value = opioidValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
opioid.addEventListener("change", showElement);


