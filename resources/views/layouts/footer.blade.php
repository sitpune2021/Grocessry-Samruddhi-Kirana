        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
          <div class="container-xxl">
            <div
              class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">
                &#169;
                <script>
                  document.write(new Date().getFullYear());
                </script>
                , made with ❤️ by
                <a href="#" target="_blank" class="footer-link">SIT SOLUTIONS PVT LTD</a>
              </div>
            </div>
          </div>
        </footer>
        <!-- / Footer -->

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
  document.addEventListener('DOMContentLoaded', function () {

      const ctx = document.getElementById('warehouseBarChart');

      const warehouseLabels = [
          @foreach($warehouseWise as $row)
              "{{ $row->warehouse->name ?? 'N/A' }}",
          @endforeach
      ];

      const lowStockData = [
          @foreach($warehouseWise as $row)
              {{ $row->total }},
          @endforeach
      ];

      new Chart(ctx, {
          type: 'bar',
          data: {
              labels: warehouseLabels,
              datasets: [{
                  label: 'Low Stock Count',
                  data: lowStockData,
                  backgroundColor: '#dc3545',
                  borderRadius: 6,
                  barThickness: 28
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  legend: { display: false },
                  tooltip: {
                      callbacks: {
                          label: function(context) {
                              return ' Low Stock: ' + context.raw;
                          }
                      }
                  }
              },
              scales: {
                  x: {
                      ticks: {
                          autoSkip: false,
                          maxRotation: 45,
                          minRotation: 30
                      },
                      grid: { display: false }
                  },
                  y: {
                      beginAtZero: true,
                      grid: { color: '#f1f1f1' }
                  }
              }
          }
      });

  });
  </script>
