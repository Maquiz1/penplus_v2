const abnorminal_pain = document.getElementById("abnorminal_pain");
const score_abnorminal_pain = document.getElementById("score_abnorminal_pain");

function showElement() {
  if (abnorminal_pain.value === "1") {
    score_abnorminal_pain.style.display = "block";
  } else {
    score_abnorminal_pain.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", abnorminal_pain.value);
}

// Check if there's a previously selected value in localStorage
const abnorminal_painValue = localStorage.getItem("selectedValue");

if (abnorminal_painValue) {
  abnorminal_pain.value = abnorminal_painValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
abnorminal_pain.addEventListener("change", showElement);

