<?php
namespace Dorguzen\Core;


use Closure;


class DGZ_Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $customMessages = [];

    /**
     * Construct with data and rules (rules may be strings or arrays).
     *
     * Example rules:
     *  ['name' => 'required|min:3|max:50', 'age' => ['required','integer','between:18,120']]
     */
    public function __construct(array $data = [], array $rules = [], array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
    }

    /**
     * Fluent factory.
     */
    public static function make(array $data, array $rules, array $customMessages = []): self
    {
        $v = new self($data, $rules, $customMessages);
        $v->validate();
        return $v;
    }


    public function getCustomMessages()
    {
        return $this->customMessages;
    }

    /**
     * Run validation.
     */
    public function validate(): void
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleset) {
            $valuePresent = array_key_exists($field, $this->data);
            $value = $valuePresent ? $this->data[$field] : null;

            // normalize ruleset to array
            if (is_string($ruleset)) {
                $rules = explode('|', $ruleset);
            } elseif (is_array($ruleset)) {
                $rules = $ruleset;
            } else {
                continue;
            }

            // 'sometimes' means skip validation when field not present
            $hasSometimes = in_array('sometimes', $rules, true);
            if ($hasSometimes && !$valuePresent) {
                continue;
            }

            // process 'nullable' flag
            $isNullable = in_array('nullable', $rules, true);
            
            // process required
            if (in_array('required', $rules, true) && !$this->validate_required($field, $value)) {
                $this->addError($field, $this->message($field, 'required', "{$field} is required."));
                // required failed, skip further rules
                continue;
            }

            // if value is null/empty and nullable: skip other rules
            if ($isNullable && ($value === null || $value === '' || $value === [])) {
                continue;
            }

            // iterate other rules
            foreach ($rules as $rule) {
                if (in_array($rule, ['required', 'nullable', 'sometimes'], true)) {
                    continue;
                }

                // allow closures (custom rule as callback)
                if ($rule instanceof Closure) {
                    $ok = call_user_func($rule, $value, $this->data, $field);
                    if ($ok !== true) {
                        $this->addError($field, is_string($ok) ? $ok : $this->message($field, 'callback', "{$field} failed validation."));
                    }
                    continue;
                }

                // parse rule with parameter: name:param1,param2
                [$name, $params] = $this->parseRule($rule);

                // if rule is custom named callback like callback:myFunction
                if ($name === 'callback' && !empty($params[0])) {
                    $callable = $params[0];
                    $ok = is_callable($callable) ? call_user_func($callable, $value, $this->data, $field) : false;
                    if ($ok !== true) {
                        $this->addError($field, is_string($ok) ? $ok : $this->message($field, 'callback', "{$field} failed validation."));
                    }
                    continue;
                }

                // map to rule methods
                $method = 'validate_' . $name;
                if (method_exists($this, $method)) {
                    $ok = $this->{$method}($field, $value, ...$params); 
                    if ($ok !== true) {
                        $this->addError($field, is_string($ok) ? $ok : $this->message($field, $name, "{$field} failed {$name} validation."));
                    }
                } else {
                    // unknown rule - ignore or flag - here we'll ignore
                }
            }
        }
    }

    /**
     * Returns boolean pass/fail
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Returns errors array: field => [msg1, msg2]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Add an error for a given field.
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Provide message override or default.
     */
    protected function message(string $field, string $rule, string $fallback): string
    {
        $key = "{$field}.{$rule}";

        if (isset($this->customMessages[$key])) {
            return $this->customMessages[$key];
        }
        $key2 = $rule;
        if (isset($this->customMessages[$key2])) {
            return $this->customMessages[$key2];
        }
        return $fallback;
    }

    /**
     * Parse a rule like "min:3" or "between:1,10".
     * Returns [name, paramsArray]
     */
    protected function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $paramString] = explode(':', $rule, 2);
            $params = explode(',', $paramString);
            return [trim($name), array_map('trim', $params)];
        }
        return [trim($rule), []];
    }

    // ---------------------------
    // Built-in rule handlers
    // each returns true or error message string
    // ---------------------------

    protected function validate_required($field, $value)
    {
        if (is_array($value)) {
            return !empty($value);
        }
        return !($value === null || $value === '');
    }

    protected function validate_string($field, $value)
    {
        return is_string($value) ? 
            true : 
            $this->message($field, "{$field}.string", "{$field} must be a string.");
    }

    protected function validate_integer($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false ? 
        true : 
        $this->message($field, "{$field}.integer", "{$field} must be an integer.");
    }

    protected function validate_numeric($field, $value)
    {
        return is_numeric($value) ? 
            true : 
            $this->message($field, "{$field}.numeric", "{$field} must be numeric.");
    }

    protected function validate_boolean($field, $value)
    {
        return in_array($value, [true, false, 1, 0, '1', '0'], true) ? 
            true : 
            $this->message($field, "{$field}.boolean", "{$field} must be boolean.");
    }

    protected function validate_email($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? 
            true : 
            $this->message($field, "{$field}.email", "{$field} is invalid email address.");
    }

    protected function validate_url($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) ? 
            true : 
            $this->message($field, "{$field}.url", "{$field} is an invalid URL.");
    }

    protected function validate_min($field, $value, $min)
    {
        if (is_numeric($value)) {
            return ($value >= $min) ? 
                true : 
                $this->message($field, "{$field}.min:{$min}", "{$field} must be at least {$min}.");
        }
        if (is_string($value)) {
            return (mb_strlen($value) >= (int)$min) ? 
                true : 
                $this->message($field, "{$field}.min:{$min}", "{$field} must be at least {$min} characters.");
        }
        if (is_array($value)) {
            return (count($value) >= (int)$min) ?
                true :
                $this->message($field, "{$field}.min:{$min}", "{$field} must have at least {$min} items.");
        }
        return "Invalid value for min check.";
    }

    protected function validate_max($field, $value, $max)
    {
        if (is_numeric($value)) {
            return ($value <= $max) ? 
                true : 
                $this->message($field, "{$field}.max:{$max}", "{$field} must be at most {$max}.");
        }
        if (is_string($value)) {
            return (mb_strlen($value) <= (int)$max) ? 
                true : 
                $this->message($field, "{$field}.max:{$max}", "{$field} must be at most {$max} characters.");
        }
        if (is_array($value)) {
            return (count($value) <= (int)$max) ? 
                true : 
                $this->message($field, "{$field}.max:{$max}", "{$field} must have at most {$max} items.");
        }
        return "Invalid value for max check.";
    }

    protected function validate_between($field, $value, $min, $max)
    {
        if (is_numeric($value)) {
            return ($value >= $min && $value <= $max) ? 
                true : 
                $this->message($field, "{$field}.between:{$min},{$max}", "{$field} must be between {$min} and {$max}.");
        }
        if (is_string($value)) {
            $len = mb_strlen($value);
            return ($len >= $min && $len <= $max) ? 
                true : 
                $this->message($field, "{$field}.between:{$min},{$max}", "{$field} must be between {$min} and {$max} characters.");
        }
        if (is_array($value)) {
            $c = count($value);
            return ($c >= $min && $c <= $max) ? 
                true : 
                $this->message($field, "{$field}.between:{$min},{$max}", "{$field} must have between {$min} and {$max} items.");
        }
        return "Invalid value for between check.";
    }

    protected function validate_regex($field, $value, $pattern)
    {
        // pattern should be valid preg pattern (no delimiting required)
        $pattern = '/' . trim($pattern, "/") . '/';
        return preg_match($pattern, (string)$value) ? 
            true : 
            $this->message($field, "{$field}.regex", "{$field} has an invalid format.");
    }

    protected function validate_in($field, $value, ...$allowed)
    {
        if (in_array($value, $allowed, true)) {
            return true;
        }

        $ruleKey = "in:" . implode(',', $allowed);

        // Build nice fallback message
        $fallback = "{$field} must be one of: " . implode(', ', $allowed) . ".";
        return $this->message($field, "{$field}.{$ruleKey}", $fallback);
    }

    protected function validate_not_in($field, $value, ...$disallowed)
    {
        if (!in_array($value, $disallowed, true)) {
            return true;
        }

        $ruleKey = "not_in:" . implode(',', $disallowed);
        $fallback = "{$field} is an invalid value";
        return $this->message($field, "{$field}.{$ruleKey}", $fallback);
    }

    protected function validate_same($field, $value, $otherField)
    {
        $other = $this->data[$otherField] ?? null;
        return $value === $other ? 
            true : 
            $this->message($field, "{$field}.same:{$otherField}", "{$field} must be the same as {$otherField}.");
    }

    protected function validate_different($field, $value, $otherField)
    {
        $other = $this->data[$otherField] ?? null;
        return $value !== $other ? 
            true : 
            $this->message($field, "{$field}.different:{$otherField}", "{$field} must be different from {$otherField}.");
    }

    protected function validate_date($field, $value)
    {
        return (strtotime($value) !== false) ? 
            true : 
            $this->message($field, "{$field}.date", "{$field} is an invalid date.");
    }

    protected function validate_before($field, $value, $date)
    {
        $ts = strtotime($value);
        $other = strtotime($date);
        return ($ts !== false && $other !== false && $ts < $other) ? 
            true : 
            $this->message($field, "{$field}.before:{$date}", "{$field} must be a valid date.");
    }

    protected function validate_after($field, $value, $date)
    {
        $ts = strtotime($value);
        $other = strtotime($date);
        return ($ts !== false && $other !== false && $ts > $other) ? 
            true : 
            $this->message($field, "{$field}.after:{$date}", "{$field} must be a valid date.");
    }

    protected function validate_array($field, $value)
    {
        return is_array($value) ? 
        true : 
        $this->message($field, "{$field}.array", "{$field} must be an array.");
    }

    protected function validate_present($field, $value)
    {
        // present means key exists. caller ensures that.
        return true;
    }

    // alias for integer rule
    protected function validate_int($field, $value)
    {
        return $this->validate_integer($field, $value) ?
            true :
            $this->message($field, "{$field}.int", "{$field} must be an integer.");
    }
}