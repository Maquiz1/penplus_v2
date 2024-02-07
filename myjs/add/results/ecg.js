const ecg = document.getElementById("ecg");
const ecg_other = document.getElementById("ecg_other");

function showElement() {
  if (ecg.value === "96") {
    ecg_other.style.display = "block";
  } else {
    ecg_other.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", ecg.value);
}

// Check if there's a previously selected value in localStorage
const ecgValue = localStorage.getItem("selectedValue");

if (ecgValue) {
  ecg.value = ecgValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
ecg.addEventListener("change", showElement);
