/* global Chart:false */

$(function () {
  ("use strict");

  var ticksStyle = {
    fontColor: "#495057",
    fontStyle: "bold",
  };

  var mode = "index";
  var intersect = true;

  // ... Previous HTML and Chart.js setup remains unchanged ...

  // Function to fetch data from PHP endpoint
  function fetchData() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          var chartData = JSON.parse(xhr.responseText);
          createChart(chartData);
          // console.log(chartData);
        } else {
          console.error("Failed to fetch data");
        }
      }
    };
    xhr.open("GET", "process3.php", true);
    xhr.send();
  }

  // Call the function to fetch data and create the chart
  fetchData();

  // Function to create Chart.js chart
  function createChart(chartData) {
    var $salesChart = $("#sales-chart");
    var salesChart = new Chart($salesChart, {
      type: "line",
      data: {
        labels: chartData.labels,
        // labels: ["JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"],
        datasets: [
          {
            backgroundColor: "#007bff",
            borderColor: "#007bff",
            data: chartData.values,
          },
          {
            backgroundColor: "#ced4da",
            borderColor: "#ced4da",
            data: chartData.values,
          },
        ],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          mode: mode,
          intersect: intersect,
        },
        hover: {
          mode: mode,
          intersect: intersect,
        },
        legend: {
          display: false,
        },
        scales: {
          yAxes: [
            {
              // display: false,
              gridLines: {
                display: true,
                lineWidth: "4px",
                color: "rgba(0, 0, 0, .2)",
                zeroLineColor: "transparent",
              },
              ticks: $.extend(
                {
                  beginAtZero: true,
                  suggestedMax: chartData.total_count,

                  // Include a dollar sign in the ticks
                  // callback: function (value) {
                  //   if (value >= 1000) {
                  //     value /= 1000;
                  //     value += "k";
                  //   }

                  //   return "$" + value;
                  // },
                },
                ticksStyle
              ),
            },
          ],
          xAxes: [
            {
              display: true,
              gridLines: {
                display: false,
              },
              ticks: ticksStyle,
            },
          ],
        },
      },
    });

      var $visitorsChart = $("#visitors-chart");
      // eslint-disable-next-line no-unused-vars
      var visitorsChart = new Chart($visitorsChart, {
        data: {
          // labels: ["18th", "20th", "22nd", "24th", "26th", "28th", "30th"],
          labels: chartData.labels,
          datasets: [
            {
              type: "line",
              // data: [100, 120, 170, 167, 180, 177, 160],
              data: chartData.values,
              backgroundColor: "transparent",
              borderColor: "#007bff",
              pointBorderColor: "#007bff",
              pointBackgroundColor: "#007bff",
              fill: false,
              // pointHoverBackgroundColor: '#007bff',
              // pointHoverBorderColor    : '#007bff'
            },
            {
              type: "line",
              // data: [60, 80, 70, 67, 80, 77, 100],
              data: chartData.values,
              backgroundColor: "tansparent",
              borderColor: "#ced4da",
              pointBorderColor: "#ced4da",
              pointBackgroundColor: "#ced4da",
              fill: false,
              // pointHoverBackgroundColor: '#ced4da',
              // pointHoverBorderColor    : '#ced4da'
            },
          ],
        },
        options: {
          maintainAspectRatio: false,
          tooltips: {
            mode: mode,
            intersect: intersect,
          },
          hover: {
            mode: mode,
            intersect: intersect,
          },
          legend: {
            display: false,
          },
          scales: {
            yAxes: [
              {
                // display: false,
                gridLines: {
                  display: true,
                  lineWidth: "4px",
                  color: "rgba(0, 0, 0, .2)",
                  zeroLineColor: "transparent",
                },
                ticks: $.extend(
                  {
                    beginAtZero: true,
                    // suggestedMax: 200,
                    suggestedMax: chartData.total_count,
                  },
                  ticksStyle
                ),
              },
            ],
            xAxes: [
              {
                display: true,
                gridLines: {
                  display: false,
                },
                ticks: ticksStyle,
              },
            ],
          },
        },
      });
  }
});

// lgtm [js/unused-local-variable]
