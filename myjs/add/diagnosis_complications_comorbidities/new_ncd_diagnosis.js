const new_diagns = document.getElementById("new_diagns");
const new_diagns_specify = document.getElementById("new_diagns_specify");

function showElement() {
  if (new_diagns.value === "1") {
    new_diagns_specify.style.display = "block";
  } else {
    new_diagns_specify.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", new_diagns.value);
}

// Check if there's a previously selected value in localStorage
const new_diagnsValue = localStorage.getItem("selectedValue");

if (new_diagnsValue) {
  new_diagns.value = new_diagnsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
new_diagns.addEventListener("change", showElement);
