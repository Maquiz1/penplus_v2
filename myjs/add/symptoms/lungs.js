const lungs = document.getElementById("lungs");
const lungs_other = document.getElementById("lungs_other");

function showElement() {
  if (lungs.value === "96") {
    lungs_other.style.display = "block";
  } else {
    lungs_other.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", lungs.value);
}

// Check if there's a previously selected value in localStorage
const lungsValue = localStorage.getItem("selectedValue");

if (lungsValue) {
  lungs.value = lungsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
lungs.addEventListener("change", showElement);
