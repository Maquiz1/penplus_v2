const social_support = document.getElementById("social_support");
const social_support_type = document.getElementById("social_support_type");

function showElement() {
  if (social_support.value === "1") {
    social_support_type.style.display = "block";
  } else {
    social_support_type.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", social_support.value);
}

// Check if there's a previously selected value in localStorage
const social_supportValue = localStorage.getItem("selectedValue");

if (social_supportValue) {
  social_support.value = social_supportValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
social_support.addEventListener("change", showElement);
