<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 2/24/2019
 * Time: 3:31 PM
 */

namespace MockServer\Model;


class NottableString
{
    private $value;
    private $not;
    private $hashCode;
    private $json;

    private function __construct(string $value, bool $not)
    {
        $this->value = $value;
        if ($not != null) {
            $this->not = $not;
        } else {
            $this->not = false;
        }
        $this->hashCode = Objects::hash($this->value, $this->not);
        $this->json = ($this->not ? "!" : "") . $this->value;
    }

    private function __construct2(string $value)
    {
        if ($value != null && $value->startsWith("!")) {
            $this->value = $value->replaceFirst("^!", "");
            $this->not = true;
        } else {
            $this->value = $value;
            $this->not = false;
        }
        $this->hashCode = Objects::hash($this->value, $this->not);
        $this->json = ($this->not ? "!" : "") . $this->value;
    }

    public static function deserializeNottableStrings(string ...$strings): array {
        $nottableStrings = [];
        foreach ($strings as $string) {
            $nottableStrings[] = self::string($string);
        }
        return $nottableStrings;
    }

//    public static List<NottableString> deserializeNottableStrings(List<String> strings) {
//        List<NottableString> nottableStrings = new LinkedList<>();
//        for (String string : strings) {
//            nottableStrings.add(string(string));
//        }
//        return nottableStrings;
//    }

    public static function serialiseNottableString(NottableString $nottableString): string
    {
        return $nottableString->toString();
    }

//    public static List<String> serialiseNottableString(List<NottableString> nottableStrings) {
//    List<String> strings = new LinkedList<>();
//        for (NottableString nottableString : nottableStrings) {
//            strings.add(nottableString.toString());
//        }
//        return strings;
//    }

    public static function string(String $value, bool $not): NottableString {
        return new NottableString($value, $not);
    }

//    public static function string(String $value): NottableString {
//        return new NottableString($value);
//    }

    public static function not(string $value) {
        return new NottableString($value, true);
    }

    public static function strings(String ...$values) {
        $nottableValues = [];
        if ($values != null) {
            foreach ($values as $value) {
                $nottableValues[] = self::string($value);
            }
        }
        return $nottableValues;
    }

//    public static List<NottableString> strings(Collection<String> values) {
//    List<NottableString> nottableValues = new ArrayList<>();
//        if (values != null) {
//            for (String value : values) {
//                nottableValues.add(string(value));
//            }
//        }
//        return nottableValues;
//    }

    public function getValue(): string
    {
        return $this->value;
    }

    //@JsonIgnore
    public function isNot(): bool {
        return $this->not;
    }

    public function capitalize(): NottableString {
        $split = explode("-", $this->value . "_");
        for ($i = 0; $i < $split->length; $i++) {
            $split[$i] = StringUtils::capitalize($split[$i]);
}
        return new NottableString(StringUtils::substringBeforeLast(Joiner::on("-")->join($split), "_"), $this->not);
    }

    public function lowercase(): NottableString {
        return new NottableString($this->value->toLowerCase(), $this->not);
    }

    public function equalsIgnoreCase($other): bool {
    return $this->equals($other, true);
}

    private function equals(Object $other, bool $ignoreCase): bool {
        if (is_string($other)) {
            if ($ignoreCase) {
                return $this->not != ((String) $other)->equalsIgnoreCase($this->value);
            } else {
                return $this->not != $other->equals($this->value);
            }
        } else if ($other instanceof NottableString) {
            $that = $other;
            if ($that->getValue() == null) {
                return $this->value == null;
            }
            $reverse = ($that->not != $this->not) && ($that->not || $this->not);
            if ($ignoreCase) {
                return $reverse != $that->getValue()->equalsIgnoreCase($this->value);
            } else {
                return $reverse != $that->getValue()->equals($this->value);
            }
        }
        return false;
    }

    //@Override
    public function equals2($other): bool {
        if (is_string($other)) {
            return $this->not != $other->equals($this-value);
        } else if ($other instanceof NottableString) {
            $that = $other;
                if ($that->getValue() == null) {
                    return $this->value == null;
                }
                $reverse = ($that->not != $this->not) && ($that->not || $this->not);
                return $reverse != $that->getValue()->equals($this->value);
            }
        return false;
    }

    //@Override
    public function hashCode(): int {
        return $this->hashCode;
    }

    //@Override
    public function toString(): string {
        return $this->json;
    }

}