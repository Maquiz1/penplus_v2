const other_complication = document.getElementById("other_complication");
const specify_complication = document.getElementById("specify_complication");

function showElement() {
  if (other_complication.value === "1") {
    specify_complication.style.display = "block";
  } else {
    specify_complication.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", other_complication.value);
}

// Check if there's a previously selected value in localStorage
const other_complicationValue = localStorage.getItem("selectedValue");

if (other_complicationValue) {
  other_complication.value = other_complicationValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
other_complication.addEventListener("change", showElement);
