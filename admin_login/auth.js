        // Define the correct username and password
        var correctUsername = "help@beperfectgroup.in";
        var correctPassword = "Beperfect@#986532";

        // Prompt the user for credentials
        var username = prompt("Enter Username:");
        var password = prompt("Enter Password:");

        // Check if the username and password are correct
        if (username !== correctUsername || password !== correctPassword) {
            // Redirect to login page if incorrect
            alert("Incorrect username or password!");
            window.location.href = "../index.php";
        }

        // JavaScript function to select/deselect all checkboxes
        function toggleSelectAll(source) {
            var checkboxes = document.getElementsByName('order_ids[]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }