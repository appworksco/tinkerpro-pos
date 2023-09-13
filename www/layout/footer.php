  <!-- JS -->
  <script type="text/javascript" src="assets/vendor/jquery/jquery.min.js"></script>
  <script type="text/javascript" src="assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>
  <script type="text/javascript" src="assets/vendor/datatables/jquery.dataTables.min.js"></script>

  <script>
    // Initialize Datatable
    $(document).ready(function () {
        $('#searchProductTable').DataTable({
          "bPaginate": false,
          pageLength : 1,
          info : false,
        });
        
    });

    // Render Date
    function renderDate() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      today = mm + '/' + dd + '/' + yyyy;
      var myDate = document.getElementById('dateDisplay');
      myDate.textContent = today;
    }
    renderDate();

    // Render Time
    function renderTime() {
      var currentTime = new Date();
      var diem = "AM";
      var h = currentTime.getHours();
      var m = currentTime.getMinutes();
      var s = currentTime.getSeconds();
      setTimeout('renderTime()',1000);
      if (h == 0) {
        h = 12;
      }
      else if (h == 12) {
        diem = "PM";
      }
      else if (h > 12) {
        h = h - 12;
        diem = "PM";
      }
      if (h < 10) {
        h = "0" + h;
      }
      if (m < 10) {
        m = "0" + m;
      }
      if (s < 10) {
        s = "0" + s;
      }
      var myClock = document.getElementById('clockDisplay');
      myClock.textContent = h + ":" + m + ":" + s + " " + diem;
      myClock.innerText = h + ":" + m + ":" + s + " " + diem;
    }
    renderTime();

    // Barcode
    $("#barcode").on("input", function() {
      $("#counter").text(this.value.length);  
      if (($("#counter").text()) != 0) {
        var transactionNum = $("#transactionNum").val();
        var qty = $("#qty").val();
        var barcode = $("#barcode").val();
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        $.ajax({
          url: 'verify-barcode.php',
          type: 'POST',
          data: {
            transactionNum: transactionNum,
            qty: qty,
            barcode: barcode
          },
          success: function(data) {
            window.location.href = "home.php?first_name=" + firstName + "&last_name=" + lastName;
          }
        });
      }
    });

    // Load data from server
    var i = 0;
    function move() {
      if (i == 0) {
        i = 1;
        var elem = document.getElementById("myBar");
        var width = 1;
        var id = setInterval(frame, 10);
        function frame() {
          if (width >= 100) {
            clearInterval(id);
            i = 0;
          } else {
            width++;
            elem.style.width = width + "%";
          }
          if (width == 100) {
            location.reload();
          }
        }
      }
    }

    // Enter amount 
    document.addEventListener("keyup", function(e) {
      // If F1 is pressed
      if (e.key == 'F1') {
        $("#helpModal").show(); //F1
        $("#searchProductModal").hide(); //F2
        $("#voidProductModal").hide(); //F3
        $("#voidTransactionModal").hide(); //F4
        $("#payCashModal").hide(); //F5
        $("#payLaterModal").hide(); //F6
        $("#withdrawModal").hide(); //F7
        $("#reprintModal").hide(); //F8
        $("#loadDataModal").hide(); //F11
        $("#logoutModal").hide(); //F12
      }
      // If info modal is open
      if ($('#helpModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#helpModal").hide();
        } else {
          // Do nothing
        }
      }
       // If F2 is pressed
       if (e.key == 'F2') {
        $("#helpModal").hide(); //F1
        $("#searchProductModal").show(); //F2
        $("#voidProductModal").hide(); //F3
        $("#voidTransactionModal").hide(); //F4
        $("#payCashModal").hide(); //F5
        $("#payLaterModal").hide(); //F6
        $("#withdrawModal").hide(); //F7
        $("#reprintModal").hide(); //F8
        $("#loadDataModal").hide(); //F11
        $("#logoutModal").hide(); //F12
      }
      // If info modal is open
      if ($('#searchProductModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#searchProductModal").hide();
        } else {
          // Do nothing
        }
      }
      // if F7 is pressed
      if (e.key == 'F7') {
        $("#helpModal").hide(); //F1
        $("#searchProductModal").hide(); //F2
        $("#voidProductModal").hide(); //F3
        $("#voidTransactionModal").hide(); //F4
        $("#payCashModal").hide(); //F5
        $("#payLaterModal").hide(); //F6
        $("#withdrawModal").show(); //F7
        $("#withdrawAmount").focus(); //F7
        $("#reprintModal").hide(); //F8
        $("#loadDataModal").hide(); //F11
        $("#logoutModal").hide(); //F12
      }
      // If withdraw modal is open
      if ($('#withdrawModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#withdrawModal").hide();
        } if (e.key == 'Enter') {
          $("#withdrawModal").hide();
          $("#withdraw").click();
        } else {
          // Do nothing
        }
      }
      // if F8 is pressed
      if (e.key == 'F8') {
        $("#helpModal").hide(); //F1
        $("#searchProductModal").hide(); //F2
        $("#voidProductModal").hide(); //F3
        $("#voidTransactionModal").hide(); //F4
        $("#payCashModal").hide(); //F5
        $("#payLaterModal").hide(); //F6
        $("#withdrawModal").hide(); //F7
        $("#reprintModal").show(); //F8
        $("#transactionNumReprint").focus(); //F8
        $("#loadDataModal").hide(); //F11
        $("#logoutModal").hide(); //F12
      }
      // If withdraw modal is open
      if ($('#reprintModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#reprintModal").hide();
        } if (e.key == 'Enter') {
          $("#reprintModal").hide();
          $("#reprint").click();
        } else {
          // Do nothing
        }
      }
      // if f11 is pressed
      if (e.key == 'F11') {
        $("#helpModal").hide(); //F1
        $("#searchProductModal").hide(); //F2
        $("#voidProductModal").hide(); //F3
        $("#voidTransactionModal").hide(); //F4
        $("#payCashModal").hide(); //F5
        $("#payLaterModal").hide(); //F6
        $("#withdrawModal").hide(); //F7
        $("#reprintModal").hide(); //F8
        $("#loadDataModal").show(); //F10
        $("#loadData").click(); //F111
        $("#logoutModal").hide(); //F12
      }
      // if F12 is pressed
      if (e.key == 'F12') {
        $("#helpModal").hide(); //F1
        $("#searchProductModal").hide(); //F2
        $("#voidProductModal").hide(); //F3
        $("#voidTransactionModal").hide(); //F4
        $("#payCashModal").hide(); //F5
        $("#amount").focus(); //F5
        $("#payLaterModal").hide(); //F6
        $("#withdrawModal").hide(); //F7
        $("#reprintModal").hide(); //F8
        $("#loadDataModal").hide(); //F11
        $("#logoutModal").show(); //F12
      }
      // If logout modal is open
      if ($('#logoutModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#logoutModal").hide();
        } if (e.key == 'Enter') {
          window.location.href = "index.php";
        } else {
          // Do nothing
        }
      }

      // If the total of the transaction is zero then do nothing
      if (total == '0' || total == '0.00' ) {
        // Do nothing
      } else {
         // If ESC is pressed
        if (e.key == 'Escape') {
          $("#helpModal").hide(); //F1
          $("#searchProductModal").hide(); //F2
          $("#voidProductModal").hide(); //F3
          $("#voidTransactionModal").hide(); //F4
          $("#payCashModal").hide(); //F5
          $("#payLaterModal").hide(); //F6
          $("#withdrawModal").hide(); //F7
          $("#reprintModal").hide(); //F8
          $("#loadDataModal").hide(); //F11
          $("#logoutModal").hide(); //F12
        }
        // If F3 is pressed
        if (e.key == 'F3') {
          $("#helpModal").hide(); //F1
          $("#searchProductModal").hide(); //F2
          $("#voidProductModal").show(); //F3
          $("#productId").focus(); //F3
          $("#voidTransactionModal").hide(); //F4
          $("#payCashModal").hide(); //F5
          $("#payLaterModal").hide(); //F6
          $("#withdrawModal").hide(); //F7
          $("#reprintModal").hide(); //F8
          $("#loadDataModal").hide(); //F11
          $("#logoutModal").hide(); //F12
        }
        // If F4 is pressed
        if (e.key == 'F4') {
          $("#helpModal").hide(); //F1
          $("#searchProductModal").hide(); //F2
          $("#voidProductModal").hide(); //F3
          $("#voidTransactionModal").show(); //F4
          $("#payCashModal").hide(); //F5
          $("#payLaterModal").hide(); //F6
          $("#withdrawModal").hide(); //F7
          $("#reprintModal").hide(); //F8
          $("#loadDataModal").hide(); //F11
          $("#logoutModal").hide(); //F12
        }
        // if F5 is pressed
        if (e.key == 'F5') {
          $("#helpModal").hide(); //F1
          $("#searchProductModal").hide(); //F2
          $("#voidProductModal").hide(); //F3
          $("#voidTransactionModal").hide(); //F4
          $("#payCashModal").show(); //F5
          $("#amount").focus(); //F5
          $("#payLaterModal").hide(); //F6
          $("#withdrawModal").hide(); //F7
          $("#reprintModal").hide(); //F8
          $("#loadDataModal").hide(); //F11
          $("#logoutModal").hide(); //F12
        }
        // if F6 is pressed
        if (e.key == 'F6') {
          $("#helpModal").hide(); //F1
          $("#searchProductModal").hide(); //F2
          $("#voidProductModal").hide(); //F3
          $("#voidTransactionModal").hide(); //F4
          $("#payCashModal").hide(); //F5
          $("#payLaterModal").show(); //F6
          $("#payer").focus(); //F6
          $("#withdrawModal").hide(); //F7
          $("#reprintModal").hide(); //F8
          $("#loadDataModal").hide(); //F11
          $("#logoutModal").hide(); //F12
        }
      }
      // If amount modal is open
      if ($('#payCashModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#payCashModal").hide();
        } else {
          // Close app
          if (e.key == 'Escape') {
            close();
          }
        }
      }
      // If change modal is open
      if ($('#changeModal').is(':visible')) {
        if (e.key == 'Enter') {
          $("#changeModal").hide();
          $("#saveTransactionPayCash").click();
        } else {
          // Do nothing
        }
      }
      // If void product modal is open
      if ($('#voidProductModal').is(':visible')) {
        if (e.key == 'Enter') {
          $("#voidProductModal").hide();
          $("#voidProduct").click();
        } else {
          // Do nothing
        }
      }
      // If void transaction modal is open
      if ($('#voidTransactionModal').is(':visible')) {
        if (e.key == 'Enter') {
          $("#voidTransactionModal").hide();
          $("#voidTransaction").click();
        } else {
          // Do nothing
        }
      }
      // If amount modal is open
      if ($('#payLaterModal').is(':visible')) {
        if (e.key == 'Escape') {
          $("#payLaterModal").hide();
        } else {
          // Close app
          if (e.key == 'Escape') {
            close();
          }
        }
      }
      // If pay later modal is open
      if ($('#payLaterInfoModal').is(':visible')) {
        $("#voidTransactionModal").hide();
        $("#payCashModal").hide(); 
        $("#payLaterModal").hide(); 
        if (e.key == 'Enter') {
          $("#payLaterModal").hide();
          $("#saveTransactionPayLater").click();
        } else {
          // Do nothing
        }
      }
    });

    // Open change modal if page reload
    $("#changeModal").show();
    $("#payLaterInfoModal").show();
  </script>
</body>
</html>