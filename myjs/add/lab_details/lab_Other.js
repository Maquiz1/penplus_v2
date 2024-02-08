const lab_Other = document.getElementById("lab_Other");
const lab_specify = document.getElementById("lab_specify");

function showElement() {
  if (lab_Other.value === "1") {
    lab_specify.style.display = "block";
  } else {
    lab_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", lab_Other.value);
}

// Check if there's a previously selected value in localStorage
const lab_OtherValue = localStorage.getItem("selectedValue");

if (lab_OtherValue) {
  lab_Other.value = lab_OtherValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
lab_Other.addEventListener("change", showElement);
