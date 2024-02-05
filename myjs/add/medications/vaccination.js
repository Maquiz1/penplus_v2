const vaccination = document.getElementById("vaccination");
const vaccination_specify = document.getElementById("vaccination_specify");

function showElement() {
  if (vaccination.value === "1") {
    vaccination_specify.style.display = "block";
  } else {
    vaccination_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", vaccination.value);
}

// Check if there's a previously selected value in localStorage
const vaccinationValue = localStorage.getItem("selectedValue");

if (vaccinationValue) {
  vaccination.value = vaccinationValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
vaccination.addEventListener("change", showElement);
