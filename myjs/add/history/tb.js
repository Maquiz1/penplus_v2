const tb = document.getElementById("tb");
const tb_year = document.getElementById("tb_year");

function showElement() {
  if (tb.value === "1") {
    tb_year.style.display = "block";
  } else if (tb.value === "2") {
    tb_year.style.display = "block";
  } else if (tb.value === "3") {
    tb_year.style.display = "block";
  } else {
    tb_year.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", tb.value);
}

// Check if there's a previously selected value in localStorage
const tbValue = localStorage.getItem("selectedValue");

if (tbValue) {
  tb.value = tbValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
tb.addEventListener("change", showElement);
