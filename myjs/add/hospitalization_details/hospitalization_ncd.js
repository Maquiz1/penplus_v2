const hospitalization_ncd = document.getElementById("hospitalization_ncd");
const hospitalization_ncd_hides = document.getElementById(
  "hospitalization_ncd_hides"
);

function showElement() {
  if (hospitalization_ncd.value === "1") {
    hospitalization_ncd_hides.style.display = "block";
  } else {
    hospitalization_ncd_hides.style.display = "none";
  }

  // Save the selected value in localStorage
  localStorage.setItem("selectedValue", hospitalization_ncd.value);
}

// Check if there's a previously selected value in localStorage
const hospitalization_ncdValue = localStorage.getItem("selectedValue");

if (hospitalization_ncdValue) {
  hospitalization_ncd.value = hospitalization_ncdValue;
}

// Show element if Option 2 is selected
showElement();

// Listen for changes in the dropdown
hospitalization_ncd.addEventListener("change", showElement);

$(document).ready(function () {
  var counter = 0;
  $("#addrow").on("click", function () {
    var newRow = $("<tr>");
    var cols = "";

    cols +=
      '<td><input type="text" class="form-control" name="admission_date[]"/></td>';
    cols +=
      '<td><input type="text" class="form-control" name="admission_reason[]"/></td>';
    cols +=
      '<td><input type="text" class="form-control" name="discharge_diagnosis[]"/></td>';

    cols +=
      '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
    newRow.append(cols);
    $("table.order-list").append(newRow);
    counter++;

    // console.log(cols);
  });

  $("table.order-list").on("click", ".ibtnDel", function (event) {
    $(this).closest("tr").remove();
    counter -= 1;
  });
});
