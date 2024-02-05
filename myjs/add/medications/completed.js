const completed = document.getElementById("completed");
const cardiology_date = document.getElementById("cardiology_date");

function showElement() {
  if (completed.value === "1") {
    cardiology_date.style.display = "block";
  } else if (completed.value === "2") {
    cardiology_date.style.display = "none";
  } else {
    cardiology_date.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", completed.value);
}

// Check if there's a previously selected value in localStorage
const completedValue = localStorage.getItem("selectedValue");

if (completedValue) {
  completed.value = completedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
completed.addEventListener("change", showElement);
