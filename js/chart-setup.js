document.addEventListener('DOMContentLoaded', function() {
  const items = document.querySelectorAll('.horizontal-item');
  items.forEach(function(item) {
    // Generate random color
    const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16);
    item.style.backgroundColor = randomColor;
  });

  // Pie chart data
  const pieData = {
    labels: ['Label 1', 'Label 2', 'Label 3'],
    datasets: [{
      label: 'Data Pencarian',
      data: [40, 60, 100],
      backgroundColor: [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)'
      ],
      borderColor: [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)'
      ],
      borderWidth: 1
    }]
  };

  // Options for pie chart
  const pieOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
      },
      tooltip: {
        callbacks: {
          label: function(tooltipItem) {
            return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2) + '%';
          }
        }
      }
    }
  };

  // Get the context of the canvas element we want to select
  const pieChart = document.getElementById('pieChart').getContext('2d');

  // Create the pie chart
  new Chart(pieChart, {
    type: 'pie',
    data: pieData,
    options: pieOptions
  });
});

// Bar chart data
const barData = {
  labels: ['Bar 1', 'Bar 2', 'Bar 3'],
  datasets: [{
    label: 'Data Bar',
    data: [80, 50, 70],
    backgroundColor: [
      'rgba(255, 159, 64, 0.7)',
      'rgba(75, 192, 192, 0.7)',
      'rgba(153, 102, 255, 0.7)'
    ],
    borderColor: [
      'rgba(255, 159, 64, 1)',
      'rgba(75, 192, 192, 1)',
      'rgba(153, 102, 255, 1)'
    ],
    borderWidth: 1
  }]
};

// Options for bar chart
const barOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      callbacks: {
        label: function(tooltipItem) {
          return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
        }
      }
    }
  }
};

// Get the context of the canvas element for bar chart
const barChart = document.getElementById('barChart').getContext('2d');

// Create the bar chart
new Chart(barChart, {
  type: 'bar',
  data: barData,
  options: barOptions
});


