<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CBE Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
        }

        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            max-width: 400px;
            margin: auto;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            display: none;
        }
    </style>
</head>
<body>
    <div id="app">
        <h1 style="text-align: center;">CBE Birr Payment</h1>

        <!-- Payment Form -->
        <form id="cbePaymentForm" action="{{ route('cbe.initialize') }}" method="POST">
            @csrf
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" placeholder="Enter amount (ETB)" required>

            <label for="transactionId">Transaction ID:</label>
            <input type="text" name="transactionId" id="transactionId" placeholder="Enter transaction ID" required>

            <label for="tillCode">Till Code:</label>
            <input type="text" name="tillCode" id="tillCode" value="4002415" readonly>

            <button type="submit">Pay Now</button>
        </form>

        <!-- Success or Error Message -->
        <div class="message" id="messageBox"></div>
    </div>

    <script>
        document.getElementById('cbePaymentForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the form from submitting the traditional way
            const form = event.target;

            // Collect the form data
            const formData = new FormData(form);
            const paymentData = {
                amount: formData.get('amount'),
                transactionId: formData.get('transactionId'),
                tillCode: formData.get('tillCode'),
                callbackUrl: "{{ route('cbe.callback') }}" // Ensure this route exists in your Laravel application
            };

            // Perform AJAX request to initialize payment
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                const messageBox = document.getElementById('messageBox');
                if (data.status === 'success') {
                    // Send the payment token to CBE Mini App via WebView
                    if (window.myJsChannel) {
                        window.myJsChannel.postMessage(data.token); // Replace with the actual token field
                        messageBox.innerHTML = 'Redirecting to payment...';
                        messageBox.style.display = 'block';
                    } else {
                        messageBox.innerHTML = 'Payment initialized successfully, but the CBE Mini App communication channel is not available.';
                        messageBox.style.display = 'block';
                        console.error("CBE Mini App communication channel is not available.");
                    }
                } else {
                    // Show error message
                    messageBox.innerHTML = 'Error: ' + data.message;
                    messageBox.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageBox = document.getElementById('messageBox');
                messageBox.innerHTML = 'An unexpected error occurred. Please try again.';
                messageBox.style.display = 'block';
            });
        });
    </script>
</body>
</html>
