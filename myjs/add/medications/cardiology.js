const cardiology = document.getElementById("cardiology");
const completed_hidden = document.getElementById("completed_hidden");
const cardiology_reason = document.getElementById("cardiology_reason");

function showElement() {
  if (cardiology.value === "1") {
    completed_hidden.style.display = "block";
    cardiology_reason.style.display = "none";
  } else if (cardiology.value === "2") {
    completed_hidden.style.display = "none";
    cardiology_reason.style.display = "block";
  } else {
    completed_hidden.style.display = "none";
    cardiology_reason.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", cardiology.value);
}

// Check if there's a previously selected value in localStorage
const cardiologyValue = localStorage.getItem("selectedValue");

if (cardiologyValue) {
  cardiology.value = cardiologyValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
cardiology.addEventListener("change", showElement);
