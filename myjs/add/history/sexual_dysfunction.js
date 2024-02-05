const sexual_dysfunction = document.getElementById("sexual_dysfunction");
const sexual_dysfunction_date = document.getElementById(
  "sexual_dysfunction_date"
);

function showElement() {
  if (sexual_dysfunction.value === "1") {
    sexual_dysfunction_date.style.display = "block";
  } else {
    sexual_dysfunction_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", sexual_dysfunction.value);
}

// Check if there's a previously selected value in localStorage
const sexual_dysfunctionValue = localStorage.getItem("selectedValue");

if (sexual_dysfunctionValue) {
  sexual_dysfunction.value = sexual_dysfunctionValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
sexual_dysfunction.addEventListener("change", showElement);
