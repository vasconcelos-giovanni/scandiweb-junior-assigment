<?php
declare(strict_types= 1);
namespace App\Core;

class Config
{
    private array $config = [];

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(".env file not found at: {$path}");
        }

        $this->load($path);
    }

    private function load(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // PHP 7.4 compatible check
            if (substr(trim($line), 0, 1) === '#') {
                continue;
            }

            $parts = explode('=', $line, 2);
            
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Remove surrounding quotes if present (PHP 7.4 compatible)
                if (
                    (substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
                ) {
                    $value = substr($value, 1, -1);
                }
                
                $this->config[$key] = $value;
            }
        }
    }

    public function get(string $key, $default = null): ?string
    {
        return $this->config[$key] ?? $default;
    }
}