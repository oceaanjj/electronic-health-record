# Description

Para 'to sa mga gagamit ng SweetAlerts

# Setups
   
   !! MAKE SURE NA LAGING MAY GANITO SA BLADE FILE NA GAGAMITAN NIYO !!
   @vite(['resources/css/app.css', 'resources/js/app.js'])
# Functions

### 1. `showSuccess(message, title)`
### 2. `showError(message, title)`
### 3. `showWarning(message, title)`
### 4. `showInfo(message, title)`
### 5. `showConfirm(message, title)`
### 6. `showLoginRequired()`

# Usage Example 

# Option 1

Puwede siyang gamitin onclick like this:
    ```
    <button onclick="showSuccess('Patient saved!')">Save</button>
    ```

# Option 2

Or Puwede naman siyang serve side like gagamit ng "script" tag like this:
    ``` 
    @if(session('success'))
    <script>showSuccess('{{ session('success') }}');</script>
    @endif
    ```

# Option 3

Kung trip niyo naman na may js ng konti puwede din namang ganito (more validation naman itu)

    ```
    <button onclick="confirmDelete()">Delete</button>
    <script>
        function confirmDelete() {
            showConfirm('Delete this patient?').then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                 }
            });
        }
    </script>
    ```
