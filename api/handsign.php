<?php
// api/handsign.php
header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || empty($data['landmarks']) || !is_array($data['landmarks'])) {
    echo json_encode([
        'success' => false,
        'error'   => 'NO_LANDMARKS',
        'message' => 'No landmarks provided to API.',
    ]);
    exit;
}

$landmarks = $data['landmarks'];

// Try to index by name if present
$byName = [];
foreach ($landmarks as $idx => $lm) {
    if (!empty($lm['name'])) {
        $byName[$lm['name']] = $lm;
        $byName[$lm['name']]['_index'] = $idx;
    }
}

// Helper: get landmark safely by name OR index
function lm_get($byName, $landmarks, $name, $index) {
    if (!empty($byName[$name])) {
        return $byName[$name];
    }
    return isset($landmarks[$index]) ? $landmarks[$index] : null;
}

// MediaPipe Hands index mapping (21 points)
$wrist     = lm_get($byName, $landmarks, 'wrist', 0);
$thumbTip  = lm_get($byName, $landmarks, 'thumb_tip', 4);
$indexTip  = lm_get($byName, $landmarks, 'index_finger_tip', 8);
$middleTip = lm_get($byName, $landmarks, 'middle_finger_tip', 12);
$ringTip   = lm_get($byName, $landmarks, 'ring_finger_tip', 16);
$pinkyTip  = lm_get($byName, $landmarks, 'pinky_finger_tip', 20);

if (!$wrist) {
    echo json_encode([
        'success' => true,
        'sign'    => 'Unknown',
        'confidence' => 0.1,
        'note'    => 'No wrist landmark detected',
    ]);
    exit;
}

$tips = array_values(array_filter([
    $indexTip, $middleTip, $ringTip, $pinkyTip
]));

// If too few tips or no thumb, still respond safely
if (count($tips) < 2 || !$thumbTip) {
    echo json_encode([
        'success' => true,
        'sign'    => 'Unknown',
        'confidence' => 0.2,
        'note'    => 'Not enough finger tips for classification',
    ]);
    exit;
}

// bounding box height for normalization
$minY = PHP_FLOAT_MAX;
$maxY = -PHP_FLOAT_MAX;
foreach ($landmarks as $lm) {
    if (!isset($lm['y'])) continue;
    if ($lm['y'] < $minY) $minY = $lm['y'];
    if ($lm['y'] > $maxY) $maxY = $lm['y'];
}
$boxHeight = max(60.0, $maxY - $minY);

function dist2d($a, $b) {
    $dx = ($a['x'] ?? 0) - ($b['x'] ?? 0);
    $dy = ($a['y'] ?? 0) - ($b['y'] ?? 0);
    return sqrt($dx * $dx + $dy * $dy);
}

// average finger tip distance from wrist
$tipDistances = [];
foreach ($tips as $t) {
  $tipDistances[] = dist2d($t, $wrist);
}
$avgTips   = array_sum($tipDistances) / max(1, count($tipDistances));
$thumbDist = dist2d($thumbTip, $wrist);

// normalize (0–100-ish)
$normTips  = ($avgTips / $boxHeight) * 100.0;
$normThumb = ($thumbDist / $boxHeight) * 100.0;

// classification
$sign       = 'Unknown';
$confidence = 0.3;

// Open hand: fingers and thumb far from wrist
if ($normTips > 55 && $normThumb > 45) {
    $sign = 'Open Hand';
    $confidence = min(1.0, ($normTips - 55) / 25.0 + 0.7);
} else {
    $foldedFingers = ($normTips < 35);
    $thumbUp       = ($normThumb > 50 && $thumbTip['y'] < $wrist['y']); // thumb above wrist

    if ($foldedFingers && $thumbUp) {
        $sign = 'Thumbs Up';
        $confidence = 0.85;
    } elseif ($foldedFingers && $normThumb < 40) {
        $sign = 'Fist';
        $confidence = 0.8;
    }
}

echo json_encode([
    'success'    => true,
    'sign'       => $sign,
    'confidence' => $confidence,
    'debug'      => [
        'normTips'  => $normTips,
        'normThumb' => $normThumb,
        'landmarks' => count($landmarks),
    ],
]);
