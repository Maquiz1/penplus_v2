const cmplctn_other = document.getElementById("cmplctn_other");
const complication_specify = document.getElementById("complication_specify");

function showElement() {
  if (cmplctn_other.value === "1") {
    complication_specify.style.display = "block";
  } else {
    complication_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", cmplctn_other.value);
}

// Check if there's a previously selected value in localStorage
const cmplctn_otherValue = localStorage.getItem("selectedValue");

if (cmplctn_otherValue) {
  cmplctn_other.value = cmplctn_otherValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
cmplctn_other.addEventListener("change", showElement);
