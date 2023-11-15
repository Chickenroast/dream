<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

<header class="header">
    <h1>Currency converter</h1>
</header>

<!-- ----------------------API----------------------- -->
<?php
function makeApiRequest($url) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: currency-converter18.p.rapidapi.com",
            "X-RapidAPI-Key: cc4f6fdbedmshf3aec4598cce062p1e808bjsn3522ff752e7a"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return false;
    } else {
        return json_decode($response, true);
    }
}

$currencies = makeApiRequest("https://currency-converter18.p.rapidapi.com/api/v1/supportedCurrencies");

?>

<form class="form" method="get" id="currency-form">         
    <div class="form-group">
        <div class="from">
        <label>From</label>
        <select name="from_currency">
            <?php
            foreach ($currencies as $currencyInfo) {
                $currencyCode = $currencyInfo['symbol'];
                $currencyName = $currencyInfo['name'];
                echo "<option value=\"$currencyCode\">$currencyName</option>";
            }
            ?>
        </select>
        </div>
        
        <div class="amount">
        <label>Amount</label>
        <input type="text" placeholder="Currency" name="amount" id="amount" />
        </div>

        <div class="to">
        <label>To</label>
        <select name="to_currency">
            <?php
            foreach ($currencies as $currencyInfo) {
                $currencyCode = $currencyInfo['symbol'];
                $currencyName = $currencyInfo['name'];
                echo "<option value=\"$currencyCode\">$currencyName</option>";
            }
            ?>
        </select>
        </div>

        <button type="submit" name="convert" id="convert">Convert</button>
    </div>
    <?php
    if ($currencies !== false) {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["convert"])) {
        $fromCurrency = $_GET["from_currency"];
        $toCurrency = $_GET["to_currency"];
        $amount = $_GET["amount"];

        $conversionApiUrl = "https://currency-converter18.p.rapidapi.com/api/v1/convert?from=$fromCurrency&to=$toCurrency&amount=$amount";

        $conversionResult = makeApiRequest($conversionApiUrl);

        if ($conversionResult !== false) {
            $convertedAmount = number_format($conversionResult['result']['convertedAmount'], 2, '.', '');
            echo "<p class='conversion-result'>Conversion Result: " . htmlspecialchars($currencyCode .' '. $convertedAmount, ENT_QUOTES, 'UTF-8') . "</p>";
        } else {
            echo "<p class='conversion-result'>Error in conversion</p>";
        }
    }
}
?>
</form>

<footer>
    <!-- Your footer content goes here -->
</footer>

</body>
</html>