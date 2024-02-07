const risk_tb = document.getElementById("risk_tb");
const risk_tb_date = document.getElementById("risk_tb_date");

function showElement() {
  if (risk_tb.value === "1") {
    risk_tb_date.style.display = "block";
  } else if (risk_tb.value === "2") {
    risk_tb_date.style.display = "block";
  } else if (risk_tb.value === "3") {
    risk_tb_date.style.display = "block";
  } else {
    risk_tb_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", risk_tb.value);
}

// Check if there's a previously selected value in localStorage
const risk_tbValue = localStorage.getItem("selectedValue");

if (risk_tbValue) {
  risk_tb.value = risk_tbValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
risk_tb.addEventListener("change", showElement);
