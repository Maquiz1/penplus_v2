const dropdown = document.getElementById("referred");
const elementToShow = document.getElementById("referred_other");

function showElement() {
  if (dropdown.value === "96") {
    elementToShow.style.display = "block";
  } else {
    elementToShow.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", dropdown.value);

  // Check if the elementToShow is hidden and the selected value is the hidden value
  if (elementToShow.style.display === "none" && dropdown.value === "96") {
    // Reset dropdown value to default or another appropriate action
    dropdown.value = "2";
    alert("Other is not available.");
  }
}

// Check if there's a previously selected value in localStorage
const prevSelectedValue = localStorage.getItem("selectedValue");

if (prevSelectedValue) {
  dropdown.value = prevSelectedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
dropdown.addEventListener("change", showElement);

