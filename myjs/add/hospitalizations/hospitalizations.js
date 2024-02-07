const hospitalizations = document.getElementById("hospitalizations");
const ncd_hospitalizations1 = document.getElementById("ncd_hospitalizations1");


function showElement() {
  if (hospitalizations.value === "1") {
    ncd_hospitalizations1.style.display = "block";
  } else {
    ncd_hospitalizations1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hospitalizations.value);
}

// Check if there's a previously selected value in localStorage
const hospitalizationsValue = localStorage.getItem("selectedValue");

if (hospitalizationsValue) {
  hospitalizations.value = hospitalizationsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hospitalizations.addEventListener("change", showElement);


