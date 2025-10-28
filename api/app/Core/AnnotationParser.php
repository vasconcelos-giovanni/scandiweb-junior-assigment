<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Parses PHPDoc-style annotations from a docblock comment.
 * This is a lightweight parser for our specific @Column annotation.
 */
class AnnotationParser
{
    /**
     * Analyzes a docblock and returns an array of annotations.
     * @param string $docComment The comment string from a property or method.
     * @return array<string, array<string, string>>
     */
    public function parse(string $docComment): array
    {
        $annotations = [];
        // Regex to find patterns like @Name(arguments)
        preg_match_all('/@(\w+)\s*\((.*?)\)/s', $docComment, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $annotationName = $match[1]; // e.g., "Column"
            $argsString = $match[2];     // e.g., type="VARCHAR(255)", options="NOT NULL"

            $args = [];
                // First, parse double-quoted values: key="value"
                $argMatches = [];
                preg_match_all('/(\w+)\s*=\s*"([^"]*)"/s', $argsString, $doubleQuoted, PREG_SET_ORDER);
                foreach ($doubleQuoted as $m) {
                    $args[$m[1]] = $m[2];
                }

                // Then, parse single-quoted values: key='value'
                preg_match_all('/(\w+)\s*=\s*\'([^\']*)\'/s', $argsString, $singleQuoted, PREG_SET_ORDER);
                foreach ($singleQuoted as $m) {
                    $args[$m[1]] = $m[2];
                }

            $annotations[$annotationName] = $args;
        }

        return $annotations;
    }
}
