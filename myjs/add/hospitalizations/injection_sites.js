const issue_injection = document.getElementById("issue_injection");
const issue_injection_yes = document.getElementById("issue_injection_yes");

function showElement() {
  if (issue_injection.value === "1") {
    issue_injection_yes.style.display = "block";
  } else {
    issue_injection_yes.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", issue_injection.value);
}

// Check if there's a previously selected value in localStorage
const issue_injectionValue = localStorage.getItem("selectedValue");

if (issue_injectionValue) {
  issue_injection.value = issue_injectionValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
issue_injection.addEventListener("change", showElement);


