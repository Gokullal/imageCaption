<!-- caption_generator.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Caption Generator</title>
</head>

<body>
    <form method="post">
        <label for="image_url">Image URL:</label>
        <input type="text" name="image_url" id="image_url" value="https://www.google.com/url?sa=i&url=https%3A%2F%2Fasia.olympus-imaging.com%2Fproduct%2Fdslr%2Fem1mk3%2Fsample.html&psig=AOvVaw1uZS-WZH9TgxpE8vwufZs_&ust=1690492217057000&source=images&cd=vfe&opi=89978449&ved=0CBAQjRxqFwoTCJjzqbKkrYADFQAAAAAdAAAAABAE" />
        <input type="submit" name="submit" value="Generate Captions" />
    </form>
    <br />

    <?php
    if (isset($_POST['submit'])) {
        $image_url = $_POST['image_url'];

        // Call the Python script with the provided image URL
        $command = "python3 caption_generator.py \"" . $image_url . "\"";
        $output = shell_exec($command);

        // Display the output
        echo "<pre>$output</pre>";
    }
    ?>
</body>

</html>