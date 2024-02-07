const diagns_changed = document.getElementById("diagns_changed");
const ncd_diagns = document.getElementById("ncd_diagns");

function showElement() {
  if (diagns_changed.value === "1") {
    ncd_diagns.style.display = "block";
  } else {
    ncd_diagns.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", diagns_changed.value);
}

// Check if there's a previously selected value in localStorage
const diagns_changedValue = localStorage.getItem("selectedValue");

if (diagns_changedValue) {
  diagns_changed.value = diagns_changedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
diagns_changed.addEventListener("change", showElement);
