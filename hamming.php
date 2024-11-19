<?php
function encodeHamming($data) {
    $dataBits = str_split($data);
    $length = count($dataBits);
    $hammingCode = [];
    $parityCount = 0;

    // Add parity bits at positions that are powers of 2
    for ($i = 1, $j = 0; $i <= $length + $parityCount; $i++) {
        if (($i & ($i - 1)) == 0) {
            $hammingCode[$i] = 0; // Temporary value for parity bit
            $parityCount++;
        } else {
            $hammingCode[$i] = $dataBits[$j++];
        }
    }

    // Calculate parity bits
    foreach (array_keys($hammingCode, 0, true) as $parityIndex) {
        $parity = 0;
        foreach (array_keys($hammingCode) as $index) {
            if (($index & $parityIndex) == $parityIndex) {
                $parity ^= $hammingCode[$index];
            }
        }
        $hammingCode[$parityIndex] = $parity;
    }

    ksort($hammingCode);
    return implode('', $hammingCode);
}

function simulateTransmission($encodedMessage, $errorProbability = 0.1) {
    $transmittedMessage = '';
    for ($i = 0; $i < strlen($encodedMessage); $i++) {
        $bit = $encodedMessage[$i];
        if (mt_rand() / mt_getrandmax() < $errorProbability) {
            $bit = $bit == '1' ? '0' : '1'; // Flip bit
        }
        $transmittedMessage .= $bit;
    }
    return $transmittedMessage;
}

function decodeHamming($receivedMessage) {
    $receivedBits = str_split($receivedMessage);
    $length = count($receivedBits);
    $errorPosition = 0;

    // Recalculate parity bits and identify error position
    foreach (array_keys($receivedBits) as $parityIndex) {
        $parity = 0;
        foreach (array_keys($receivedBits) as $index) {
            if (($index & $parityIndex) == $parityIndex) {
                $parity ^= $receivedBits[$index];
            }
        }
        if ($parity !== 0) {
            $errorPosition += $parityIndex;
        }
    }

    if ($errorPosition > 0) {
        // Correct the error
        $receivedBits[$errorPosition] = $receivedBits[$errorPosition] == '1' ? '0' : '1';
    }

    // Remove parity bits to retrieve original data
    $data = [];
    foreach ($receivedBits as $index => $bit) {
        if (($index & ($index - 1)) != 0) {
            $data[] = $bit;
        }
    }

    return [implode('', $data), $errorPosition];
}

// Main Program
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputMessage = isset($_POST['message']) ? $_POST['message'] : null;

    if ($inputMessage && preg_match('/^[01]+$/', $inputMessage)) {
        // Encode the message using Hamming Code
        $encodedMessage = encodeHamming($inputMessage);

        // Simulate transmission
        $transmittedMessage = simulateTransmission($encodedMessage);

        // Decode and correct errors
        list($decodedMessage, $errorPosition) = decodeHamming($transmittedMessage);

        // Display results
        echo "<div class='result'>
            <p>Input Message: $inputMessage</p>
            <p>Encoded Message (Hamming Code): $encodedMessage</p>
            <p>Transmitted Message: $transmittedMessage</p>
            <p>Decoded Message: $decodedMessage</p>
            <p>Error Position: " . ($errorPosition > 0 ? $errorPosition : "No Error") . "</p>
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
    <title>Hamming Code Simulation</title>
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
    <h1>Hamming Code Simulation</h1>
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
