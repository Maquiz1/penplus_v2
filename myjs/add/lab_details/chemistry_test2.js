const chemistry_test2 = document.getElementById("chemistry_test2");
const hide_chemistry_test2 = document.getElementById("hide_chemistry_test2");

function showElement() {
  if (chemistry_test2.value === "1") {
    hide_chemistry_test2.style.display = "block";
  } else {
    hide_chemistry_test2.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", chemistry_test2.value);
}

// Check if there's a previously selected value in localStorage
const chemistry_test2Value = localStorage.getItem("selectedValue");

if (chemistry_test2Value) {
  chemistry_test2.value = chemistry_test2Value;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
chemistry_test2.addEventListener("change", showElement);
