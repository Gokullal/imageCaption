import requests

function_url = "https://imagecaptiongokul.azurewebsites.net"
image_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFRmWtO1zrO6tt35ewAJOE9NpAb8yiwhbrBWyxjVQCZw&s"

payload = {"image_url": image_url}
headers = {"Content-Type": "application/json"}

try:
    response = requests.post(function_url, json=payload, headers=headers)
    response.raise_for_status()  # Check for any HTTP errors

    if response.status_code == 200:
        try:
            data = response.json()
            print("Function returned:", data)
        except ValueError as e:
            print("Error decoding JSON response:", e)
    else:
        print("Error:", response.text)

except requests.exceptions.RequestException as e:
    print("Request error:", e)
