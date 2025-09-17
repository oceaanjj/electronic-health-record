<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Patient Activities of daily living</title>
    @vite(['./resources/css/ivs-and-lines.css'])
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
                <th class="title">IV FLUID</th>
                <th class="title">RATE</th>
                <th class="title">SITE</th>
                <th class="title">STATUS</th>
            </tr>

            <tr>
                <td><input type="text" placeholder="iv fluid"></td>
                <td><input type="text" placeholder="rate"></td>
                <td><input type="text" placeholder="site"></td>
                <td><input type="text" placeholder="status"></td>
            </tr>





        </table>
    </div>

    <div class="buttons">
        <button class="btn" type="submit">Submit</button>
    </div>


</body>

</html>