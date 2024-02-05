const alcohol = document.getElementById("alcohol");
const alcohol_type1 = document.getElementById("alcohol_type1");
const quantity = document.getElementById("quantity");

function showElement() {
  if (alcohol.value === "1") {
    alcohol_type1.style.display = "block";
    quantity.style.display = "block";
  } else if (alcohol.value === "2") {
    alcohol_type1.style.display = "block";
    quantity.style.display = "block";
  } else {
    alcohol_type1.style.display = "none";
    quantity.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", alcohol.value);
}

// Check if there's a previously selected value in localStorage
const alcoholValue = localStorage.getItem("selectedValue");

if (alcoholValue) {
  alcohol.value = alcoholValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
alcohol.addEventListener("change", showElement);
