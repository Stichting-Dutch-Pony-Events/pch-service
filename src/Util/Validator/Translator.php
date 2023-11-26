<?php

namespace App\Util\Validator;

use Illuminate\Contracts\Translation\Translator as IlluminateTranslator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class Translator implements IlluminateTranslator
{
    public const SUPPORTED_LANGUAGES = [
        'en',
        'nl',
    ];

    public static ?array $translations = [];
    public static bool $dtoLoaded = false;

    protected string $locale;

    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance = self::$instance ?: new self();
    }

    public static function mergeTranslations(array $translations): void
    {
        self::$translations = array_replace_recursive(
            self::$translations,
            $translations
        );
        self::$dtoLoaded    = true;
    } 

    public static function loadTranslationsByRequest(?Request $request = null): void
    {
        $language         = $request?->getPreferredLanguage(self::SUPPORTED_LANGUAGES) ?? self::instance()->getLocale();
        $translationsPath = sprintf("%s/Resources/Lang/%s.php", __DIR__, $language);

        if (file_exists($translationsPath)) {
            self::$translations = include($translationsPath);
        }
    }

    public static function mergeIlluminateValidatorTranslations(?Request $request = null): void
    {
        $language     = $request?->getPreferredLanguage(self::SUPPORTED_LANGUAGES) ?? self::instance()->getLocale();
        $translations = [];

        $translationsDirectory = sprintf("%s/Resources/Lang/%s/", __DIR__, $language);
        $translationFiles      = array_diff(scandir($translationsDirectory), array('..', '.'));

        foreach ($translationFiles as $translationFile) {
            $key                = basename($translationFile, '.php');
            $translations[$key] = include($translationsDirectory . $translationFile);
        }

        self::mergeTranslations($translations);
    }

    public static function translate(string $path, array $variables = []): string|array
    {
        $keys         = explode('.', $path);
        $currentValue = self::$translations;

        foreach ($keys as $currentKey) {
            if (!is_array($currentValue) || !array_key_exists($currentKey, $currentValue)) {
                return $path;
            }

            $currentValue = $currentValue[$currentKey];
        }

        if (is_array($currentValue)) {
            return $currentValue;
        }

        return self::replaceVariables($currentValue, $variables);
    }

    /**
     * @param string $value
     * @param array $variables
     * @return string
     */
    private static function replaceVariables(string $value, array $variables): string
    {
        $keyPatterns = array_map(
            static fn ($variable) => sprintf("/{{ %s }}/", $variable),
            array_keys($variables)
        );

        $values = array_values($variables);

        return preg_replace($keyPatterns, $values, $value);
    }

    public function get($key, array $replace = [], $locale = null)
    {
        return self::translate($key, $replace);
    }

    public function choice($key, $number = null, array $replace = [], $locale = null): array|string
    {
        return self::translate($key, $replace);
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }
}
