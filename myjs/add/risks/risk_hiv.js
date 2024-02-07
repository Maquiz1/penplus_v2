const risk_hiv = document.getElementById("risk_hiv");
const risk_hiv_date = document.getElementById("risk_hiv_date");
const risk_art1 = document.getElementById("risk_art1");

function showElement() {
  if (risk_hiv.value === "1") {
    risk_hiv_date.style.display = "block";
    risk_art1.style.display = "block";
  } else if (risk_hiv.value === "2") {
    risk_hiv_date.style.display = "block";
    risk_art1.style.display = "none";
  } else {
    risk_hiv_date.style.display = "none";
    risk_art1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", risk_hiv.value);
}

// Check if there's a previously selected value in localStorage
const risk_hivValue = localStorage.getItem("selectedValue");

if (risk_hivValue) {
  risk_hiv.value = risk_hivValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
risk_hiv.addEventListener("change", showElement);
