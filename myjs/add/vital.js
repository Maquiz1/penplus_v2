function passValue() {
  $("#weight, #height").on("input", function () {
    setTimeout(function () {
      var weight = $("#weight").val();
      var height = $("#height").val() / 100; // Convert cm to m
      var bmi = weight / (height * height);
      // $("#bmi").text(bmi.toFixed(2));
      // $("#bmi").html(bmi);
      // $("#bmi").val();
      // console.log(bmi);

      bmi = bmi.toFixed(2);
      // Get the value you want to pass (for example, a hardcoded value)
      const valueToPass = bmi;

      // Get the input field by its ID
      const inputField = document.getElementById("bmi");

      // Set the value of the input field
      inputField.value = valueToPass;
    }, 1);
  });


}

passValue();
