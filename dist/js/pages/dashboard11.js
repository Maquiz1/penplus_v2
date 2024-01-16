$(document).ready(function () {
  // Fetch data from PHP using jQuery AJAX
  $.ajax({
    url: "process1.php",
    type: "GET",
    dataType: "json",
    success: function (data) {
      // Create a Chart.js chart after data is successfully fetched
      createChart(data);
      // console.log(data);
    },
    error: function (error) {
      console.error("Error fetching data:", error);
    },
  });

  function createChart(data) {
    // console.log(data);
    var date = new Date();
    var current_date =
      date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    
    // Get the canvas element
    var ctx = document.getElementById("revenue-chart-canvas").getContext("2d");
    // var ctx = document.getElementById("revenue-chart-canvas3").getContext("2d");

    // Create a new Chart instance
    var myChart = new Chart(ctx, {
      type: "bar", // Change chart type as needed
      data: {
        // labels: ["Label 1", "Label 2", "Label 3", "Label 4", "Label 5"],
        labels: data.map((obj) => obj.monthname),
        datasets: [
          {
            label: "TOTAL ENROLLMENT DATA as of " + current_date,
            data: data.map((obj) => obj.amount),
            backgroundColor: "rgba(75, 192, 192, 0.2)", // Change color as needed
            borderColor: "rgba(75, 192, 192, 1)", // Change color as needed
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  }
});
