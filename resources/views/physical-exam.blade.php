<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Physical Exam</title>
    @vite(['resources/css/physical-exam-style.css'])
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


        <table>
                    <tr>
                        <th class="title">SYSTEM</th>
                        <th class="title">FINDINGS</th>
                        <th class="title">ALERTS</th>
                    </tr>

                    <tr>
                        <th class="system">GENERAL APPEARANCE</th>
                        <td><textarea placeholder="Enter General Appearance findings"></textarea></td>
                        <td><input type="text" name="general_appearance_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">SKIN</th>
                        <td><textarea placeholder="Enter Skin findings"></textarea></td>
                        <td><input type="text" name="skin_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">EYES</th>
                        <td><textarea placeholder="Enter Eyes findings"></textarea></td>
                        <td><input type="text" name="eyes_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">ORAL CAVITY</th>
                        <td><textarea placeholder="Enter Oral Cavity findings"></textarea></td>
                        <td><input type="text" name="oral_cavity_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">CARDIOVASCULAR</th>
                        <td><textarea placeholder="Enter Cardiovascular findings"></textarea></td>
                        <td><input type="text" name="cardiovascular_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">ABDOMEN</th>
                        <td><textarea placeholder="Enter Abdomen findings"></textarea></td>
                        <td><input type="text" name="abdomen_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">EXTREMITIES</th>
                        <td><textarea placeholder="Enter Extremities findings"></textarea></td>
                        <td><input type="text" name="extremities_alerts" placeholder="alerts"></td>
                    </tr>



                    <tr>
                        <th class="system">NEUROLOGICAL</th>
                        <td><textarea placeholder="Enter Neurological findings"></textarea></td>
                        <td><input type="text" name="neurological_alerts" placeholder="alerts"></td>
                    </tr>
            
                   
        </table>
    </div>

    <div class="btn">
        <button type="submit">Submit</button>
    </div>


    <div class="cdss-btn">
        <a href="#" class="btn">CDSS</a>
    </div>
    
</body>
</html>