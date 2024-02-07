const ncd_hospitalizations = document.getElementById("ncd_hospitalizations");
const hospitalization_number = document.getElementById(
  "hospitalization_number"
);


function showElement() {
  if (ncd_hospitalizations.value === "1") {
    hospitalization_number.style.display = "block";
  } else {
    hospitalization_number.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", ncd_hospitalizations.value);
}

// Check if there's a previously selected value in localStorage
const ncd_hospitalizationsValue = localStorage.getItem("selectedValue");

if (ncd_hospitalizationsValue) {
  ncd_hospitalizations.value = ncd_hospitalizationsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
ncd_hospitalizations.addEventListener("change", showElement);


