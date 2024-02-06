const joints = document.getElementById("joints");
const joints_hides1 = document.getElementById("joints_hides1");
const joints_hides2 = document.getElementById("joints_hides2");

function showElement() {
  if (joints.value === "1") {
    joints_hides1.style.display = "block";
    joints_hides2.style.display = "block";
  } else {
    joints_hides1.style.display = "none";
    joints_hides2.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", joints.value);
}

// Check if there's a previously selected value in localStorage
const jointsValue = localStorage.getItem("selectedValue");

if (jointsValue) {
  joints.value = jointsValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
joints.addEventListener("change", showElement);
