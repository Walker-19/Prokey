<?php 

namespace App\Core;

abstract class Validator
{
    private static $messages = [
        'required' => 'Le champs :field est obligatoire.',
        'string' => 'Le champs :field doit etre une chaine de caractère.',
        'number' => 'Le champs :field doit etre un chiffre.',
        'max' => 'Le champs :field ne doit pas dépasser :max caractères.',
        'in' => 'Le champs :field doit être une des valeurs suivantes : :values.',
        'email' => 'Le champs :field doit être une adresse email valide.',
        'slug' => 'Le champs :field doit être un slug valide (lettres minuscules, chiffres et tirets uniquement).'
    ];
    
    public static function make(array $data, array $rules): bool
    {
        $data = self::sanitize($data);

        foreach ($rules as $field => $ruleString) {

            $rulesArray = explode('|', $ruleString);

            foreach ($rulesArray as $rule) {
                if($rule === 'required') {
                    if (!isset($data[$field]) || empty($data[$field])) {
                        self::getMessage('required', $field);
                        return false;
                    }
                } elseif (strpos($rule, 'string') === 0) {
                    if (isset($data[$field]) && !is_string($data[$field])) {
                        self::getMessage('string', $field);
                        return false;
                    }
                } elseif (strpos($rule, 'max:') === 0) {
                    $maxLength = (int)explode(':', $rule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) > $maxLength) {
                        self::getMessage('max', $field, ['max' => $maxLength]);
                        return false;
                    }
                } elseif (strpos($rule, 'in:') === 0) {
                    $allowedValues = explode(',', explode(':', $rule)[1]);
                    if (isset($data[$field]) && !in_array($data[$field], $allowedValues)) {
                        self::getMessage('in', $field, ['values' => implode(', ', $allowedValues)]);
                        return false;
                    }
                } elseif ($rule === 'number') {
                    if (isset($data[$field]) && !is_numeric($data[$field])) {
                        self::getMessage('number', $field);
                        return false;
                    }
                } elseif ($rule === 'email') {
                    if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        self::getMessage('email', $field);
                        return false;
                    }
                } elseif ($rule === 'slug') {
                    if (isset($data[$field]) && !self::isSlug($data[$field])) {
                        self::getMessage('slug', $field);
                        return false;
                    }
                }
            }
                
        }
        
        return true;
    }

    private static function sanitize($data)
    {
        $cleanData = [];
        foreach ($data as $key => $value) {
            if(is_string($value)) {
                $value = trim($value);
                $value = stripslashes($value);
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            $cleanData[$key] = $value;
        }

        return $cleanData;
    }

    private static function getMessage($rule, $field, $params = [])
    {
        $message = self::$messages[$rule] ?? 'Le champs :field est invalide.';
        $message = str_replace(':field', $field, $message);

        foreach ($params as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }

        Notification::add('error', $message);

        return $message;
    }

    private static function isSlug($string)
    {
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $string);
    }
}