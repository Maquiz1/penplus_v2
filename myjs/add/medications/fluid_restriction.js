const restriction_other = document.getElementById("restriction_other");
const restriction_specify = document.getElementById("restriction_specify");

function showElement() {
  if (restriction_other.value === "1") {
    restriction_specify.style.display = "block";
  } else {
    restriction_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", restriction_other.value);
}

// Check if there's a previously selected value in localStorage
const restriction_otherValue = localStorage.getItem("selectedValue");

if (restriction_otherValue) {
  restriction_other.value = restriction_otherValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
restriction_other.addEventListener("change", showElement);
