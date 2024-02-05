const hiv = document.getElementById("hiv");
const hiv_test = document.getElementById("hiv_test");
const art1 = document.getElementById("art1");

function showElement() {
  if (hiv.value === "1") {
    hiv_test.style.display = "block";
    art1.style.display = "block";
  } else if (hiv.value === "2") {
    hiv_test.style.display = "block";
    art1.style.display = "none";
  } else {
    hiv_test.style.display = "none";
    art1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hiv.value);
}

// Check if there's a previously selected value in localStorage
const renalValue = localStorage.getItem("selectedValue");

if (renalValue) {
  hiv.value = renalValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hiv.addEventListener("change", showElement);
