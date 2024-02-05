const neuropathy = document.getElementById("neuropathy");
const neuropathy_date = document.getElementById("neuropathy_date");

function showElement() {
  if (neuropathy.value === "1") {
    neuropathy_date.style.display = "block";
  } else {
    neuropathy_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", neuropathy.value);
}

// Check if there's a previously selected value in localStorage
const neuropathyValue = localStorage.getItem("selectedValue");

if (neuropathyValue) {
  neuropathy.value = neuropathyValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
neuropathy.addEventListener("change", showElement);
