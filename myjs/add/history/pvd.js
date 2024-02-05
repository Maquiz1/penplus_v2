const pvd = document.getElementById("pvd");
const pvd_date = document.getElementById("pvd_date");

function showElement() {
  if (pvd.value === "1") {
    pvd_date.style.display = "block";
  } else {
    pvd_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", pvd.value);
}

// Check if there's a previously selected value in localStorage
const pvdValue = localStorage.getItem("selectedValue");

if (pvdValue) {
  pvd.value = pvdValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
pvd.addEventListener("change", showElement);
