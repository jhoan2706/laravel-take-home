<?php

namespace App\Services\Remix;

class RemixService
{
    /**
     * Generate 4 variants by adding prefixes/suffixes to the original text.
     *
     * Constraints:
     * - Include the original text in each variant
     * - Add different prefixes/suffixes (hooks, CTAs, etc.)
     * - Stay under 280 characters
     * - No external APIs
     */
    public function variants(string $text): array
    {
        // Define prefixes and suffixes (kept short to avoid large overhead)
        $prefixes = ["Quick tip:", "Hot take:", "Did you know?", "Pro tip:"];
        $suffixes = ["Try this today!", "What do you think?", "Let us know!", "Check it out!"];

        $MAX = 280;
        $variants = [];

        foreach ($prefixes as $i => $prefix) {
            $suffix = $suffixes[$i] ?? '';

            // Try full prefix + text + suffix first
            $candidate = $prefix . ' ' . $text . ' ' . $suffix;

            // Use mb string functions for correct multi-byte handling
            $len = mb_strlen($candidate);

            if ($len <= $MAX) {
                $variants[] = $candidate;
            } else {
                // Calculate how much room we have for suffix after prefix and text
                $baseWithPrefix = $prefix . ' ' . $text;
                $baseLen = mb_strlen($baseWithPrefix);

                if ($baseLen <= $MAX) {
                    // space left for suffix (including a separating space)
                    $allowedForSuffix = $MAX - $baseLen - 1; // -1 for space before suffix

                    if ($allowedForSuffix > 0) {
                        $truncatedSuffix = mb_substr($suffix, 0, $allowedForSuffix);

                        // Truncate to last word boundary so we don't cut words mid-way
                        $lastSpace = mb_strrpos($truncatedSuffix, ' ');
                        if ($lastSpace !== false && $lastSpace > 0) {
                            $truncatedSuffix = mb_substr($truncatedSuffix, 0, $lastSpace);
                        } else {
                            // No word boundary found in truncated suffix -> omit suffix
                            $truncatedSuffix = '';
                        }

                        if ($truncatedSuffix !== '') {
                            $variants[] = $baseWithPrefix . ' ' . rtrim($truncatedSuffix);
                        } else {
                            $variants[] = $baseWithPrefix;
                        }
                    } else {
                        // No room for suffix, use prefix + text only (if it fits)
                        $variants[] = $baseWithPrefix;
                    }
                } else {
                    // Prefix + text doesn't fit; try text + truncated suffix
                    $baseTextLen = mb_strlen($text);
                    if ($baseTextLen <= $MAX) {
                        $allowedForSuffix = $MAX - $baseTextLen - 1; // -1 for space
                        if ($allowedForSuffix > 0) {
                            $truncatedSuffix = mb_substr($suffix, 0, $allowedForSuffix);

                            // Truncate to last word boundary to avoid mid-word cuts
                            $lastSpace = mb_strrpos($truncatedSuffix, ' ');
                            if ($lastSpace !== false && $lastSpace > 0) {
                                $truncatedSuffix = mb_substr($truncatedSuffix, 0, $lastSpace);
                            } else {
                                $truncatedSuffix = '';
                            }

                            if ($truncatedSuffix !== '') {
                                $variants[] = $text . ' ' . rtrim($truncatedSuffix);
                            } else {
                                $variants[] = $text;
                            }
                        } else {
                            // No room for suffix/prefix; just use the original text
                            $variants[] = $text;
                        }
                    }
                }
            }

            if (count($variants) === 4) {
                break;
            }
        }

        // Ensure we always return exactly 4 items (pad with original text if needed)
        while (count($variants) < 4) {
            $variants[] = $text;
        }

        // Make sure variants are strings and trimmed
        return array_map(fn ($v) => trim((string) $v), $variants);
    }
}
