const alcohol_type = document.getElementById("alcohol_type");
const alcohol_other = document.getElementById("alcohol_other");

function showElement() {
  if (alcohol_type.value === "96") {
    alcohol_other.style.display = "block";
  } else {
    alcohol_other.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", alcohol_type.value);
}

// Check if there's a previously selected value in localStorage
const alcohol_typeValue = localStorage.getItem("selectedValue");

if (alcohol_typeValue) {
  alcohol_type.value = alcohol_typeValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
alcohol_type.addEventListener("change", showElement);
