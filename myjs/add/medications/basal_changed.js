const basal_changed = document.getElementById("basal_changed");
const basal_changed_hides = document.getElementById("basal_changed_hides");

function showElement() {
  if (basal_changed.value === "1") {
    basal_changed_hides.style.display = "block";
  } else {
    basal_changed_hides.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", basal_changed.value);
}

// Check if there's a previously selected value in localStorage
const basal_changedValue = localStorage.getItem("selectedValue");

if (basal_changedValue) {
  basal_changed.value = basal_changedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
basal_changed.addEventListener("change", showElement);
