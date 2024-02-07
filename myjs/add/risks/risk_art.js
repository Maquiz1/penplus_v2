const risk_art = document.getElementById("risk_art");
const risk_art_date = document.getElementById("risk_art_date");

function showElement() {
  if (risk_art.value === "1") {
    risk_art_date.style.display = "block";
  } else {
    risk_art_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", risk_art.value);
}

// Check if there's a previously selected value in localStorage
const risk_artValue = localStorage.getItem("selectedValue");

if (risk_artValue) {
  risk_art.value = risk_artValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
risk_art.addEventListener("change", showElement);
