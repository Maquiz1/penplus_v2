const cardiac_surgery2 = document.getElementById("cardiac_surgery2");
const cardiac_surgery_type2 = document.getElementById("cardiac_surgery_type2");

function showElement() {
  if (cardiac_surgery2.value === "1") {
    cardiac_surgery_type2.style.display = "block";
  } else {
    cardiac_surgery_type2.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", cardiac_surgery2.value);
}

// Check if there's a previously selected value in localStorage
const cardiac_surgery2Value = localStorage.getItem("selectedValue");

if (cardiac_surgery2Value) {
  cardiac_surgery2.value = cardiac_surgery2Value;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
cardiac_surgery2.addEventListener("change", showElement);
