<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Registration</title>
    <link rel="stylesheet" href="css/patient-registration.css">
</head>

<body>
   <div class="header">PATIENT REGISTARTION</div>

  <div class="form-container">
    <form action="#" method="POST">
      
      <div class="form-row">        
          <div class="form-group">
            <label>Name</label>
            <input type="text" placeholder="Enter patient name">
          </div>
            
          <div class="form-group">
            <label>Age</label>
            <input type="number" placeholder="Enter age">
          </div>

          <div class="form-group">
            <label>Sex</label>
            <select>
              <option>Select sex</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
      </div>

      <div class="form-row">
          <div class="form-group">
            <label>Address</label>
            <input type="text" placeholder="Enter complete address">
          </div>

          <div class="form-group">
            <label>Birth Place</label>
            <input type="text" placeholder="Enter birth place">
          </div>
      </div>

      <div class="form-row">
          <div class="form-group">
            <label>Religion</label>
            <input type="text" placeholder="Enter religion">
          </div>

          <div class="form-group">
            <label>Ethnicity</label>
            <input type="text" placeholder="Enter ethnicity">
          </div>
      </div>

      <div class="form-row">
          <div class="form-group" style="flex: 1;">
            <label>Chief of Compliants</label>
            <textarea rows="3" placeholder="Enter chief compliants"></textarea>
          </div>
      </div>

      <div class="form-row">
          <div class="form-group">
            <label>Admission Date</label>
            <input type="date">
          </div>
          <div class="form-group">
            <label>Room No.</label>
            <input type="text" placeholder="Enter room number">
          </div>
          <div class="form-group">
            <label>Bed No.</label>
            <input type="text" placeholder="Enter bed number">
          </div>
      </div>

    </form>
  </div>

  <div class="header">EMERGENCY CONTACT</div>
      <div class="form-row">        
          <div class="form-group">
            <label>Name</label>
            <input type="text" placeholder="Enter name">
          </div>
            
          <div class="form-group">
            <label>Age</label>
            <input type="text" placeholder="Enter relationship to patient">
          </div>

          <div class="form-group">
            <label>Contact Number</label>
            <input type="text" placeholder="Enter number">
          </div>

          <div class="form-group">
            <label>Date of Admission</label>
            <input type="date">
          </div>
      </div>

</body>
</html>
