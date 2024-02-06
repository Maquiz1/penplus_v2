const hypoglycemia_severe = document.getElementById("hypoglycemia_severe");
const hypoglycemia__number = document.getElementById("hypoglycemia__number");

function showElement() {
  if (hypoglycemia_severe.value === "1") {
    hypoglycemia__number.style.display = "block";
  } else {
    hypoglycemia__number.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hypoglycemia_severe.value);
}

// Check if there's a previously selected value in localStorage
const hypoglycemia_severeValue = localStorage.getItem("selectedValue");

if (hypoglycemia_severeValue) {
  hypoglycemia_severe.value = hypoglycemia_severeValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hypoglycemia_severe.addEventListener("change", showElement);

