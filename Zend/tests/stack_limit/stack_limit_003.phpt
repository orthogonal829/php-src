--TEST--
Stack limit 003 - Stack limit checks with fixed max_allowed_stack_size
--SKIPIF--
<?php
if (!function_exists('zend_test_zend_call_stack_get')) die("skip zend_test_zend_call_stack_get() is not available");
?>
--EXTENSIONS--
zend_test
--INI--
zend.max_allowed_stack_size=256K
--FILE--
<?php

var_dump(zend_test_zend_call_stack_get());

class Test1 {
    public function __destruct() {
        new Test1;
    }
}

class Test2 {
    public function __clone() {
        clone $this;
    }
}

function replace() {
    return preg_replace_callback('#.#', function () {
        return replace();
    }, 'x');
}

try {
    new Test1;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

try {
    clone new Test2;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

try {
    replace();
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}

?>
--EXPECTF--
array(4) {
  ["base"]=>
  string(%d) "0x%x"
  ["max_size"]=>
  string(%d) "0x%x"
  ["position"]=>
  string(%d) "0x%x"
  ["EG(stack_limit)"]=>
  string(%d) "0x%x"
}
Maximum call stack size of %d bytes (zend.max_allowed_stack_size-zend.reserved_stack_size) reached. Infinite recursion?
Maximum call stack size of %d bytes (zend.max_allowed_stack_size-zend.reserved_stack_size) reached. Infinite recursion?
Maximum call stack size of %d bytes (zend.max_allowed_stack_size-zend.reserved_stack_size) reached. Infinite recursion?
