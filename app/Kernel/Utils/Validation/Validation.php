<?php


namespace App\Kernel\Utils\Validation;

class Validation
{
    
    const ERROR_DEFAULT = 'Invalid';
    
    protected $_fields = array();
    protected $_errors = array();
    protected $_validations = array();
    protected $_labels = array();
    protected $_instanceRules = array();
    protected $_instanceRuleMessage = array();
    protected static $_rules = array();
    protected static $_ruleMessages = array();
    protected $validUrlPrefixes = array('http://', 'https://', 'ftp://');
    protected $stop_on_first_fail = false;
    
    public function __construct($data = array(), $fields = array())
    {
        $this->_fields = !empty($fields) ? array_intersect_key($data, array_flip($fields)) : $data;
        $messages = include 'messages.php';
        static::$_ruleMessages = array_merge(static::$_ruleMessages, $messages);
    }

    protected function validateRequired($field, $value, $params = array())
    {
        if (isset($params[0]) && (bool)$params[0]) {
            $find = $this->getPart($this->_fields, explode('.', $field), true);
            return $find[1];
        }

        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        }

        return true;
    }

    protected function validateNumeric($field, $value)
    {
        return is_numeric($value);
    }

    protected function validateInteger($field, $value, $params)
    {
        if (isset($params[0]) && (bool)$params[0]) {
            //strict mode
            return preg_match('/^([0-9]|-[1-9]|-?[1-9][0-9]*)$/i', $value);
        }

        return filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    protected function stringLength($value)
    {
        if (!is_string($value)) {
            return false;
        } elseif (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }
        return strlen($value);
    }

    protected function validateLengthBetween($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0] && $length <= $params[1];
    }

    protected function validateLengthMin($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0];
    }

    protected function validateLengthMax($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length <= $params[0];
    }
    
    protected function validateBetween($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        }
        if (!isset($params[0]) || !is_array($params[0]) || count($params[0]) !== 2) {
            return false;
        }

        list($min, $max) = $params[0];

        return $this->validateMin($field, $value, array($min)) && $this->validateMax($field, $value, array($max));
    }
    
    protected function validateEmail($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateUrl($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                return filter_var($value, \FILTER_VALIDATE_URL) !== false;
            }
        }

        return false;
    }
    
    protected function validateUrlActive($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                $host = parse_url(strtolower($value), PHP_URL_HOST);

                return checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME');
            }
        }

        return false;
    }
    
    protected function validateAlpha($field, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    protected function validateAlphaNum($field, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    protected function validateRegex($field, $value, $params)
    {
        return preg_match($params[0], $value);
    }

    protected function validateDate($field, $value)
    {
        $isDate = false;
        if ($value instanceof \DateTime) {
            $isDate = true;
        } else {
            $isDate = strtotime($value) !== false;
        }

        return $isDate;
    }

    protected function validateOptional($field, $value, $params)
    {
        //Always return true
        return true;
    }

    public function data()
    {
        return $this->_fields;
    }

    public function errors($field = null)
    {
        if ($field !== null) {
            return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
        }

        return $this->_errors;
    }

    public function error($field, $msg, array $params = array())
    {
        $msg = $this->checkAndSetLabel($field, $msg, $params);

        $values = array();
        // Printed values need to be in string format
        foreach ($params as $param) {
            if (is_array($param)) {
                $param = "['" . implode("', '", $param) . "']";
            }
            if ($param instanceof \DateTime) {
                $param = $param->format('Y-m-d');
            } else {
                if (is_object($param)) {
                    $param = get_class($param);
                }
            }
            // Use custom label instead of field name if set
            if (is_string($params[0])) {
                if (isset($this->_labels[$param])) {
                    $param = $this->_labels[$param];
                }
            }
            $values[] = $param;
        }

        $this->_errors[$field][] = vsprintf($msg, $values);
    }
    
    public function message($msg)
    {
        $this->_validations[count($this->_validations) - 1]['message'] = $msg;

        return $this;
    }
    
    public function reset()
    {
        $this->_fields = array();
        $this->_errors = array();
        $this->_validations = array();
        $this->_labels = array();
    }

    protected function getPart($data, $identifiers, $allow_empty = false)
    {
        // Catches the case where the field is an array of discrete values
        if (is_array($identifiers) && count($identifiers) === 0) {
            return array($data, false);
        }
        // Catches the case where the data isn't an array or object
        if (is_scalar($data)) {
            return array(NULL, false);
        }
        $identifier = array_shift($identifiers);
        // Glob match
        if ($identifier === '*') {
            $values = array();
            foreach ($data as $row) {
                list($value, $multiple) = $this->getPart($row, $identifiers, $allow_empty);
                if ($multiple) {
                    $values = array_merge($values, $value);
                } else {
                    $values[] = $value;
                }
            }
            return array($values, true);
        } // Dead end, abort
        elseif ($identifier === NULL || !isset($data[$identifier])) {
            if ($allow_empty) {
                //when empty values are allowed, we only care if the key exists
                return array(null, array_key_exists($identifier, $data));
            }
            return array(null, false);
        } // Match array element
        elseif (count($identifiers) === 0) {
            if ($allow_empty) {
                //when empty values are allowed, we only care if the key exists
                return array(null, array_key_exists($identifier, $data));
            }
            return array($data[$identifier], false);
        } // We need to go deeper
        else {
            return $this->getPart($data[$identifier], $identifiers, $allow_empty);
        }
    }

    public function validate()
    {
        $set_to_break = false;
        foreach ($this->_validations as $v) {
            foreach ($v['fields'] as $field) {
                list($values, $multiple) = $this->getPart($this->_fields, explode('.', $field));

                if ($this->hasRule('optional', $field) && isset($values)) {
                    //Continue with execution below if statement
                } elseif (
                    $v['rule'] !== 'required' && !$this->hasRule('required', $field) &&
                    $v['rule'] !== 'accepted' &&
                    (!isset($values) || $values === '' || ($multiple && count($values) == 0))
                ) {
                    continue;
                }

                $errors = $this->getRules();
                if (isset($errors[$v['rule']])) {
                    $callback = $errors[$v['rule']];
                } else {
                    $callback = array($this, 'validate' . ucfirst($v['rule']));
                }

                if (!$multiple) {
                    $values = array($values);
                }

                $result = true;
                foreach ($values as $value) {
                    $result = $result && call_user_func($callback, $field, $value, $v['params'], $this->_fields);
                }

                if (!$result) {
                    $this->error($field, $v['message'], $v['params']);
                    if ($this->stop_on_first_fail) {
                        $set_to_break = true;
                        break;
                    }
                }
            }
            if ($set_to_break) break;
        }
        $_SESSION['errors'] = $this->_errors;
        return count($this->errors()) === 0;
    }
    
    public function stopOnFirstFail($stop = true)
    {
        $this->stop_on_first_fail = (bool)$stop;
    }

    protected function getRules()
    {
        return array_merge($this->_instanceRules, static::$_rules);
    }

    protected function getRuleMessages()
    {
        return array_merge($this->_instanceRuleMessage, static::$_ruleMessages);
    }

    protected function hasRule($name, $field)
    {
        foreach ($this->_validations as $validation) {
            if ($validation['rule'] == $name) {
                if (in_array($field, $validation['fields'])) {
                    return true;
                }
            }
        }

        return false;
    }

    protected static function assertRuleCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Second argument must be a valid callback. Given argument was not callable.');
        }
    }

    public function addInstanceRule($name, $callback, $message = null)
    {
        static::assertRuleCallback($callback);

        $this->_instanceRules[$name] = $callback;
        $this->_instanceRuleMessage[$name] = $message;
    }

    public static function addRule($name, $callback, $message = null)
    {
        if ($message === null) {
            $message = static::ERROR_DEFAULT;
        }

        static::assertRuleCallback($callback);

        static::$_rules[$name] = $callback;
        static::$_ruleMessages[$name] = $message;
    }

    public function getUniqueRuleName($fields)
    {
        if (is_array($fields)) {
            $fields = implode("_", $fields);
        }

        $orgName = "{$fields}_rule";
        $name = $orgName;
        $rules = $this->getRules();
        while (isset($rules[$name])) {
            $name = $orgName . "_" . rand(0, 10000);
        }

        return $name;
    }

    public function hasValidator($name)
    {
        $rules = $this->getRules();
        return method_exists($this, "validate" . ucfirst($name))
            || isset($rules[$name]);
    }

    public function rule($rule, $fields)
    {
        // Get any other arguments passed to function
        $params = array_slice(func_get_args(), 2);

        if (is_callable($rule)
            && !(is_string($rule) && $this->hasValidator($rule))) {
            $name = $this->getUniqueRuleName($fields);
            $msg = isset($params[0]) ? $params[0] : null;
            $this->addInstanceRule($name, $rule, $msg);
            $rule = $name;
        }

        $errors = $this->getRules();
        if (!isset($errors[$rule])) {
            $ruleMethod = 'validate' . ucfirst($rule);
            if (!method_exists($this, $ruleMethod)) {
                throw new \InvalidArgumentException("Rule '" . $rule . "' has not been registered with " . __CLASS__ . "::addRule().");
            }
        }

        // Ensure rule has an accompanying message
        $msgs = $this->getRuleMessages();
        $message = isset($msgs[$rule]) ? $msgs[$rule] : self::ERROR_DEFAULT;

        // Ensure message contains field label
        if (function_exists('mb_strpos')) {
            $notContains = mb_strpos($message, '{field}') === false;
        } else {
            $notContains = strpos($message, '{field}') === false;
        }
        if ($notContains) {
            $message = '{field} ' . $message;
        }

        $this->_validations[] = array(
            'rule' => $rule,
            'fields' => (array)$fields,
            'params' => (array)$params,
            'message' => $message
        );

        return $this;
    }

    public function label($value)
    {
        $lastRules = $this->_validations[count($this->_validations) - 1]['fields'];
        $this->labels(array($lastRules[0] => $value));

        return $this;
    }

    public function labels($labels = array())
    {
        $this->_labels = array_merge($this->_labels, $labels);
        return $this;
    }

    protected function checkAndSetLabel($field, $msg, $params)
    {
        if (isset($this->_labels[$field])) {
            $msg = str_replace('{field}', $this->_labels[$field], $msg);

            if (is_array($params)) {
                $i = 1;
                foreach ($params as $k => $v) {
                    $tag = '{field' . $i . '}';
                    $label = isset($params[$k]) && (is_numeric($params[$k]) || is_string($params[$k])) && isset($this->_labels[$params[$k]]) ? $this->_labels[$params[$k]] : $tag;
                    $msg = str_replace($tag, $label, $msg);
                    $i++;
                }
            }
        } else {
            $msg = str_replace('{field}', ucwords(str_replace('_', ' ', $field)), $msg);
        }

        return $msg;
    }

    public function rules($rules)
    {
        foreach ($rules as $ruleType => $params) {
            if (is_array($params)) {
                foreach ($params as $innerParams) {
                    if (!is_array($innerParams)) {
                        $innerParams = (array)$innerParams;
                    }
                    array_unshift($innerParams, $ruleType);
                    call_user_func_array(array($this, 'rule'), $innerParams);
                }
            } else {
                $this->rule($ruleType, $params);
            }
        }
    }
    
    public function withData($data, $fields = array())
    {
        $clone = clone $this;
        $clone->_fields = !empty($fields) ? array_intersect_key($data, array_flip($fields)) : $data;
        $clone->_errors = array();
        return $clone;
    }
    
    public function mapFieldRules($field_name, $rules)
    {
        $me = $this;

        array_map(function ($rule) use ($field_name, $me) {
            $rule = (array)$rule;
            $rule_name = array_shift($rule);

            $message = null;
            if (isset($rule['message'])) {
                $message = $rule['message'];
                unset($rule['message']);
            }
            //Add the field and additional parameters to the rule
            $added = call_user_func_array(array($me, 'rule'), array_merge(array($rule_name, $field_name), $rule));
            if (!empty($message)) {
                $added->message($message);
            }
        }, (array)$rules);
    }

    public function mapFieldsRules($rules)
    {
        $me = $this;
        array_map(function ($field_name) use ($rules, $me) {
            $me->mapFieldRules($field_name, $rules[$field_name]);
        }, array_keys($rules));
    }
}