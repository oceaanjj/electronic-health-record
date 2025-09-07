<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Admission Form</title>
</head>
<body>
    <h2>Patient Admission Form</h2>

    <form action="#" method="POST">
        <table border="1" cellpadding="10">
            <tr>
                <td>Last Name:</td>
                <td><input type="text" name="lastname" required></td>
            </tr>
            <tr>
                <td>First Name:</td>
                <td><input type="text" name="firstname" required></td>
            </tr>
            <tr>
                <td>Middle Name:</td>
                <td><input type="text" name="middlename"></td>
            </tr>
            <tr>
                <td>Suffix (optional):</td>
                <td><input type="text" name="suffix"></td>
            </tr>
            <tr>
                <td>Age:</td>
                <td><input type="number" name="age" min="0"></td>
            </tr>
            <tr>
                <td>Sex:</td>
                <td>
                    <select name="sex">
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input type="text" name="address" size="40"></td>
            </tr>
            <tr>
                <td>Birthplace:</td>
                <td><input type="text" name="birthplace"></td>
            </tr>
            <tr>
                <td>Religion:</td>
                <td><input type="text" name="religion"></td>
            </tr>
            <tr>
                <td>Ethnicity:</td>
                <td><input type="text" name="ethnicity"></td>
            </tr>
            <tr>
                <td>Chief of Complaint:</td>
                <td><textarea name="complaint" rows="3" cols="30"></textarea></td>
            </tr>
            <tr>
                <td>Admission Date:</td>
                <td><input type="date" name="admission_date"></td>
            </tr>
            <tr>
                <td>Room No.:</td>
                <td><input type="text" name="room_no"></td>
            </tr>
            <tr>
                <td>Bed Number:</td>
                <td><input type="text" name="bed_no"></td>
            </tr>
        </table>
        <br>
        <button type="submit">Submit</button>
    </form>

</body>
</html>
