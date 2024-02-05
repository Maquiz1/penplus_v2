const retinopathy = document.getElementById("retinopathy");
const retinopathy_date = document.getElementById("retinopathy_date");

function showElement() {
  if (retinopathy.value === "1") {
    retinopathy_date.style.display = "block";
  } else {
    retinopathy_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", retinopathy.value);
}

// Check if there's a previously selected value in localStorage
const retinopathyValue = localStorage.getItem("selectedValue");

if (retinopathyValue) {
  retinopathy.value = retinopathyValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
retinopathy.addEventListener("change", showElement);
