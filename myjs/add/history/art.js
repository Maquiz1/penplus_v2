const art = document.getElementById("art");
const art_date = document.getElementById("art_date");

function showElement() {
  if (art.value === "1") {
    art_date.style.display = "block";
  } else {
    art_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", art.value);
}

// Check if there's a previously selected value in localStorage
const artValue = localStorage.getItem("selectedValue");

if (artValue) {
  art.value = artValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
art.addEventListener("change", showElement);
