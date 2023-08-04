<?php
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the image URL from the form data
    $imageUrl = $_POST["image_url"];

    // Prepare the command to run the Python script
    $command = "python test1.py " . escapeshellarg($imageUrl);

    // Execute the Python script and get the output
    $output = shell_exec($command);

    // Output the response as JSON
    header("Content-Type: application/json");
    echo json_encode(["caption" => $output]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ... (rest of your head section) ... -->
</head>

<body>
    <!-- ... (rest of your HTML code) ... -->
    <form method="post" action="">
        <label for="imageUrl">Image URL:</label>
        <input type="text" name="image_url" id="imageUrl" placeholder="Enter Image URL">
        <input type="submit" value="Generate">
    </form>
    <p id="resultCaption" class="caption-text green-border"></p>
    <!-- ... (rest of your HTML code) ... -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const resultCaption = document.getElementById("resultCaption");

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent the default form submission behavior

                // Get the image URL from the input field
                const imageUrl = document.getElementById("imageUrl").value;

                // Create a new XMLHttpRequest object
                const xhr = new XMLHttpRequest();

                // Configure the AJAX request
                xhr.open("POST", "", true); // Send the POST request to the same PHP file
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Use application/x-www-form-urlencoded since we are sending form data

                // Handle the response from the server
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Parse the JSON response from the server
                        try {
                            const response = JSON.parse(xhr.responseText);

                            // Update the content of the <p> tag with the generated caption
                            resultCaption.textContent = response.caption;

                            // Log the JSON response to the browser console
                            console.log("Response:", response);
                        } catch (error) {
                            console.error("Error parsing JSON response:", error);
                            console.log("Response text:", xhr.responseText);
                        }
                    } else {
                        console.error("Request failed. Status:", xhr.status);
                    }
                };

                // Prepare the form data to be sent in the request body
                const formData = new FormData();
                formData.append("image_url", imageUrl);

                // Send the AJAX request
                xhr.send(formData);
            });
        });
    </script>
</body>

</html>