<?php
/**
 * Injury Coordinate Testing and Diagnosis Script
 * Tests injury marker positioning logic
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "           INJURY COORDINATE DIAGNOSIS TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test 1: Check if injuries exist in database
echo "[TEST 1] Checking injuries in database...\n";
echo str_repeat("─", 65) . "\n";

try {
    $sql = "SELECT
        i.id,
        i.form_id,
        i.injury_number,
        i.injury_type,
        i.body_view,
        i.coordinate_x,
        i.coordinate_y,
        i.notes,
        f.form_number,
        f.patient_name
    FROM injuries i
    JOIN prehospital_forms f ON i.form_id = f.id
    ORDER BY i.form_id DESC, i.injury_number ASC
    LIMIT 20";

    $stmt = db_query($sql);
    $injuries = $stmt->fetchAll();

    if (count($injuries) === 0) {
        echo "❌ NO INJURIES FOUND in database!\n";
        echo "   Create a form with injury markers first.\n\n";
        exit;
    }

    echo "✅ Found " . count($injuries) . " injuries in database\n\n";

    // Display injuries
    $currentFormId = null;
    foreach ($injuries as $injury) {
        if ($currentFormId !== $injury['form_id']) {
            $currentFormId = $injury['form_id'];
            echo "\n📋 FORM: {$injury['form_number']} - Patient: {$injury['patient_name']}\n";
            echo str_repeat("─", 65) . "\n";
        }

        echo sprintf(
            "  Injury #%-2d | %-12s | %-5s | X: %6.2f%% | Y: %6.2f%%\n",
            $injury['injury_number'],
            ucfirst($injury['injury_type']),
            ucfirst($injury['body_view']),
            $injury['coordinate_x'],
            $injury['coordinate_y']
        );
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Coordinate Range Validation
echo "\n\n[TEST 2] Coordinate Range Validation\n";
echo str_repeat("─", 65) . "\n";

$invalidCoordinates = [];
foreach ($injuries as $injury) {
    $x = $injury['coordinate_x'];
    $y = $injury['coordinate_y'];

    $issues = [];
    if ($x < 0 || $x > 100) {
        $issues[] = "X out of range (0-100): $x";
    }
    if ($y < 0 || $y > 100) {
        $issues[] = "Y out of range (0-100): $y";
    }
    if ($x == 0 && $y == 0) {
        $issues[] = "Coordinates are (0,0) - may be default values";
    }

    if (!empty($issues)) {
        $invalidCoordinates[] = [
            'injury' => $injury,
            'issues' => $issues
        ];
    }
}

if (empty($invalidCoordinates)) {
    echo "✅ All coordinates are within valid range (0-100%)\n";
} else {
    echo "❌ Found " . count($invalidCoordinates) . " injuries with invalid coordinates:\n\n";
    foreach ($invalidCoordinates as $invalid) {
        $inj = $invalid['injury'];
        echo "  Form: {$inj['form_number']}, Injury #{$inj['injury_number']}\n";
        foreach ($invalid['issues'] as $issue) {
            echo "    ⚠ $issue\n";
        }
    }
}

// Test 3: Check JavaScript Positioning Logic
echo "\n\n[TEST 3] JavaScript Positioning Logic Test\n";
echo str_repeat("─", 65) . "\n";

echo "Simulating marker positioning for sample injury:\n\n";

$sampleInjury = $injuries[0];
$x_percent = $sampleInjury['coordinate_x'];
$y_percent = $sampleInjury['coordinate_y'];

echo "Database Values:\n";
echo "  X coordinate: {$x_percent}%\n";
echo "  Y coordinate: {$y_percent}%\n";
echo "  Body view: {$sampleInjury['body_view']}\n\n";

// Simulate typical image dimensions
$imageSizes = [
    'desktop' => ['width' => 300, 'height' => 600],
    'tablet' => ['width' => 250, 'height' => 500],
    'mobile' => ['width' => 200, 'height' => 400]
];

foreach ($imageSizes as $device => $size) {
    $width = $size['width'];
    $height = $size['height'];

    // This is the CURRENT calculation in the code
    $pixel_x = ($x_percent / 100) * $width;
    $pixel_y = ($y_percent / 100) * $height;

    echo ucfirst($device) . " ({$width}x{$height}):\n";
    echo "  Marker position: ({$pixel_x}px, {$pixel_y}px)\n";
    echo "  As percentage: (" . number_format(($pixel_x/$width)*100, 1) . "%, " . number_format(($pixel_y/$height)*100, 1) . "%)\n";
    echo "\n";
}

// Test 4: Check for Common Issues
echo "\n[TEST 4] Common Issues Check\n";
echo str_repeat("─", 65) . "\n";

$issues = [];

// Check if coordinates might be stored as pixels instead of percentages
$possiblePixelCoords = array_filter($injuries, function($inj) {
    return $inj['coordinate_x'] > 100 || $inj['coordinate_y'] > 100;
});

if (!empty($possiblePixelCoords)) {
    $issues[] = "⚠ Some coordinates are >100 - might be stored as pixels instead of %";
}

// Check if all coordinates are too similar
$xCoords = array_column($injuries, 'coordinate_x');
$yCoords = array_column($injuries, 'coordinate_y');
$xRange = max($xCoords) - min($xCoords);
$yRange = max($yCoords) - min($yCoords);

if ($xRange < 5 && $yRange < 5) {
    $issues[] = "⚠ All markers are clustered (range < 5%) - might be positioning issue";
}

// Check for decimal precision
$hasDecimals = false;
foreach ($injuries as $inj) {
    if ($inj['coordinate_x'] != floor($inj['coordinate_x']) ||
        $inj['coordinate_y'] != floor($inj['coordinate_y'])) {
        $hasDecimals = true;
        break;
    }
}

if ($hasDecimals) {
    echo "✅ Coordinates have decimal precision (good for accuracy)\n";
} else {
    $issues[] = "⚠ All coordinates are integers - may lose precision";
}

if (empty($issues)) {
    echo "✅ No common issues detected\n";
} else {
    foreach ($issues as $issue) {
        echo "$issue\n";
    }
}

// Test 5: Generate Positioning Test HTML
echo "\n\n[TEST 5] Generating Visual Test Page\n";
echo str_repeat("─", 65) . "\n";

$testHtml = '<!DOCTYPE html>
<html>
<head>
    <title>Injury Coordinate Visual Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; }
        .diagram-container {
            position: relative;
            display: inline-block;
            border: 2px solid #333;
            margin: 20px;
        }
        .body-image {
            display: block;
            width: 300px;
            height: 600px;
            background: #e0e0e0;
        }
        .injury-marker {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: red;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            transform: translate(-50%, -50%);
            cursor: pointer;
            z-index: 10;
        }
        .info { background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Injury Coordinate Visual Test</h1>
        <div class="info">
            <strong>Form:</strong> ' . htmlspecialchars($sampleInjury['form_number']) . '<br>
            <strong>Patient:</strong> ' . htmlspecialchars($sampleInjury['patient_name']) . '<br>
            <strong>Total Injuries:</strong> ' . count($injuries) . '
        </div>

        <h2>Injury Markers on Body Diagram</h2>
        <div id="frontContainer" class="diagram-container">
            <div class="body-image" style="background: url(\'../images/body-front.png\') center/contain no-repeat;"></div>
        </div>
        <div id="backContainer" class="diagram-container">
            <div class="body-image" style="background: url(\'../images/body-back.png\') center/contain no-repeat;"></div>
        </div>

        <h2>Injury Data</h2>
        <table>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>View</th>
                <th>X (%)</th>
                <th>Y (%)</th>
                <th>Notes</th>
            </tr>';

foreach ($injuries as $inj) {
    if ($inj['form_id'] === $sampleInjury['form_id']) {
        $testHtml .= sprintf(
            '<tr>
                <td>%d</td>
                <td>%s</td>
                <td>%s</td>
                <td>%.2f</td>
                <td>%.2f</td>
                <td>%s</td>
            </tr>',
            $inj['injury_number'],
            htmlspecialchars($inj['injury_type']),
            $inj['body_view'],
            $inj['coordinate_x'],
            $inj['coordinate_y'],
            htmlspecialchars($inj['notes'] ?: '-')
        );
    }
}

$testHtml .= '
        </table>
    </div>

    <script>
        const injuries = ' . json_encode(array_filter($injuries, function($inj) use ($sampleInjury) {
            return $inj['form_id'] === $sampleInjury['form_id'];
        }), JSON_NUMERIC_CHECK) . ';

        window.addEventListener("load", function() {
            injuries.forEach(injury => {
                const container = document.getElementById(injury.body_view + "Container");
                if (!container) return;

                const image = container.querySelector(".body-image");
                const container_rect = container.getBoundingClientRect();
                const image_rect = image.getBoundingClientRect();

                // Calculate position using CURRENT logic from edit_record.php
                const containerX = image_rect.left - container_rect.left + (injury.coordinate_x / 100) * image_rect.width;
                const containerY = image_rect.top - container_rect.top + (injury.coordinate_y / 100) * image_rect.height;

                const marker = document.createElement("div");
                marker.className = "injury-marker";
                marker.style.left = containerX + "px";
                marker.style.top = containerY + "px";
                marker.textContent = injury.injury_number;
                marker.title = `Injury #${injury.injury_number} - ${injury.injury_type}\\nX: ${injury.coordinate_x}%, Y: ${injury.coordinate_y}%`;

                container.appendChild(marker);

                console.log(`Injury #${injury.injury_number}:`, {
                    stored: {x: injury.coordinate_x, y: injury.coordinate_y},
                    containerRect: container_rect,
                    imageRect: image_rect,
                    calculated: {x: containerX, y: containerY}
                });
            });
        });
    </script>
</body>
</html>';

$testFilePath = __DIR__ . '/injury_coordinate_test.html';
file_put_contents($testFilePath, $testHtml);

echo "✅ Visual test page created: injury_coordinate_test.html\n";
echo "   Open in browser to see marker positions\n";

// Summary
echo "\n\n" . str_repeat("═", 65) . "\n";
echo "                          SUMMARY\n";
echo str_repeat("═", 65) . "\n\n";

echo "Total Injuries Tested: " . count($injuries) . "\n";
echo "Forms Covered: " . count(array_unique(array_column($injuries, 'form_id'))) . "\n";
echo "\nTest Files Generated:\n";
echo "  - injury_coordinate_test.html (open in browser)\n";
echo "\n✅ Tests complete! Check the visual test page for positioning.\n\n";
?>
