<?php
require_once __DIR__ . "/../app/Config/functions.php";
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testChangeLangSwitchesPrefix()
    {
        // simulate current request
        $_SERVER['REQUEST_URI'] = '/pt/about';
        $result = changeLang(LANG_EN);
        $this->assertSame('/en/about', $result);
    }
    public function testLanguagesReturnExpectedList()
    {
        $this->assertSame([LANG_EN, LANG_PT, LANG_FR, LANG_ES, LANG_ES], languages());
    }

}
