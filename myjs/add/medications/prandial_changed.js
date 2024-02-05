const prandial_changed = document.getElementById("prandial_changed");
const prandial_changed_hides = document.getElementById(
  "prandial_changed_hides"
);

function showElement() {
  if (prandial_changed.value === "1") {
    prandial_changed_hides.style.display = "block";
  } else {
    prandial_changed_hides.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", prandial_changed.value);
}

// Check if there's a previously selected value in localStorage
const prandial_changedValue = localStorage.getItem("selectedValue");

if (prandial_changedValue) {
  prandial_changed.value = prandial_changedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
prandial_changed.addEventListener("change", showElement);
