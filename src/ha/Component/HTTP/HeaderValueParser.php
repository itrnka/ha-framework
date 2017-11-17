<?php
declare(strict_types = 1);

namespace ha\Component\HTTP;


/**
 * Class HeaderValueParser
 * Works for simple header values such as Accept*, Content* (cookie, user agent and other complicated string not supported yet)
 * @package ha\Component\HTTP
 */
class HeaderValueParser
{
    private $headerValue;

    private static $cache = [];

    public function __construct(string $headerValue)
    {
        $this->headerValue = $headerValue;
    }

    public function getParamValue(string $param)
    {
        foreach ($this->parse() AS $valueGroup) {
            foreach ($valueGroup AS $value) {
                if (!is_null($value['key']) && strcasecmp($value['key'], $param) == 0) {
                    return $value['value'];
                }
            }
        }
        return '';
    }

    public function getValuesOnly() : array
    {
        $return = [];
        foreach ($this->parse() AS $valueGroup) {
            foreach ($valueGroup AS $value) {
                if ($value['key'] === null) $return[] = $value['value'];
            }
        }
        return $return;
    }

    public function parse()
    {
        if (isSet($this->cache[$this->headerValue])) {
            return $this->cache[$this->headerValue];
        }

        $result = [];
        $groups = $this->_splitToGroups($this->headerValue, ',');
        foreach ($groups AS $subGroup) {
            $groupData = [];
            $subGroup = $this->_splitToGroups($subGroup, ';');
            #main()->dump($subGroup);
            foreach ($subGroup AS $value) {
                $valueParsed = $this->_extractParamValue($value);
                if (!is_array($valueParsed)) continue;
                $paramName = $valueParsed[0];
                $value = $valueParsed[1];
                if (strlen($value) > 1 && $value{0} === '"' && substr($value, -1) === '"') {
                    $value = trim($value, '"');
                } elseif (strlen($value) > 1 && $value{0} === '\'' && substr($value, -1) === '\'') {
                    $value = trim($value, '\'');
                }
                if ($value === '') continue;
                if (is_numeric($value)) $value = $value + 0;

                $groupData[] = [
                    'key' => $paramName, 'value' => $value,
                ];
            }
            $result[] = $groupData;
        }
        return $result;
    }


    private function _extractParamValue($partValue)
    {
        $eqPos = strpos($partValue, '=');
        $doubleSlashPos = strpos($partValue, '"');
        $singleSlashPos = strpos($partValue, '\'');

        $slashPositions = [];
        if (is_int($doubleSlashPos)) $slashPositions[] = $doubleSlashPos;
        if (is_int($singleSlashPos)) $slashPositions[] = $singleSlashPos;

        // without slashes
        if (count($slashPositions) === 0) {
            if ($eqPos === false) {
                return [null, $partValue];
            }
            return [
                trim(substr($partValue, 0, $eqPos)), trim(substr($partValue, ($eqPos + 1))),
            ];
        }

        // with slash
        $slashPosition = min($slashPositions);
        if ($eqPos === false) {
            return [null, $partValue];
        }
        if ($eqPos !== false && $eqPos > $slashPosition) {
            echo $partValue;
            var_dump($eqPos);
            return [null, $partValue];
        }

        return [
            trim(substr($partValue, 0, $eqPos)), trim(substr($partValue, ($eqPos + 1))),
        ];
    }

    private function _splitToGroups($headerValue, $separator) : array
    {
        $headerValueLength = strlen($headerValue);
        $groups = [];
        $group = '';
        $slashOpened = false;
        $slash = null;
        for ($i = 0; $i <= $headerValueLength; $i++) {
            if ($i === $headerValueLength) {
                if ($slashOpened) $group = ''; // invalid header
                $group = trim($group);
                if ($group !== '') $groups[] = $group;
                break;
            }
            $ch = $headerValue{$i};
            switch ($ch) {
                case '"':
                case '\'':
                    if ($slashOpened && $slash === $ch) {
                        $slashOpened = false;
                        $slash = null;
                    } else {
                        if (is_null($slash)) {
                            $slash = $ch;
                            $slashOpened = true;
                        }
                    }
                    $group .= $ch;
                    break;
                case $separator:
                    if (!$slashOpened) {
                        $group = trim($group);
                        if ($group !== '') $groups[] = $group;
                        $group = "";
                    } else {
                        $group .= $ch;
                    }
                    break;
                default:
                    $group .= $ch;
            }
        }
        return $groups;
    }
}