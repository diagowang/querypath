<?php
/**
 * @file
 * CSS Event handling tests for PseudoClasses.
 */
namespace QueryPath\Tests;

require_once __DIR__ . '/../TestCase.php';

use \QueryPath\CSS\DOMTraverser\PseudoClass;

/**
 * @ingroup querypath_tests
 * @group CSS
 */
class PseudoClassTest extends TestCase {

  protected function doc($string, $tagname) {

    $doc = new \DOMDocument('1.0');
    $doc->loadXML($string);

    $found = $doc->getElementsByTagName($tagname)->item(0);

    return array($found, $doc->documentElement);

  }

  /**
   * @expectedException \QueryPath\CSS\ParseException
   */
  public function testUnknownPseudoClass() {
    $xml = '<?xml version="1.0"?><root><foo>test</foo></root>';

    list($ele, $root) = $this->doc($xml, 'foo');
    $ps = new PseudoClass();

    $ps->elementMatches('TotallyFake', $ele, $root);
  }

  public function testLang() {
    $xml = '<?xml version="1.0"?><root><foo lang="en-US">test</foo></root>';

    list($ele, $root) = $this->doc($xml, 'foo');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('lang', $ele, $root, 'en-US');
    $this->assertTrue($ret);
    $ret = $ps->elementMatches('lang', $ele, $root, 'en');
    $this->assertTrue($ret);
    $ret = $ps->elementMatches('lang', $ele, $root, 'fr-FR');
    $this->assertFalse($ret);
    $ret = $ps->elementMatches('lang', $ele, $root, 'fr');
    $this->assertFalse($ret);


    // Check on ele that doesn't have lang.
    $ret = $ps->elementMatches('lang', $root, $root, 'fr');
    $this->assertFalse($ret);

  }

  public function testLangNS() {
    $this->markTestIncomplete();
  }

  public function testFormType() {
    $xml = '<?xml version="1.0"?><root><foo type="submit">test</foo></root>';

    list($ele, $root) = $this->doc($xml, 'foo');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('submit', $ele, $root);
    $this->assertTrue($ret);

    $ret = $ps->elementMatches('reset', $ele, $root);
    $this->assertFalse($ret);

  }

  public function testHasAttribute() {
    $xml = '<?xml version="1.0"?><root><foo enabled="enabled">test</foo></root>';

    list($ele, $root) = $this->doc($xml, 'foo');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('enabled', $ele, $root);
    $this->assertTrue($ret);
    $ret = $ps->elementMatches('disabled', $ele, $root);
    $this->assertFalse($ret);
  }

  public function testHeader() {
    $xml = '<?xml version="1.0"?><root><h1>TEST</h1><H6></H6><hi/><h12/><h1i/></root>';

    list($ele, $root) = $this->doc($xml, 'h1');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('header', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'H6');
    $ret = $ps->elementMatches('header', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'hi');
    $ret = $ps->elementMatches('header', $ele, $root);
    $this->assertFalse($ret);
    list($ele, $root) = $this->doc($xml, 'h1i');
    $ret = $ps->elementMatches('header', $ele, $root);
    $this->assertFalse($ret);
    list($ele, $root) = $this->doc($xml, 'h12');
    $ret = $ps->elementMatches('header', $ele, $root);
    $this->assertFalse($ret);
  }

  public function testContains(){
    $xml = '<?xml version="1.0"?><root><h>This is a test of :contains.</h></root>';

    list($ele, $root) = $this->doc($xml, 'h');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('contains', $ele, $root, 'test');
    $this->assertTrue($ret);

    $ret = $ps->elementMatches('contains', $ele, $root, 'is a test');
    $this->assertTrue($ret);

    $ret = $ps->elementMatches('contains', $ele, $root, 'This is a test of :contains.');
    $this->assertTrue($ret);

    $ret = $ps->elementMatches('contains', $ele, $root, 'Agent P, here is your mission.');
    $this->assertFalse($ret);

    $ret = $ps->elementMatches('contains', $ele, $root, "'Agent P, here is your mission.'");
    $this->assertFalse($ret);

  }
  public function testContainsExactly() {
    $xml = '<?xml version="1.0"?><root><h>This is a test of :contains-exactly.</h></root>';

    list($ele, $root) = $this->doc($xml, 'h');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('contains-exactly', $ele, $root, 'test');
    $this->assertFalse($ret);

    $ret = $ps->elementMatches('contains-exactly', $ele, $root, 'This is a test of :contains-exactly.');
    $this->assertTrue($ret);

    $ret = $ps->elementMatches('contains-exactly', $ele, $root, 'Agent P, here is your mission.');
    $this->assertFalse($ret);

    $ret = $ps->elementMatches('contains-exactly', $ele, $root, '"Agent P, here is your mission."');
    $this->assertFalse($ret);
  }
  public function testHas() {
  }
  public function testParent() {
    $ps = new PseudoClass();

    $xml = '<?xml version="1.0"?><root><p/></root>';
    list($ele, $root) = $this->doc($xml, 'p');
    $ret = $ps->elementMatches('parent', $ele, $root);
    $this->assertFalse($ret);

    $xml = '<?xml version="1.0"?><root><p></p>></root>';
    list($ele, $root) = $this->doc($xml, 'p');
    $ret = $ps->elementMatches('parent', $ele, $root);
    $this->assertFalse($ret);

    $xml = '<?xml version="1.0"?><root><p>TEST</p></root>';
    list($ele, $root) = $this->doc($xml, 'p');
    $ret = $ps->elementMatches('parent', $ele, $root);
    $this->assertTrue($ret);

    $xml = '<?xml version="1.0"?><root><p><q/></p></root>';
    list($ele, $root) = $this->doc($xml, 'p');
    $ret = $ps->elementMatches('parent', $ele, $root);
    $this->assertTrue($ret);

  }
  public function testFirst() {
    $ps = new PseudoClass();
    $xml = '<?xml version="1.0"?><root><p><q/></p><a></a><b/></root>';

    list($ele, $root) = $this->doc($xml, 'q');
    $ret = $ps->elementMatches('first', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'p');
    $ret = $ps->elementMatches('first', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'b');
    $ret = $ps->elementMatches('first', $ele, $root);
    $this->assertFalse($ret);

  }
  public function testLast() {
    $ps = new PseudoClass();
    $xml = '<?xml version="1.0"?><root><p><q/></p><a></a><b/></root>';

    list($ele, $root) = $this->doc($xml, 'q');
    $ret = $ps->elementMatches('last', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'p');
    $ret = $ps->elementMatches('last', $ele, $root);
    $this->assertFalse($ret);

    list($ele, $root) = $this->doc($xml, 'b');
    $ret = $ps->elementMatches('last', $ele, $root);
    $this->assertTrue($ret);
  }
  public function testByPosition() {
  }
  public function testNot() {
  }
  public function testOnlyOfType() {
  }
  public function testEmpty() {
    $xml = '<?xml version="1.0"?><root><foo lang="en-US">test</foo><bar/><baz></baz></root>';

    list($ele, $root) = $this->doc($xml, 'foo');
    $ps = new PseudoClass();

    $ret = $ps->elementMatches('empty', $ele, $root);
    $this->assertFalse($ret);

    list($ele, $root) = $this->doc($xml, 'bar');
    $ret = $ps->elementMatches('empty', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'baz');
    $ret = $ps->elementMatches('empty', $ele, $root);
    $this->assertTrue($ret);
  }
  public function testOnlyChild() {
    $xml = '<?xml version="1.0"?><root><foo>test<a/></foo><b><c/></b></root>';
    $ps = new PseudoClass();

    list($ele, $root) = $this->doc($xml, 'foo');
    $ret = $ps->elementMatches('only-child', $ele, $root);
    $this->assertFalse($ret);

    list($ele, $root) = $this->doc($xml, 'a');
    $ret = $ps->elementMatches('only-child', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'c');
    $ret = $ps->elementMatches('only-child', $ele, $root);
    $this->assertTrue($ret);
  }
  public function testLastOfType() {
  }
  public function testFirstOftype() {
  }
  public function testNthLastChild() {
  }
  public function testNthChild() {
    // :even
    // :odd
    // :nth-child
    // :first-child
  }
  public function testNthOfTypeChild() {
  }
  public function testNthLastOfTypeChild() {
  }
  public function testLink() {
    $ps = new PseudoClass();
    $xml = '<?xml version="1.0"?><root><a href="foo"><b hreff="bar">test</b></a><c/></root>';

    list($ele, $root) = $this->doc($xml, 'c');
    $ret = $ps->elementMatches('link', $ele, $root);
    $this->assertFalse($ret);

    list($ele, $root) = $this->doc($xml, 'a');
    $ret = $ps->elementMatches('link', $ele, $root);
    $this->assertTrue($ret);

    list($ele, $root) = $this->doc($xml, 'b');
    $ret = $ps->elementMatches('link', $ele, $root);
    $this->assertFalse($ret);
  }
  public function testRoot() {
    $ps = new PseudoClass();
    $xml = '<?xml version="1.0"?><root><p><q/></p><a></a><b/></root>';

    list($ele, $root) = $this->doc($xml, 'q');
    $ret = $ps->elementMatches('root', $ele, $root);
    $this->assertFalse($ret);

    list($ele, $root) = $this->doc($xml, 'root');
    $ret = $ps->elementMatches('root', $ele, $root);
    $this->assertTrue($ret);
  }
  /* Deprecated and removed.
  public function testXRoot() {
  }
  public function testXReset() {
  }
   */
}
