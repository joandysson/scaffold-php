<?php
declare(strict_types=1);

namespace Config\Validation;

class Validator
{
    /**
     * Validate data against simple rules.
     * Supported rules: required, email
     *
     * @param array $data
     * @param array<string,string> $rules
     * @return array<string,array<string>> List of errors
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $ruleString);
            foreach ($ruleList as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = 'required';
                }
                if ($rule === 'email' && $value !== null && !filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'email';
                }
            }
        }
        return $errors;
    }
}
