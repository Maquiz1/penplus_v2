const other_support = document.getElementById("other_support");
const support_specify = document.getElementById("support_specify");

function showElement() {
  if (other_support.value === "1") {
    support_specify.style.display = "block";
  } else {
    support_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", other_support.value);
}

// Check if there's a previously selected value in localStorage
const other_supportValue = localStorage.getItem("selectedValue");

if (other_supportValue) {
  other_support.value = other_supportValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
other_support.addEventListener("change", showElement);
