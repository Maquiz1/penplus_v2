const other_sickle = document.getElementById("other_sickle");
const sickle_specify = document.getElementById("sickle_specify");

function showElement() {
  if (other_sickle.value === "1") {
    sickle_specify.style.display = "block";
  } else {
    sickle_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", other_sickle.value);
}

// Check if there's a previously selected value in localStorage
const other_sickleValue = localStorage.getItem("selectedValue");

if (other_sickleValue) {
  other_sickle.value = other_sickleValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
other_sickle.addEventListener("change", showElement);
