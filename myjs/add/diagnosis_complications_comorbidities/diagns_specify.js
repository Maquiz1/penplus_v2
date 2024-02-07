const diagns_cardiac = document.getElementById("diagns_cardiac");
const diagns_diabetes = document.getElementById("diagns_diabetes");
const diagns_sickle = document.getElementById("diagns_sickle");

const diagns_specify = document.getElementById("diagns_specify");

function showElement() {
  if (
    diagns_cardiac.value === "96" ||
    diagns_diabetes.value === "96" ||
    diagns_sickle.value === "96"
  ) {
    diagns_specify.style.display = "block";
  } else {
    diagns_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", diagns_cardiac.value);
  localStorage.setItem("selectedValue", diagns_diabetes.value);
  localStorage.setItem("selectedValue", diagns_sickle.value);
}

// Check if there's a previously selected value in localStorage
const diagns_cardiacValue = localStorage.getItem("selectedValue");
const diagns_diabetesValue = localStorage.getItem("selectedValue");
const diagns_sickleValue = localStorage.getItem("selectedValue");

if (diagns_cardiacValue) {
  diagns_cardiac.value = diagns_cardiacValue;
}
if (diagns_diabetesValue) {
  diagns_diabetes.value = diagns_diabetesValue;
}

if (diagns_sickleValue) {
  diagns_sickle.value = diagns_sickleValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
diagns_cardiac.addEventListener("change", showElement);
diagns_diabetes.addEventListener("change", showElement);
diagns_sickle.addEventListener("change", showElement);
