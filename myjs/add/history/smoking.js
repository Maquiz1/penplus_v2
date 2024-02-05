const smoking = document.getElementById("smoking");
const packs = document.getElementById("packs");
const active_smoker = document.getElementById("active_smoker");

function showElement() {
  if (smoking.value === "1") {
    packs.style.display = "block";
    active_smoker.style.display = "block";
  } else {
    packs.style.display = "none";
    active_smoker.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", smoking.value);
}

// Check if there's a previously selected value in localStorage
const smokingValue = localStorage.getItem("selectedValue");

if (smokingValue) {
  smoking.value = smokingValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
smoking.addEventListener("change", showElement);
