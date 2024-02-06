const lower_arms = document.getElementById("lower_arms");
const score_lower_arms = document.getElementById("score_lower_arms");

function showElement() {
  if (lower_arms.value === "1") {
    score_lower_arms.style.display = "block";
  } else {
    score_lower_arms.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", lower_arms.value);
}

// Check if there's a previously selected value in localStorage
const lower_armsValue = localStorage.getItem("selectedValue");

if (lower_armsValue) {
  upper_arms.value = lower_armsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
lower_arms.addEventListener("change", showElement);
