const new_referrals = document.getElementById("new_referrals");
const new_referrals_type = document.getElementById("new_referrals_type");

function showElement() {
  if (new_referrals.value === "1") {
    new_referrals_type.style.display = "block";
  } else {
    new_referrals_type.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", new_referrals.value);
}

// Check if there's a previously selected value in localStorage
const new_referralsValue = localStorage.getItem("selectedValue");

if (new_referralsValue) {
  new_referrals.value = new_referralsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
new_referrals.addEventListener("change", showElement);
