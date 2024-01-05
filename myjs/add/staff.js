$(function () {
  bsCustomFileInput.init();
});

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

$(document).ready(function () {
  var counter = 0;
  $("#addrow2").on("click", function () {
    var newRow = $("<tr>");
    var cols = "";

    cols +=
      '<td><input class="autocomplete form-control" type="text" name="medication_type[]" id="myInput" placeholder="Type medications name..." onkeyup="fetchData1()"</td>';
    cols +=
      '<td><select class="form-control" name="medication_action[]" id="medication_action[]" style="width: 100%;"><option value="">Select</option><option value="1">Continue</option><option value="2">Start</option><option value="3">Stop</option><option value="4">Not Eligible</option></select></td>';
    cols +=
      '<td><input type="text" class="form-control" name="medication_dose[]"></td>';

    cols +=
      '<td><input type="button" class="ibtnDel2 btn btn-md btn-danger "  value="Delete"></td>';
    newRow.append(cols);
    $("table.order-list").append(newRow);
    counter++;

    function fetchData1() {
      /*An array containing all the country names in the world:*/
      // var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];
      // var getUid = $(this).val();
      fetch("fetch_medications.php")
        .then((response) => response.json())
        .then((data) => {
          // Process the data received from the PHP script
          // console.log(data);
          autocomplete(document.getElementById(myInput), data);
        })
        .catch((error) => {
          // Handle any errors that occurred during the fetch request
          console.error("Error:", error);
        });
    }
  });

  $("table.order-list").on("click", ".ibtnDel2", function (event) {
    $(this).closest("tr").remove();
    counter -= 1;
  });
});

$(document).ready(function () {
  var counter = 0;
  $("#addrow3").on("click", function () {
    var newRow = $("<tr>");
    var cols = "";

    cols += '<td><input type="text" class="form-control" name="age[]"/></td>';
    cols +=
      '<td><select class="form-control" name="sex[]" id="sex[]" style="width: 100%;"><option value="">Select</option><option value="1">Male</option><option value="2">Female</option></select></td>';
    cols +=
      '<td><input type="text" class="form-control" name="sickle_status[]"/></td>';

    cols +=
      '<td><input type="button" class="ibtnDel3 btn btn-md btn-danger "  value="Delete"></td>';
    newRow.append(cols);
    $("table.order-list").append(newRow);
    counter++;

    // console.log(cols);
  });

  $("table.order-list").on("click", ".ibtnDel3", function (event) {
    $(this).closest("tr").remove();
    counter -= 1;
  });
});

{
  /* <?php if ($user->data()->pswd == 0) { ?>
            $(window).on('load', function() {
                $("#change_password_n").modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
            });
         <?php } ?> */
}

{
  /* if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        } */
}

function checkQuestionValue1(currentQuestion, elementToHide) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide = document.getElementById(elementToHide);

  var questionValue = currentQuestionInput.value;

  if (questionValue === "1") {
    elementToHide.classList.remove("hidden");
  } else {
    elementToHide.classList.add("hidden");
  }
}

function checkQuestionValue21(currentQuestion, elementToHide) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide = document.getElementById(elementToHide);

  var questionValue = currentQuestionInput.value;

  if (questionValue === "2") {
    elementToHide.classList.remove("hidden");
  } else {
    elementToHide.classList.add("hidden");
  }
}

function checkQuestionValue2(currentQuestion, elementToHide1, elementToHide2) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide1 = document.getElementById(elementToHide1);
  var elementToHide2 = document.getElementById(elementToHide2);

  var questionValue = currentQuestionInput.value;

  if (questionValue === "1") {
    elementToHide1.classList.remove("hidden");
  } else if (questionValue === "2") {
    elementToHide2.classList.remove("hidden");
  } else {
    elementToHide1.classList.add("hidden");
    elementToHide2.classList.add("hidden");
  }
}

function check2QuestionValue2(currentQuestion, elementToHide1, elementToHide2) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide1 = document.getElementById(elementToHide1);
  var elementToHide2 = document.getElementById(elementToHide2);

  var questionValue = currentQuestionInput.value;

  if (questionValue === "1") {
    elementToHide1.classList.remove("hidden");
    elementToHide2.classList.remove("hidden");
  } else if (questionValue === "2") {
    elementToHide1.classList.remove("hidden");
  } else {
    elementToHide1.classList.add("hidden");
    elementToHide2.classList.add("hidden");
  }
}

function checkNotQuestionValue3(
  currentQuestion,
  elementToHide1,
  elementToHide2
) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide1 = document.getElementById(elementToHide1);
  var elementToHide2 = document.getElementById(elementToHide2);

  var questionValue = currentQuestionInput.value;

  if (questionValue != "3") {
    elementToHide1.classList.remove("hidden");
    elementToHide2.classList.remove("hidden");
  } else {
    elementToHide1.classList.add("hidden");
    elementToHide2.classList.add("hidden");
  }
}

function checkNot1QuestionValue3(currentQuestion, elementToHide) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide = document.getElementById(elementToHide1);
  var questionValue = currentQuestionInput.value;

  if (questionValue != "3") {
    elementToHide.classList.remove("hidden");
  } else {
    elementToHide.classList.add("hidden");
  }
}

function checkNotQuestionValue5(currentQuestion, elementToHide) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide = document.getElementById(elementToHide);

  var questionValue = currentQuestionInput.value;
  if (questionValue != "5") {
    elementToHide.classList.remove("hidden");
  } else {
    elementToHide.classList.add("hidden");
  }
}

function checkNotQuestionValue4(currentQuestion, elementToHide) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide = document.getElementById(elementToHide);

  var questionValue = currentQuestionInput.value;
  if (questionValue != "4") {
    elementToHide.classList.remove("hidden");
  } else {
    elementToHide.classList.add("hidden");
  }
}

function checkQuestionValue96(currentQuestion, elementToHide) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide = document.getElementById(elementToHide);

  var questionValue = currentQuestionInput.value;

  if (questionValue === "96") {
    elementToHide.classList.remove("hidden");
  } else {
    elementToHide.classList.add("hidden");
  }
}

function checkQuestionValue3(currentQuestion, elementToHide1, elementToHide2) {
  var currentQuestionInput = document.getElementById(currentQuestion);
  var elementToHide1 = document.getElementById(elementToHide1);
  var elementToHide2 = document.getElementById(elementToHide2);

  var questionValue = currentQuestionInput.value;

  if (questionValue === "1") {
    elementToHide1.classList.remove("hidden");
    elementToHide2.classList.remove("hidden");
  } else {
    elementToHide1.classList.add("hidden");
    elementToHide2.classList.add("hidden");
  }
}

function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function (e) {
    var a,
      b,
      i,
      val = this.value;
    /*close any already open lists of autocompleted values*/
    closeAllLists();
    if (!val) {
      return false;
    }
    currentFocus = -1;
    /*create a DIV element that will contain the items (values):*/
    a = document.createElement("DIV");
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("class", "autocomplete-items");
    /*append the DIV element as a child of the autocomplete container:*/
    this.parentNode.appendChild(a);
    /*for each item in the array...*/
    for (i = 0; i < arr.length; i++) {
      /*check if the item starts with the same letters as the text field value:*/
      if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
        /*create a DIV element for each matching element:*/
        b = document.createElement("DIV");
        /*make the matching letters bold:*/
        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
        b.innerHTML += arr[i].substr(val.length);
        /*insert a input field that will hold the current array item's value:*/
        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
        /*execute a function when someone clicks on the item value (DIV element):*/
        b.addEventListener("click", function (e) {
          /*insert the value for the autocomplete text field:*/
          inp.value = this.getElementsByTagName("input")[0].value;
          /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
          closeAllLists();
        });
        a.appendChild(b);
      }
    }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function (e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
      /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
      currentFocus++;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 38) {
      //up
      /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
      currentFocus--;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 13) {
      /*If the ENTER key is pressed, prevent the form from being submitted,*/
      e.preventDefault();
      if (currentFocus > -1) {
        /*and simulate a click on the "active" item:*/
        if (x) x[currentFocus].click();
      }
    }
  });

  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = x.length - 1;
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }

  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }

  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
    closeAllLists(e.target);
  });
}

function fetchData() {
  /*An array containing all the country names in the world:*/
  // var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];
  // var getUid = $(this).val();
  fetch("fetch_medications.php")
    .then((response) => response.json())
    .then((data) => {
      // Process the data received from the PHP script
      // console.log(data);
      autocomplete(document.getElementById("myInput"), data);
    })
    .catch((error) => {
      // Handle any errors that occurred during the fetch request
      console.error("Error:", error);
    });

  fetch("fetch_firstname.php")
    .then((response) => response.json())
    .then((data) => {
      // Process the data received from the PHP script
      // console.log(data);
      autocomplete(document.getElementById("firstname"), data);
    })
    .catch((error) => {
      // Handle any errors that occurred during the fetch request
      console.error("Error:", error);
    });

  fetch("fetch_middlename.php")
    .then((response) => response.json())
    .then((data) => {
      // Process the data received from the PHP script
      // console.log(data);
      autocomplete(document.getElementById("middlename"), data);
    })
    .catch((error) => {
      // Handle any errors that occurred during the fetch request
      console.error("Error:", error);
    });

  fetch("fetch_lastname.php")
    .then((response) => response.json())
    .then((data) => {
      // Process the data received from the PHP script
      autocomplete(document.getElementById("lastname"), data);
    })
    .catch((error) => {
      // Handle any errors that occurred during the fetch request
      console.error("Error:", error);
    });

  $(document).ready(function () {
    $("#add_crf6").click(function (e) {
      // if ($("#validation")[0].checkValidity()) {
      //   PREVENT PAGE TO REFRESH
      // e.preventDefault();

      // if($("#FDATE").val() == ''){
      //     $("#FDATEError").text('* Date is empty');
      // };
      // if($("#cDATE").val() == ''){
      //     $("#cDATEError").text('* Date is empty');
      // };
      // if($("#cpersid").val() == ''){
      //     $("#cpersidError").text('* NAME is empty');
      // };

      if ($("#renal_urea").val() == "") {
        $("#renal_ureaError").text("* Renal Urea is empty");
      }

      if ($("#renal_urea_units").val() == "") {
        $("#renal_urea_unitsError").text("* Renal Urea Units is empty");
      }

      // if ($("#password1").val() != $("#password2").val()) {
      //     $("#passError").text('* Passowrd do not match');
      //     //console.log("Not matched");
      //     $("#register-btn").val('Sign Up');
      // }
      // }
    });

    $("#weight, #height").on("input", function () {
      setTimeout(function () {
        var weight = $("#weight").val();
        var height = $("#height").val() / 100; // Convert cm to m
        var bmi = weight / (height * height);
        $("#bmi").text(bmi.toFixed(2));
      }, 1);
    });

    $("#one").on("input", null, null, function (e) {
      if ($("#one").val().length == 2) {
        setTimeout(function () {
          $("#two").focus();
        }, 1);
      }
    });
    $("#three").click(function () {
      $("#four").focus();
    });
    $("#five").on("input", null, null, function () {
      if ($("#five").val().length == 2) {
        $("#six").val("It works!");
      }
    });

    $("#fl_wait").hide();
    $("#wait_ds").hide();
    $("#region").change(function () {
      var getUid = $(this).val();
      $("#wait_ds").show();
      $.ajax({
        url: "process.php?cnt=region",
        method: "GET",
        data: {
          getUid: getUid,
        },
        success: function (data) {
          $("#ds_data").html(data);
          $("#wait_ds").hide();
        },
      });
    });
    $("#wait_wd").hide();
    $("#ds_data").change(function () {
      $("#wait_wd").hide();
      var getUid = $(this).val();
      $.ajax({
        url: "process.php?cnt=district",
        method: "GET",
        data: {
          getUid: getUid,
        },
        success: function (data) {
          $("#wd_data").html(data);
          $("#wait_wd").hide();
        },
      });
    });

    $("#a_cc").change(function () {
      var getUid = $(this).val();
      $("#wait").show();
      $.ajax({
        url: "process.php?cnt=payAc",
        method: "GET",
        data: {
          getUid: getUid,
        },
        success: function (data) {
          $("#cus_acc").html(data);
          $("#wait").hide();
        },
      });
    });

    $("#study_id").change(function () {
      var getUid = $(this).val();
      var type = $("#type").val();
      $("#fl_wait").show();
      $.ajax({
        url: "process.php?cnt=study",
        method: "GET",
        data: {
          getUid: getUid,
          type: type,
        },

        success: function (data) {
          console.log(data);
          $("#s2_2").html(data);
          $("#fl_wait").hide();
        },
      });
    });
  });
}

// function fetchData() {
//     // Clear previous search results
//     // document.getElementById("searchResults").innerHTML = "";
//     // document.getElementById("myInput").innerHTML = "";

//     // Get the search input value
//     // var searchInput = document.getElementById("searchInput").value;
//     var searchInput = document.getElementById("myInput").value;

//     // Fetch data from the server
//     // fetch("search.php?query=" + searchInput)
//     fetch("fetch_medications.php")

//         .then(response => response.json())
//         .then(data => {
//             // Process the fetched data
//             // data.forEach(result => {
//             //     // Create a list item for each result
//             //     var li = document.createElement("li");
//             //     li.textContent = result;
//             //     document.getElementById("searchResults").appendChild(li);
//             // });
//             // var searchInput = document.getElementById("myInput").value;

//             // console.log(data);
//         })
//         .catch(error => console.error(error));
// }

$("#weight, #height").on("input", function () {
  setTimeout(function () {
    var weight = $("#weight").val();
    var height = $("#height").val() / 100; // Convert cm to m
    var bmi = weight / (height * height);
    $("#bmi").text(bmi.toFixed(2));
  }, 1);
});

$(document).ready(function () {
  $("#fl_wait").hide();
  $("#wait_ds").hide();
  $("#region").change(function () {
    var getUid = $(this).val();
    $("#wait_ds").show();
    $.ajax({
      url: "process.php?cnt=region",
      method: "GET",
      data: {
        getUid: getUid,
      },
      success: function (data) {
        $("#ds_data").html(data);
        $("#wait_ds").hide();
      },
    });
  });
  $("#wait_wd").hide();
  $("#ds_data").change(function () {
    $("#wait_wd").hide();
    var getUid = $(this).val();
    $.ajax({
      url: "process.php?cnt=district",
      method: "GET",
      data: {
        getUid: getUid,
      },
      success: function (data) {
        $("#wd_data").html(data);
        $("#wait_wd").hide();
      },
    });
  });

  $("#a_cc").change(function () {
    var getUid = $(this).val();
    $("#wait").show();
    $.ajax({
      url: "process.php?cnt=payAc",
      method: "GET",
      data: {
        getUid: getUid,
      },
      success: function (data) {
        $("#cus_acc").html(data);
        $("#wait").hide();
      },
    });
  });

  $("#study_id").change(function () {
    var getUid = $(this).val();
    var type = $("#type").val();
    $("#fl_wait").show();
    $.ajax({
      url: "process.php?cnt=study",
      method: "GET",
      data: {
        getUid: getUid,
        type: type,
      },

      success: function (data) {
        console.log(data);
        $("#s2_2").html(data);
        $("#fl_wait").hide();
      },
    });
  });
});

// Add row chemotherapy
document
  .getElementById("add-medication")
  .addEventListener("click", function () {
    var table = document
      .getElementById("medication_list")
      .getElementsByTagName("tbody")[0];
    var newRow = table.insertRow(table.rows.length);
    var medication_type = newRow.insertCell(0);
    var medication_action = newRow.insertCell(1);
    var medication_dose = newRow.insertCell(2);
    var actionCell = newRow.insertCell(3);
    medication_type.innerHTML =
      '<input class="autocomplete" type="text" name="medication_type[]" id="myInput" placeholder="Type medications name..." onkeyup="fetchData()">';
    medication_action.innerHTML =
      '<select name="medication_action[]" id="medication_action[]" style="width: 100%;"><option value="">Select</option><option value="1">Continue</option><option value="2">Start</option><option value="3">Stop</option><option value="4">Not Eligible</option></select>';
    medication_dose.innerHTML = '<input type="text" name="medication_dose[]">';
    actionCell.innerHTML =
      '<button type="button" class="remove-row">Remove</button>';
    // console.log(medication_type);
  });

// Add row chemotherapy
document
  .getElementById("add-hospitalization-details")
  .addEventListener("click", function () {
    var table = document
      .getElementById("hospitalization_details_table")
      .getElementsByTagName("tbody")[0];
    var newRow = table.insertRow(table.rows.length);
    var admission_date = newRow.insertCell(0);
    var admission_reason = newRow.insertCell(1);
    var discharge_diagnosis = newRow.insertCell(2);
    var actionCell = newRow.insertCell(3);
    admission_date.innerHTML =
      '<input type="text" name="admission_date[]"><span>(Example: 2010-12-01)</span>';
    admission_reason.innerHTML =
      '<input type="text" name="admission_reason[]">';
    discharge_diagnosis.innerHTML =
      '<input type="text" name="discharge_diagnosis[]">';
    actionCell.innerHTML =
      '<button type="button" class="remove-row">Remove</button>';
  });

// Add row surgery
document
  .getElementById("add-sickle-cell-status")
  .addEventListener("click", function () {
    var table = document
      .getElementById("sickle_cell_table")
      .getElementsByTagName("tbody")[0];
    var newRow = table.insertRow(table.rows.length);
    var age = newRow.insertCell(0);
    var sex = newRow.insertCell(1);
    var status = newRow.insertCell(2);
    var actionCell = newRow.insertCell(3);
    age.innerHTML = '<input type="text" name="age[]">';
    sex.innerHTML =
      '<select name="sex[]" id="sex[]" style="width: 100%;"><option value="">Select</option><option value="1">Male</option><option value="2">Female</option></select>';
    status.innerHTML = '<input type="text" name="sickle_status[]">';
    actionCell.innerHTML =
      '<button type="button" class="remove-row">Remove</button>';
  });

// Remove row
document.addEventListener("click", function (e) {
  if (e.target && e.target.classList.contains("remove-row")) {
    var row = e.target.parentNode.parentNode;
    row.parentNode.removeChild(row);
  }
});
