const transfusion_needed = document.getElementById("transfusion_needed");
const transfusion_units = document.getElementById("transfusion_units");

function showElement() {
  if (transfusion_needed.value === "1") {
    transfusion_units.style.display = "block";
  } else {
    transfusion_units.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", transfusion_needed.value);
}

// Check if there's a previously selected value in localStorage
const transfusion_neededValue = localStorage.getItem("selectedValue");

if (transfusion_neededValue) {
  transfusion_needed.value = transfusion_neededValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
transfusion_needed.addEventListener("change", showElement);
