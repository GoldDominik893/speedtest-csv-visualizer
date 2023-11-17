<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file = $_FILES["file"]["tmp_name"];

    if (($handle = fopen($file, "r")) !== FALSE) {
        $downloadSpeeds = array();
        $uploadSpeeds = array();
        $labels = array();
        $downloadSum = 0;
        $uploadSum = 0;
        $count = 0;

        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $download = intval($data[5]) / 1000; // Convert download speed from bits to Mbps
            $upload = intval($data[7]) / 1000;   // Convert upload speed from bits to Mbps
            $downloadSpeeds[] = $download;
            $uploadSpeeds[] = $upload;
            $downloadSum += $download;
            $uploadSum += $upload;
            $count++;

            // Labeling using date/time (1st column)
            $labels[] = $data[0];
        }
        fclose($handle);

        $averageDownload = ($count > 0) ? ($downloadSum / $count) : 0;
        $averageUpload = ($count > 0) ? ($uploadSum / $count) : 0;

        // Output average speeds
        echo "<h3>Average Download Speed: " . number_format($averageDownload, 2) . " Mbps</h3>";
        echo "<h3>Average Upload Speed: " . number_format($averageUpload, 2) . " Mbps</h3>";

        // Generate a simple line graph using the data
        echo "<h3>Graph</h3>";
        echo "<canvas id='speedChart' width='400' height='200'></canvas>";

        // JavaScript to plot the graph using Chart.js library
        echo "<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>";
        echo "<script>";
        echo "var ctx = document.getElementById('speedChart').getContext('2d');";
        echo "var myChart = new Chart(ctx, {";
        echo "    type: 'line',";
        echo "    data: {";
        echo "        labels: " . json_encode($labels) . ",";
        echo "        datasets: [{";
        echo "            label: 'Download Speed (Mbps)',";
        echo "            data: " . json_encode($downloadSpeeds) . ",";
        echo "            borderColor: 'rgb(75, 192, 192)',";
        echo "            tension: 0.1";
        echo "        }, {";
        echo "            label: 'Upload Speed (Mbps)',";
        echo "            data: " . json_encode($uploadSpeeds) . ",";
        echo "            borderColor: 'rgb(192, 75, 192)',";
        echo "            tension: 0.1";
        echo "        }]";
        echo "    },";
        echo "    options: {}";
        echo "});";
        echo "</script>";
    } else {
        echo "Failed to open the uploaded file.";
    }
}
?>
