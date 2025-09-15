<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Intake and Output</title>
    @vite(['resources/css/intake-and-output-style.css'])
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

        <div>
            <label for="day">DAY NO :</label>
            <select id="day" name="day">
                <option value="">-- Select number --</option>
                <option value="1">1</option>
            </select>

            <label for="date">DATE :</label>
            <input type="date" id="date" name="date">
        </div>


        <table>
                    <tr>
                        <th class="title">ORAL INTAKE (mL)</th>
                        <th class="title">IV FLUIDS (mL)</th>
                        <th class="title">URINE OUTPUT (mL)</th>
                        <th class="title">Alerts</th>
                    </tr>

                    <tr>
                        <td><input type="text" name="oral intake" placeholder="Oral Intake"></td>
                        <td><input type="text" name="iv fluids" placeholder="IV Fluids"></td>
                        <td><input type="text" name="urine output" placeholder="Urine Output"></td>
                        <td><input type="text" name="alerts" placeholder="Alerts"></td>
                    </tr>
                   
        </table>
    </div>

    <div class="btn">
        <button type="submit">Submit</button>
    </div>


    <div class="cdss-btn">
        <a href="#" class="btn">CDSS</a>
    </div>

    <div class="calculate-btn">
        <a href="#" class="btn">Calculate fluid balance</a>
    </div>
    
</body>
</html>