<?php

namespace App\Support;

use Respect\Validation\Exceptions\NestedValidationException;

class FormValidation
{
    public function validate(array $data, array $rules, array $messages = []): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            try {
                $rule->assert($data[$field] ?? null);
            } catch (NestedValidationException $exception) {
                foreach ($exception->getMessages() as $key => $msg) {
                    $index = "$field.$key";
                    $errors[$field][] = $messages[$index] ?? $msg;
                }
            }
        }

        return $errors;
    }
}
