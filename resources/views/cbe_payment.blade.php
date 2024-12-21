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
            margin: 20px;
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
    
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
    
        select {
            appearance: none; /* Removes default browser styling */
            background-color: #fff;
            cursor: pointer;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
    
        select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
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
    
        .hidden {
            display: none;
        }
    
        .message {
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <div id="app">
        <h1 style="text-align: center;">Virtual Equb</h1>
        @if (!empty($error))
            <div style="color: red; text-align: center; margin-bottom: 20px;">
                {{ $error }}
            </div>
        @endif
        <!-- Payment Form -->
        @if (empty($error))
        <form id="cbePaymentForm" action="{{ route('cbe.initialize') }}" method="POST">
            @csrf
            <label for="equb">Select Equb:</label>
            <select name="equb_id" id="equb" required>
                <option value="">Select an Equb</option>
                @foreach ($equbs as $item)
                    <option value="{{ $item->id }}" data-amount="{{ $item->amount }}">
                        {{ $item->equbType->name }}, {{ $item->amount }} {{'ETB'}}
                    </option>
                @endforeach
            </select>
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" placeholder="Enter amount (ETB)" required>

            {{-- <label for="tillCode">Till Code:</label>
            <input type="text" name="tillCode" id="tillCode" value="4002415" readonly>
             --}}
            <!-- Hidden input for token -->
            <input type="hidden" name="token" id="token" value="{{ $token }}">
            <input type="hidden" name="phone" value="{{ $phone }}">
            <button type="submit">Pay Now</button>
        </form>
        @endif

        <!-- Success or Error Message -->
        <div class="message" id="messageBox"></div>
    </div>

    <script>
        // Update the amount field when an Equb is selected
        document.getElementById('equb').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const equbAmount = selectedOption.getAttribute('data-amount');
            document.getElementById('amount').value = equbAmount;
        });
    
        // Handle form submission
        document.getElementById('cbePaymentForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the form from submitting the traditional way
            const form = event.target;
    
            // Collect the form data
            const formData = new FormData(form);
            const paymentData = {
                amount: formData.get('amount'),
                equb_id: formData.get('equb_id'),
                token: formData.get('token'),
                phone: formData.get('phone'),
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
                console.log('Processed Payload', data); // Log the full payload
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
