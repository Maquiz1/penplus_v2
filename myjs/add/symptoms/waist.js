const waist = document.getElementById("waist");
const score_waist = document.getElementById("score_waist");

function showElement() {
  if (upper_arms.value === "1") {
    score_waist.style.display = "block";
  } else {
    score_waist.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", waist.value);
}

// Check if there's a previously selected value in localStorage
const waistValue = localStorage.getItem("selectedValue");

if (waistValue) {
  waist.value = waistValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
waist.addEventListener("change", showElement);
