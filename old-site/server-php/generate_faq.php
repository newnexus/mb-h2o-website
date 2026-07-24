<?php
function generateFaq($jsonFile, $errorLog) {
    $output = '';
    $jsonContent = file_get_contents($jsonFile);
    $faqs = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile: Whole file invalid - " . json_last_error_msg() . PHP_EOL;
        file_put_contents($errorLog, $errorMessage, FILE_APPEND);
        return $output;
    }

    $requiredKeys = ['question', 'answer', 'foldout_icon'];
    $validAnimationTypes = ['fade-in', 'slide-down'];
    $validDirections = ['top', 'bottom'];

    foreach ($faqs as $index => $faq) {
        $missingKeys = array_diff($requiredKeys, array_keys($faq));
        if (!empty($missingKeys)) {
            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile faq $index: Missing keys - " . implode(', ', $missingKeys) . PHP_EOL;
            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
            continue;
        }

        if (!is_string($faq['foldout_icon'])) {
            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile faq $index: Invalid foldout_icon" . PHP_EOL;
            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
            continue;
        }

        if (isset($faq['animations'])) {
            foreach (['question', 'answer'] as $component) {
                if (isset($faq['animations'][$component])) {
                    foreach ($faq['animations'][$component] as $animIndex => $animation) {
                        if (!isset($animation['type']) || !in_array($animation['type'], $validAnimationTypes)) {
                            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile faq $index, $component animation $animIndex: Invalid or missing animation type" . PHP_EOL;
                            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
                            unset($faq['animations'][$component][$animIndex]);
                            continue;
                        }
                        if ($animation['type'] === 'slide-down' && (!isset($animation['amount']) || !is_numeric($animation['amount']))) {
                            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile faq $index, $component animation $animIndex: Invalid or missing slide-down amount" . PHP_EOL;
                            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
                            unset($faq['animations'][$component][$animIndex]);
                        }
                    }
                }
            }
        }

        $animationClasses = ['question' => [], 'answer' => []];
        if (isset($faq['animations'])) {
            foreach (['question', 'answer'] as $component) {
                if (isset($faq['animations'][$component])) {
                    foreach ($faq['animations'][$component] as $animation) {
                        $class = "animate-{$animation['type']}";
                        $animationClasses[$component][] = $class;
                    }
                }
            }
        }

        $backgroundStyle = '';
        $riverImagePath = "assets/images/river-background/river-image-" . ($index + 1) . ".webp";
        if (file_exists($riverImagePath)) {
            $backgroundStyle = ' style="background-image: url(\'' . htmlspecialchars($riverImagePath) . '\');"';
        }

        $output .= "<div class=\"faq-container fade-in-faq\"$backgroundStyle>\n";
        $output .= "    <div class=\"faq-question-container\">\n";
        $output .= "        <h2 class=\"" . implode(' ', $animationClasses['question']) . "\">" . htmlspecialchars($faq['question']) . "</h2>\n";
        $output .= "        <img src=\"" . htmlspecialchars($faq['foldout_icon']) . "\" alt=\"Foldout Arrow\" class=\"faq-foldout-icon\">\n";
        $output .= "    </div>\n";
        $output .= "    <div class=\"faq-answer-container\">\n";
        $output .= "        <p class=\"faq-answer " . implode(' ', $animationClasses['answer']) . "\" style=\"display: none;\">" . htmlspecialchars($faq['answer']) . "</p>\n";
        $output .= "    </div>\n";
        $output .= "</div>\n";
    }

    return $output;
}
?>