<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="stylesheet" href="caption.css" />
</head>
<!-- https://github.com/Gokullal/zebodo/blob/main/components/addbanner.php -->

<body>

  <div class="body">
    <div class="main">
      <div class="upload">
        <h1>Improving Image Captioning Using ChatGPT</h1>
        <form method="POST" id="upload-form" enctype="multipart/form-data" target="upload-target">
          <div class="form-item">
            <div class="choose-body" id="choose">
              <label class="choose-img-btn" for="imgUpload">Choose Image</label>
              <input type="file" id="imgUpload" accept="image/*" name="image" />
            </div>
            <div id="imageContainer"></div>
            <script>
              document
                .getElementById("imgUpload")
                .addEventListener("change", function(event) {
                  const imgUpload = event.target;
                  const imageContainer =
                    document.getElementById("imageContainer");

                  if (imgUpload.files.length > 0) {
                    const file = imgUpload.files[0];
                    const reader = new FileReader();

                    reader.onload = function() {
                      const imageDataURL = reader.result;
                      imageContainer.innerHTML = `<img src="${imageDataURL}" alt="Uploaded Image">`;
                      document
                        .getElementById("choose")
                        .style.setProperty("display", "none");
                      //https://stackoverflow.com/questions/5191478/changing-element-style-attribute-dynamically-using-javascript
                    };

                    reader.readAsDataURL(file);
                  } else {
                    imageContainer.innerHTML = "";
                  }
                });
            </script>
          </div>
          <input type="submit" class="input-item button blue" value="Upload" name="submit" />
        </form>
        <iframe name="upload-target" style="margin:0 auto; height:fit-content; width:fit-content;display:none;"></iframe>
        <p id="uploadStatus" style="margin:0 auto;"></p>
        <a href="javascript:void(0);" class="button green" id="generate">Generate</a>
      </div>

      <!-- https://stackoverflow.com/questions/1265887/call-javascript-function-on-hyperlink-click -->
      <script>
        var el = document.getElementById('generate');
        el.onclick = getdata;
        const apiKey = "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";
        const apiUrl = "https://api.openai.com/v1/chat/completions";

        aiText = "A person holding a knife"

        function getdata() {

          document.getElementById('aiModel').innerText = aiText

          // chat Gpt 
          const message = "The caption of the image is a " + aiText + "is it danger";
          sendMessage(message);
          async function sendMessage(message) {
            // addMessageToChat("You", message);

            const response = await fetch(apiUrl, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${apiKey}`,
              },
              body: JSON.stringify({
                messages: [{
                  role: "user",
                  content: message
                }],
                model: "gpt-3.5-turbo", // Specify the model parameter
              }),
            });

            const data = await response.json();

            if (data.choices && data.choices.length > 0) {
              const reply = data.choices[0].message.content;
              // addMessageToChat("ChatGPT", reply);
              document.getElementById('chatGPT').innerText = reply;
            } else {
              // addMessageToChat(
              //   "ChatGPT",
              //   "Sorry, I could not generate a response."
              // );
              document.getElementById('chatGPT').innerText = "error";
            }
          }

          // function addMessageToChat(role, message) {
          //   const messageElement = document.createElement("div");
          //   messageElement.classList.add("message");
          //   // messageElement.textContent = `${role}: ${message}`;
          //   // document.getElementById('chatGPT').innerText = reply;
          //   chatContainer.appendChild(messageElement);
          //   chatContainer.scrollTop = chatContainer.scrollHeight;
          // }
        }
      </script>
      <div class="caption">
        <div class="caption-item">
          <label for=""> The AI model</label>
          <p class="caption-text green-border" id="aiModel"></p>
        </div>
        <div class="caption-item">
          <label for=""> ChatGPT</label>
          <p class="caption-text blue-border" id="chatGPT"></p>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<?php
include('connections.php');
if (isset($_POST['submit'])) {

  if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

    // Check if file was uploaded without errors

    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["image"]["name"];
    $filetype = $_FILES["image"]["type"];
    $filesize = $_FILES["image"]["size"];


    // Verify file extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");

    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if ($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");

    // Verify MYME type of the file
    if (in_array($filetype, $allowed)) {
      // Check whether file exists before uploading it
      $target = "../test-images/";
      $tar = "http://localhost/image/test-images/";
      if (file_exists($target . $filename)) {

        echo $filename . " is already exists.";
      } else {
        move_uploaded_file($_FILES["image"]["tmp_name"], $target . $filename);
        $s = $tar . $filename;
        $sql = "INSERT INTO imgUpload (image) VALUES('$s')";

        if (mysqli_query($conn, $sql)) {
          echo (" <script> document.getElementById('uploadStatus').innerText = 'Record inserted successfully'
          </script>");
        } else {
          echo "Could not insert record: " . mysqli_error($conn);
        }

        mysqli_close($conn);
      }
    } else {
?>
      <script>
        document.getElementById('uploadStatus').innerText = 'Error: There was a problem uploading your file. Please try again.'
      </script>
<?php
    }
  } else {
    echo "Error: " . $_FILES["image"]["error"];
  }
}
?>