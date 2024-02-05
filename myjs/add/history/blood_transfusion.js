const blood_transfusion = document.getElementById("blood_transfusion");
const transfusion_born = document.getElementById("transfusion_born");
const transfusion_12months = document.getElementById("transfusion_12months");

function showElement() {
  if (blood_transfusion.value === "1") {
    transfusion_born.style.display = "block";
    transfusion_12months.style.display = "block";
  } else {
    transfusion_born.style.display = "none";
    transfusion_12months.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", blood_transfusion.value);
}

// Check if there's a previously selected value in localStorage
const blood_transfusionValue = localStorage.getItem("selectedValue");

if (blood_transfusionValue) {
  blood_transfusion.value = blood_transfusionValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
blood_transfusion.addEventListener("change", showElement);
