<?php
function demux_1_to_2($data, $select) {
    // Validate inputs
    if (!in_array($data, [0, 1]) || !in_array($select, [0, 1])) {
        throw new InvalidArgumentException("Inputs must be binary (0 or 1).");
    }

    // Compute outputs
    $y0 = (1 - $select) * $data; // Y0 = ~S * D
    $y1 = $select * $data;       // Y1 = S * D

    return [$y0, $y1];
}

// Main program
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = isset($_POST['data']) ? (int)$_POST['data'] : null;
    $select = isset($_POST['select']) ? (int)$_POST['select'] : null;

    try {
        list($y0, $y1) = demux_1_to_2($data, $select);
        echo "Outputs: Y0 = $y0, Y1 = $y1";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>1:2 Demultiplexer Simulation</title>
</head>
<body>
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
        .output {
            color: #2f855a;
            margin-top: 20px;
            font-size: 18px;
        }
        .error {
            color: #e53e3e;
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
    <h1>1:2 Demultiplexer Simulation</h1>
    <form method="post">
        <label for="data">Input Signal (D: 0 or 1):</label>
        <input type="number" id="data" name="data" min="0" max="1" required>
        <br><br>
        <label for="select">Select Line (S: 0 or 1):</label>
        <input type="number" id="select" name="select" min="0" max="1" required>
        <br><br>
        <button type="submit">Compute Outputs</button>
    </form>
</body>
</html>
<?php
}
?>
