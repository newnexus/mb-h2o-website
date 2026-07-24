<?php
function generateSections($jsonFile, $errorLog) {
    $output = '';
    $jsonContent = file_get_contents($jsonFile);
    $sections = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile: Whole file invalid - " . json_last_error_msg() . PHP_EOL;
        file_put_contents($errorLog, $errorMessage, FILE_APPEND);
        return $output; // Return empty string on error
    }

    $requiredKeys = ['title', 'text', 'media_type'];
    $validAnimationTypes = ['scale-up', 'slide-in', 'fade-in', 'rotate-in', 'bounce'];
    $validDirections = ['left', 'right', 'top', 'bottom'];

    foreach ($sections as $index => $section) {
        $missingKeys = array_diff($requiredKeys, array_keys($section));
        if (!empty($missingKeys)) {
            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile section $index: Missing keys - " . implode(', ', $missingKeys) . PHP_EOL;
            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
            continue;
        }

        if ($section['media_type'] === 'thumbnail' && !isset($section['youtube_id'])) {
            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile section $index: Missing youtube_id for thumbnail type" . PHP_EOL;
            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
            continue;
        }

        if (in_array($section['media_type'], ['image', 'video']) && (!isset($section['media_src']) || !is_string($section['media_src']))) {
            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile section $index: Invalid media_src for {$section['media_type']} type" . PHP_EOL;
            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
            continue;
        } elseif ($section['media_type'] === 'carousel' && (!isset($section['media_src']) || !is_array($section['media_src']))) {
            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile section $index: Invalid media_src array for carousel type" . PHP_EOL;
            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
            continue;
        }

        if (isset($section['animations'])) {
            foreach (['media', 'title', 'text'] as $component) {
                if (isset($section['animations'][$component])) {
                    foreach ($section['animations'][$component] as $animIndex => $animation) {
                        if (!isset($animation['type']) || !in_array($animation['type'], $validAnimationTypes)) {
                            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile section $index, $component animation $animIndex: Invalid or missing animation type" . PHP_EOL;
                            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
                            unset($section['animations'][$component][$animIndex]);
                            continue;
                        }
                        if ($animation['type'] === 'slide-in' && (!isset($animation['direction']) || !in_array($animation['direction'], $validDirections))) {
                            $errorMessage = date('Y-m-d H:i:s') . " - Error in file $jsonFile section $index, $component animation $animIndex: Invalid or missing slide-in direction" . PHP_EOL;
                            file_put_contents($errorLog, $errorMessage, FILE_APPEND);
                            unset($section['animations'][$component][$animIndex]);
                        }
                    }
                }
            }
        }

        $animationClasses = ['media' => [], 'title' => [], 'text' => []];
        if (isset($section['animations'])) {
            foreach (['media', 'title', 'text'] as $component) {
                if (isset($section['animations'][$component])) {
                    foreach ($section['animations'][$component] as $animation) {
                        $class = "animate-{$animation['type']}";
                        if ($animation['type'] === 'slide-in') {
                            $class .= "-{$animation['direction']}";
                        }
                        $animationClasses[$component][] = $class;
                    }
                }
            }
        }

        // Check for river background image
        $backgroundStyle = '';
        $riverImagePath = "assets/images/river-background/river-image-" . ($index + 1) . ".webp";
        if (file_exists($riverImagePath)) {
            $backgroundStyle = ' style="background-image: url(\'' . htmlspecialchars($riverImagePath) . '\');"';
        }

        // Build HTML with line breaks and indentation
        $output .= "<div class=\"section-container scale-up-section\"$backgroundStyle>\n";
        $output .= "    <div class=\"section\">\n";
        $output .= "        <div class=\"media\">\n";
        if ($section['media_type'] === 'image') {
            $output .= "            <div class=\"media-container\">\n";
            $output .= "                <img src=\"" . htmlspecialchars($section['media_src']) . "\" alt=\"" . htmlspecialchars($section['title']) . "\" class=\"" . implode(' ', $animationClasses['media']) . "\">\n";
            $output .= "            </div>\n";
        } elseif ($section['media_type'] === 'video') {
            $output .= "            <div class=\"media-container\">\n";
            $output .= "                <video controls class=\"" . implode(' ', $animationClasses['media']) . "\">\n";
            $output .= "                    <source src=\"" . htmlspecialchars($section['media_src']) . "\" type=\"video/mp4\">\n";
            $output .= "                </video>\n";
            $output .= "            </div>\n";
        } elseif ($section['media_type'] === 'carousel') {
            $output .= "            <div class=\"media-container\">\n";
            $output .= "                <div class=\"carousel\">\n";
            foreach ($section['media_src'] as $imgIndex => $img) {
                $active = $imgIndex === 0 ? 'active' : '';
                $output .= "                    <img src=\"" . htmlspecialchars($img) . "\" class=\"$active " . implode(' ', $animationClasses['media']) . "\">\n";
            }
            $output .= "                </div>\n";
            $output .= "                <div class=\"carousel-controls\">\n";
            foreach ($section['media_src'] as $imgIndex => $img) {
                $active = $imgIndex === 0 ? 'active' : '';
                $output .= "                    <span class=\"carousel-bullet $active\" data-index=\"$imgIndex\"></span>\n";
            }
            $output .= "                </div>\n";
            $output .= "            </div>\n";
        } elseif ($section['media_type'] === 'thumbnail') {
            $output .= "            <div class=\"media-container\">\n";
            $output .= "                <div class=\"youtube-thumbnail " . implode(' ', $animationClasses['media']) . "\" data-youtube-id=\"" . htmlspecialchars($section['youtube_id']) . "\">\n";
            $output .= "                    <img src=\"" . htmlspecialchars($section['media_src']) . "\" alt=\"YouTube Thumbnail\">\n";
            $output .= "                    <div class=\"youtube-icon\"></div>\n";
            $output .= "                </div>\n";
            $output .= "            </div>\n";
        }
        $output .= "        </div>\n";
        $output .= "        <div class=\"text-content\">\n";
        $output .= "            <h2 class=\"" . implode(' ', $animationClasses['title']) . "\">" . htmlspecialchars($section['title']) . "</h2>\n";
        $output .= "            <p class=\"" . implode(' ', $animationClasses['text']) . "\">" . htmlspecialchars($section['text']) . "</p>\n";
        $output .= "        </div>\n";
        $output .= "    </div>\n";
        $output .= "</div>\n";
    }

    return $output;
}
?>