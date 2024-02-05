const cardiac_surgery_type = document.getElementById("cardiac_surgery_type");
const surgery_other1 = document.getElementById("surgery_other1");

function showElement() {
  if (cardiac_surgery_type.value === "96") {
    surgery_other1.style.display = "block";
  } else {
    surgery_other1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", cardiac_surgery_type.value);
}

// Check if there's a previously selected value in localStorage
const cardiac_surgery_typeValue = localStorage.getItem("selectedValue");

if (cardiac_surgery_typeValue) {
  cardiac_surgery_type.value = cardiac_surgery_typeValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
cardiac_surgery_type.addEventListener("change", showElement);
