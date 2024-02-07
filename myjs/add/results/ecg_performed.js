const ecg_performed = document.getElementById("ecg_performed");
const ecg_performed1 = document.getElementById("ecg_performed1");

function showElement() {
  if (ecg_performed.value === "1") {
    ecg_performed1.style.display = "block";
  } else {
    ecg_performed1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", ecg_performed.value);
}

// Check if there's a previously selected value in localStorage
const ecg_performedValue = localStorage.getItem("selectedValue");

if (ecg_performedValue) {
  ecg_performed.value = ecg_performedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
ecg_performed.addEventListener("change", showElement);
