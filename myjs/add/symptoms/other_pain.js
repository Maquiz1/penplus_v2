const other_pain = document.getElementById("other_pain");
const spescify_other_pain = document.getElementById("spescify_other_pain");
const score_other_pain = document.getElementById("score_other_pain");

function showElement() {
  if (other_pain.value === "1") {
    spescify_other_pain.style.display = "block";
    score_other_pain.style.display = "block";
  } else {
    spescify_other_pain.style.display = "none";
    score_other_pain.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", other_pain.value);
}

// Check if there's a previously selected value in localStorage
const other_painValue = localStorage.getItem("selectedValue");

if (other_painValue) {
  other_pain.value = other_painValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
other_pain.addEventListener("change", showElement);
