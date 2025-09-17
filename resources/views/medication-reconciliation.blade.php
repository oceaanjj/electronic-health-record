<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Medication reconciliation</title>
    @vite(['./resources/css/medication-reconciliation.css'])
</head>

<body>
    <div class="container">
        <div class="header">
            <label for="patient">PATIENT NAME :</label>
            <select id="patient" name="patient">
                <option value="">-- Select Patient --</option>
                <option value="Althea Pascua">Jovilyn Esquerra</option>
            </select>
        </div>


        <div class="section">
              <table>
        <tr>
          <th colspan="6">Patient's Current Medication (Upon Admission)</th>
        </tr>
                <tr>
                  <th>Medication</th>
                  <th>Dose</th>
                  <th>Route</th>
                  <th>Frequency</th>
                  <th>Indication</th>
                  <th>Administered During Stay?</th>
                </tr>
                <tr>
                  <td><input type="text" placeholder="Medication"></td>
                  <td><input type="text" placeholder="Dose"></td>
                  <td><input type="text" placeholder="Route"></td>
                  <td><input type="text" placeholder="Frequency"></td>
                  <td><input type="text" placeholder="Indication"></td>
                  <td><input type="text"></td>
                </tr>
              </table>
            </div>
<br>
            <div class="section">
              <table>
        <tr>
          <th colspan="6">Patient's Home Medication (If Any)</th>
        </tr>
                <tr>
                  <th>Medication</th>
                  <th>Dose</th>
                  <th>Route</th>
                  <th>Frequency</th>
                  <th>Indication</th>
                  <th>Discontinued on Admission?</th>
                </tr>
                <tr>
                  <td><input type="text" placeholder="Medication"></td>
                  <td><input type="text" placeholder="Dose"></td>
                  <td><input type="text" placeholder="Route"></td>
                  <td><input type="text" placeholder="Frequency"></td>
                  <td><input type="text" placeholder="Indication"></td>
                  <td><input type="text"></td>
                </tr>
              </table>
            </div>
<br>
            <div class="section">
              <table>
        <tr>
          <th colspan="6">Changes in Medication During Hospitalization</th>
        </tr>
                <tr>
                  <th>Medication</th>
                  <th>Dose</th>
                  <th>Route</th>
                  <th>Frequency</th>
                  <th>Reason for Change</th>
                </tr>
                <tr>
                  <td><input type="text" placeholder="Medication"></td>
                  <td><input type="text" placeholder="Dose"></td>
                  <td><input type="text" placeholder="Route"></td>
                  <td><input type="text" placeholder="Frequency"></td>
                  <td><input type="text"></td>

                </tr>
              </table>
            </div>
    </div>

  
    <div class="buttons">
        <button class="btn"type="submit">Submit</button>
    </div>

    
</body>
</html>