const diagns_complication = document.getElementById("diagns_complication");
const new_ncd_diagns = document.getElementById("new_ncd_diagns");

function showElement() {
  if (diagns_complication.value === "1") {
    new_ncd_diagns.style.display = "block";
  } else {
    new_ncd_diagns.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", diagns_complication.value);
}

// Check if there's a previously selected value in localStorage
const diagns_complicationValue = localStorage.getItem("selectedValue");

if (diagns_complicationValue) {
  diagns_complication.value = diagns_complicationValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
diagns_complication.addEventListener("change", showElement);
