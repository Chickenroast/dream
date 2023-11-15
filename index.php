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

    <!-- ----------------------TRANSLATION FORM------------------------------------------------ -->
    <?php
    // Initialize translation variables
    $translationResult = null;
    $translatedText = null;

    
    // Check if the translation form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["textToTranslate"]) && isset($_GET["targetLanguage"])) {
        // Get form inputs
        $textToTranslate = $_GET["textToTranslate"];
        $targetLanguage = $_GET["targetLanguage"];

        // Call the translate function
        function translate($from, $to, $text) {
            
            $text = str_replace("'", "&#39;", $text);
            $encodedText = urlencode($text);
        
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" . $from . "&tl=" . $to . "&hl=en-US&dt=t&dt=bd&dj=1&source=icon&tk=310461.310461&q=" . $encodedText,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }

        $translationResult = translate("en", $targetLanguage, $textToTranslate);

        // Process translation result
        $translationData = json_decode($translationResult, true);
        if (isset($translationData['sentences'][0]['trans'])) {
            $translatedText = $translationData['sentences'][0]['trans'];
        } else {
            $translatedText = 'Translation not available';
        }
    }
    ?>

    <form action="index.php" method="get" class="from">
        <label for="textToTranslate">Texte à traduire:</label>
        <input type="text" id="textToTranslate" name="textToTranslate" required>
        <br>

        <label for="targetLanguage">Langue cible:</label>
        <select id="targetLanguage" name="targetLanguage" required>
            <option value="fr">Français</option>
            <option value="es">Espagnol</option>
            <option value="de">Allemand</option>
        </select>
        <br>

        <button type="submit">Traduire</button>
        <?php if (isset($translationData['sentences'][0]['trans'])): ?>
    <p class="return"> <?php echo htmlspecialchars($translationData['sentences'][0]['trans']); ?></p>
<?php else: ?>
    <p class="return">Translation not available</p>
<?php endif; ?>
    </form>

    <!-- Display the translation result if available -->
   

<!-- ----------------------API-OU T ES?---------------------------------------------------------------- -->
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
<!-- ------------------------------------EL FORMULAIRE------------------------------------------------ -->
<form class="form" method="get" id="currency-form">
    <!-- ------------------------------------FORMGROUP------------------------------------------------ -->         
    <div class="form-group">

    <!-- ------------------------------------FROM------------------------------------------------ -->
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
<!-- ------------------------------------TO------------------------------------------------ -->
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

<!-- ------------------------------------FONCTION------------------------------------------------ -->
    <?php
    if ($currencies !== false) {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["convert"])) {
        
        $fromCurrency = $_GET["from_currency"];
        $toCurrency = $_GET["to_currency"];
        $selectedToCurrencyCode = $_GET["to_currency"];
        $amount = $_GET["amount"];

        $conversionApiUrl = "https://currency-converter18.p.rapidapi.com/api/v1/convert?from=$fromCurrency&to=$toCurrency&amount=$amount";

        $conversionResult = makeApiRequest($conversionApiUrl);
        // <!-- ------------------------------------RESULTAT------------------------------------------------ -->
        if ($conversionResult !== false) {
            $convertedAmount = number_format($conversionResult['result']['convertedAmount'], 2, '.', '');
            echo "<p class='conversion-result'>Conversion Result: " . htmlspecialchars( $selectedToCurrencyCode .' '. $convertedAmount, ENT_QUOTES, 'UTF-8') . "</p>";
        } else {
            echo "<p class='conversion-result'>Error in conversion</p>";
        }
    }
}
?>

</form>

<footer>

</footer>

</body>
</html>