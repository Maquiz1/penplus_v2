const echo_performed = document.getElementById("echo_performed");
const echo_performed1 = document.getElementById("echo_performed1");

function showElement() {
  if (echo_performed.value === "1") {
    echo_performed1.style.display = "block";
  } else {
    echo_performed1.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", echo_performed.value);
}

// Check if there's a previously selected value in localStorage
const echo_performedValue = localStorage.getItem("selectedValue");

if (echo_performedValue) {
  echo_performed.value = echo_performedValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
echo_performed.addEventListener("change", showElement);
