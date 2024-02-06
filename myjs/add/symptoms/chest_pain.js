const chest_pain = document.getElementById("chest_pain");
const score_chest_pain = document.getElementById("score_chest_pain");

function showElement() {
  if (chest_pain.value === "1") {
    score_chest_pain.style.display = "block";
  } else {
    score_chest_pain.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", chest_pain.value);
}

// Check if there's a previously selected value in localStorage
const chest_painValue = localStorage.getItem("selectedValue");

if (chest_painValue) {
  chest_pain.value = chest_painValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
chest_pain.addEventListener("change", showElement);
