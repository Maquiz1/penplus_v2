const history_other = document.getElementById("history_other");
const history_specify = document.getElementById("history_specify");


function showElement() {
  if (history_other.value === "1") {
    history_specify.style.display = "block";
  } else {
    history_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", history_other.value);
}

// Check if there's a previously selected value in localStorage
const history_otherValue = localStorage.getItem("selectedValue");

if (history_otherValue) {
  history_other.value = history_otherValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
history_other.addEventListener("change", showElement);
