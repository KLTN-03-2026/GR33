<?php
$output = "\"{\\\"ma_chung_chi\\\":\\\"CC_GG12092004VN\\\",\\\"ten_chung_chi\\\":\\\"Google_AI\\\"}\"";

$step1 = trim($output, " \"\n\r\t");
echo "Step 1: " . $step1 . "\n";
echo "Decoded Step 1: " . print_r(json_decode($step1, true), true) . "\n";

$step1_alt = json_decode($output);
echo "Alt Step 1: " . $step1_alt . "\n";
echo "Decoded Alt: " . print_r(json_decode($step1_alt, true), true) . "\n";
