<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Illness Record</title>
    @vite(['./resources/css/medical-history-style.css'])
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
          <th rowspan="2" class="title">PRESENT ILLNESS</th>
          <th>NAME</th>
          <th>DESCRIPTION</th>
          <th>MEDICATION</th>
          <th>DOSAGE</th>
          <th>SIDE EFFECT</th>
          <th>COMMENT</th>
          
        </tr>
   
        <tr>
          <td><input type="text" placeholder="Enter Illness Name"></td>
          <td><textarea placeholder="Enter Description"></textarea></td>
          <td><textarea placeholder="Enter Medication"></textarea></td>
          <td><textarea placeholder="Enter Dosage"></textarea></td>
          <td><textarea placeholder="Enter Side Effect"></textarea></td>
          <td><textarea placeholder="Enter Comment"></textarea></td>
        </tr>





        <tr>
          <th rowspan="2" class="title">PAST MEDICAL / SURGICAL</th>
        </tr>
   
        <tr>
          <td><input type="text" placeholder="Enter Illness Name"></td>
          <td><textarea placeholder="Enter Description"></textarea></td>
          <td><textarea placeholder="Enter Medication"></textarea></td>
          <td><textarea placeholder="Enter Dosage"></textarea></td>
          <td><textarea placeholder="Enter Side Effect"></textarea></td>
          <td><textarea placeholder="Enter Comment"></textarea></td>
        </tr>




        <tr>
          <th rowspan="2" class="title">KNOWN CONDITION OR ALLERGIES</th>
        </tr>
   
        <tr>
          <td><input type="text" placeholder="Enter Illness Name"></td>
          <td><textarea placeholder="Enter Description"></textarea></td>
          <td><textarea placeholder="Enter Medication"></textarea></td>
          <td><textarea placeholder="Enter Dosage"></textarea></td>
          <td><textarea placeholder="Enter Side Effect"></textarea></td>
          <td><textarea placeholder="Enter Comment"></textarea></td>
        </tr>

        

        <tr>
          <th rowspan="2" class="title">VACCINATION & IMMUNIZATION</th>
        </tr>
   
        <tr>
          <td><input type="text" placeholder="Enter Illness Name"></td>
          <td><textarea placeholder="Enter Description"></textarea></td>
          <td><textarea placeholder="Enter Medication"></textarea></td>
          <td><textarea placeholder="Enter Dosage"></textarea></td>
          <td><textarea placeholder="Enter Side Effect"></textarea></td>
          <td><textarea placeholder="Enter Comment"></textarea></td>
        </tr>





        <tr>
          <th colspan="7" class="title">DEVELOPMENTAL HISTORY</th>
        </tr>

        <tr>
          <th rowspan="2" class="title">GROSS MOTOR</th>
          <td colspan="6">FINDINGS</td>
        </tr>
   
        <tr>
          <td colspan="6"><textarea placeholder="Enter Gross Motor findings"></textarea></td>
        </tr>



        <tr>
          <th rowspan="2" class="title">FINE MOTOR</th>
          <td colspan="6">FINDINGS</td>
        </tr>
   
        <tr>
          <td colspan="6"><textarea placeholder="Enter Fine Motor findings"></textarea></td>
        </tr>


        <tr>
          <th rowspan="2" class="title">LANGUAGE</th>
          <td colspan="6">FINDINGS</td>
        </tr>
   
        <tr>
          <td colspan="6"><textarea placeholder="Enter Language findings"></textarea></td>
        </tr>


        <tr>
          <th rowspan="2" class="title">COGNITIVE</th>
          <td colspan="6">FINDINGS</td>
        </tr>
   
        <tr>
          <td colspan="6"><textarea placeholder="Enter Cognitive findings"></textarea></td>
        </tr>


        <tr>
          <th rowspan="2" class="title">SOCIAL</th>
          <td colspan="6">FINDINGS</td>
        </tr>
   
        <tr>
          <td colspan="6"><textarea placeholder="Enter Social findings"></textarea></td>
        </tr>

        


    </table>
  </div>

    <div class="btn">
        <button type="submit">Submit</button>
    </div>


</body>
</html>
