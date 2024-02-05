const stroke_tia = document.getElementById("stroke_tia");
const stroke_tia_date = document.getElementById("stroke_tia_date");

function showElement() {
  if (stroke_tia.value === "1") {
    stroke_tia_date.style.display = "block";
  } else {
    stroke_tia_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", stroke_tia.value);
}

// Check if there's a previously selected value in localStorage
const stroke_tiaValue = localStorage.getItem("selectedValue");

if (stroke_tiaValue) {
  stroke_tia.value = stroke_tiaValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
stroke_tia.addEventListener("change", showElement);
