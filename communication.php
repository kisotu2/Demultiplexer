<?php
function addParityBit($message) {
    // Calculate parity bit (even parity)
    $countOnes = substr_count($message, '1');
    $parityBit = $countOnes % 2 == 0 ? '0' : '1';
    return $message . $parityBit;
}

function simulateTransmission($encodedMessage, $errorProbability = 0.1) {
    // Introduce errors based on probability
    $transmittedMessage = '';
    for ($i = 0; $i < strlen($encodedMessage); $i++) {
        $bit = $encodedMessage[$i];
        // Randomly flip the bit based on error probability
        if (mt_rand() / mt_getrandmax() < $errorProbability) {
            $bit = $bit == '1' ? '0' : '1';
        }
        $transmittedMessage .= $bit;
    }
    return $transmittedMessage;
}

function checkParity($receivedMessage) {
    // Extract original message and parity bit
    $originalMessage = substr($receivedMessage, 0, -1);
    $parityBit = $receivedMessage[-1];
    $countOnes = substr_count($originalMessage, '1');
    $expectedParity = $countOnes % 2 == 0 ? '0' : '1';
    return $parityBit === $expectedParity ? [$originalMessage, true] : [$originalMessage, false];
}

// Main Program
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputMessage = isset($_POST['message']) ? $_POST['message'] : null;

    if ($inputMessage && preg_match('/^[01]+$/', $inputMessage)) {
        // Encode the message
        $encodedMessage = addParityBit($inputMessage);

        // Simulate transmission
        $transmittedMessage = simulateTransmission($encodedMessage);

        // Check parity at the receiver
        list($decodedMessage, $isValid) = checkParity($transmittedMessage);

        // Display results
        echo "<div class='result'>
            <p>Input Message: $inputMessage</p>
            <p>Encoded Message (with parity): $encodedMessage</p>
            <p>Transmitted Message: $transmittedMessage</p>
            <p>Decoded Message: $decodedMessage</p>
            <p>Status: " . ($isValid ? 'Valid (No Error Detected)' : 'Error Detected') . "</p>
        </div>";
    } else {
        echo "<div class='error'>Error: Please enter a valid binary message (e.g., 1010).</div>";
    }
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communication System Simulation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #5a67d8;
            margin-top: 20px;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin: 20px auto;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input {
            padding: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        button {
            background-color: #5a67d8;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #434190;
        }
        .result, .error {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
            text-align: left;
        }
        .result {
            background-color: #e6fffa;
            color: #2c7a7b;
        }
        .error {
            background-color: #ffe6e6;
            color: #cc0000;
        }
    </style>
</head>
<body>
    <h1>Communication System Simulation</h1>
    <form method="post">
        <label for="message">Enter Binary Message (e.g., 1010):</label>
        <input type="text" id="message" name="message" required pattern="[01]+">
        <button type="submit">Simulate</button>
    </form>
</body>
</html>
<?php
}
?>
