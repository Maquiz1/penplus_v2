const chemistry_test = document.getElementById("chemistry_test");
const hide_chemistry_test = document.getElementById("hide_chemistry_test");

function showElement() {
  if (chemistry_test.value === "1") {
    hide_chemistry_test.style.display = "block";
  } else {
    hide_chemistry_test.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", chemistry_test.value);
}

// Check if there's a previously selected value in localStorage
const chemistry_testValue = localStorage.getItem("selectedValue");

if (chemistry_testValue) {
  chemistry_test.value = chemistry_testValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
chemistry_test.addEventListener("change", showElement);
