<?php
/**
 * Created by PhpStorm.
 * User: khomk
 * Date: 3/2/2019
 * Time: 10:26 PM
 */

namespace MockServer\Model;


class Header extends KeyToMultiValue {

    public function __construct(String $name, String... $value) {
        parent::__construct($name, $value);
    }

    public static function header(String $name, int $value) {
        return new Header($name, $value);
    }

}