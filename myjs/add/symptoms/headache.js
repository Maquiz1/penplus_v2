const headache = document.getElementById("headache");
const score_headache = document.getElementById("score_headache");

function showElement() {
  if (headache.value === "1") {
    score_headache.style.display = "block";
  } else {
    score_headache.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", headache.value);
}

// Check if there's a previously selected value in localStorage
const headacheValue = localStorage.getItem("selectedValue");

if (headacheValue) {
  headache.value = headacheValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
headache.addEventListener("change", showElement);
