const upper_arms = document.getElementById("upper_arms");
const score_upper_arms = document.getElementById("score_upper_arms");

function showElement() {
  if (upper_arms.value === "1") {
    score_upper_arms.style.display = "block";
  } else {
    score_upper_arms.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", upper_arms.value);
}

// Check if there's a previously selected value in localStorage
const upper_armsValue = localStorage.getItem("selectedValue");

if (upper_armsValue) {
  upper_arms.value = upper_armsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
upper_arms.addEventListener("change", showElement);
