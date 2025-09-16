<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Medical Administration</title>
<<<<<<< HEAD
    @vite(['resources/css/#.css'])
=======
    @vite(['./resources/css/medication-administration.css'])
>>>>>>> fb9bfa54b07a5bd3ad40c06dfd34fc7e0d04f8e6
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

        <div class="section-bar">
            <label for="date">DATE :</label>
            <input type="date" id="date" name="date">
        </div>


        <table>
          <tr>
              <th class="title">MEDICATION</th>
              <th class="title">DOSE</th>
              <th class="title">ROUTE</th>
              <th class="title">FREQUENCY</th>
              <th class="title">COMMENTS</th>
              <th class="title">TIME</th>
          </tr>

          <tr>
              <td><input type="text" placeholder="Medication"></td>
              <td><input type="text" placeholder="Dose"></td>
              <td><input type="text" placeholder="Route"></td>
              <td><input type="text" placeholder="Frequency"></td>
              <td><input type="text" placeholder="Comments"></td>
              <th class="time">10:00 AM</th>
          </tr>

          <tr>
              <td><input type="text" placeholder="Medication"></td>
              <td><input type="text" placeholder="Dose"></td>
              <td><input type="text" placeholder="Route"></td>
              <td><input type="text" placeholder="Frequency"></td>
              <td><input type="text" placeholder="Comments"></td>
              <th class="time">2:00 PM</th>
          </tr>

          <tr>
              <td><input type="text" placeholder="Medication"></td>
              <td><input type="text" placeholder="Dose"></td>
              <td><input type="text" placeholder="Route"></td>
              <td><input type="text" placeholder="Frequency"></td>
              <td><input type="text" placeholder="Comments"></td>
              <th class="time">6:00 PM</th>
          </tr>
       </table>
    </div>
  
    <div class="buttons">
        <button class="btn"type="submit">Submit</button>
    </div>

    
</body>
</html>