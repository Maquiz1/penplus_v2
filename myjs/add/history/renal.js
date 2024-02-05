const renal = document.getElementById("renal");
const renal_date = document.getElementById("renal_date");

function showElement() {
  if (renal.value === "1") {
    renal_date.style.display = "block";
  } else {
    renal_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", renal.value);
}

// Check if there's a previously selected value in localStorage
const renalValue = localStorage.getItem("selectedValue");

if (renalValue) {
  renal.value = renalValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
renal.addEventListener("change", showElement);
