// your_script.js

// Get the canvas element
const ctx = document.getElementById("myChart").getContext("2d");

// Initialize the chart
const myChart = new Chart(ctx, {
  type: "line", // You can change the chart type (bar, line, etc.)
  data: {
    labels: months, // X-axis labels
    datasets: [
      {
        label: "Group 1",
        data: groupData.group1,
        borderColor: "rgba(75, 192, 192, 1)", // Line color
        borderWidth: 2, // Line width
        fill: false, // Do not fill area under the line
      },
      {
        label: "Group 2",
        data: groupData.group2,
        borderColor: "rgba(255, 99, 132, 1)",
        borderWidth: 2,
        fill: false,
      },
      // Add more datasets for additional groups
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
