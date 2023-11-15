<?php


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["textToTranslate"]) && isset($_GET["targetLanguage"])) {
    // Get form inputs
    $textToTranslate = $_GET["textToTranslate"];
    $targetLanguage = $_GET["targetLanguage"];

    // Call the translate function
    $translationResult = translate("en", $targetLanguage, $textToTranslate);
} else {
    // If the form is not submitted, set $translationResult to null
    $translationResult = null;
}

function translate($from, $to, $text){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://translate.googleapis.com/translate_a/single?client=gtx&sl=" . $from . "&tl=" . $to . "&hl=en-US&dt=t&dt=bd&dj=1&source=icon&tk=310461.310461&q=" . urlencode($text),
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

?>

<form action="airbnb.php" method="get">
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
</form>

<?php if ($translationResult !== null): ?>
    <?php
    $translationData = json_decode($translationResult, true);

    if (isset($translationData['sentences'][0]['trans'])) {
        $translatedText = $translationData['sentences'][0]['trans'];
    } else {
        // Handle the case where the translation is not available
        $translatedText = 'Translation not available';
    }
    ?>
    <p>Translation: <?php echo $translatedText; ?></p>
<?php endif; ?>

