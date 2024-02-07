const echo_other = document.getElementById("echo_other");
const echo_performed2 = document.getElementById("echo_performed2");
const echo_other2 = document.getElementById("echo_other2");

function showElement() {
  if (echo_other.value === "1") {
    echo_performed2.style.display = "block";
    echo_other2.style.display = "block";
  } else {
    echo_performed2.style.display = "none";
    echo_other2.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", echo_other.value);
}

// Check if there's a previously selected value in localStorage
const echo_otherValue = localStorage.getItem("selectedValue");

if (echo_otherValue) {
  echo_other.value = echo_otherValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
echo_other.addEventListener("change", showElement);
