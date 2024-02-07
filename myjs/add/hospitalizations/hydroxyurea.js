const hydroxyurea = document.getElementById("hydroxyurea");
const hydroxyurea_date = document.getElementById("hydroxyurea_date");
const hydroxyurea_dose = document.getElementById("hydroxyurea_dose");


function showElement() {
  if (hydroxyurea.value === "1") {
    hydroxyurea_date.style.display = "block";
    hydroxyurea_dose.style.display = "block";
  } else {
    hydroxyurea_date.style.display = "none";
    hydroxyurea_dose.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hydroxyurea.value);
}

// Check if there's a previously selected value in localStorage
const hydroxyureaValue = localStorage.getItem("selectedValue");

if (hydroxyureaValue) {
  hydroxyurea.value = hydroxyureaValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hydroxyurea.addEventListener("change", showElement);


