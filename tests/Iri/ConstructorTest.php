<?php

namespace WpOrg\Requests\Tests\Iri;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Tests\Fixtures\StringableObject;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

/**
 * @covers \WpOrg\Requests\Iri::__construct
 */
final class ConstructorTest extends TestCase {

	/**
	 * Tests receiving an exception when an invalid input type is passed to the constructor.
	 *
	 * @dataProvider dataInvalidInput
	 *
	 * @param mixed $iri Invalid input.
	 *
	 * @return void
	 */
	public function testInvalidInput($iri) {
		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage('Argument #1 ($iri) must be of type string|Stringable|null');

		new Iri($iri);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidInput() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_NULL, TypeProviderHelper::GROUP_STRINGABLE);
	}

	/**
	 * Safeguard that the constructor can accept Stringable objects as $iri.
	 *
	 * @return void
	 */
	public function testAcceptsStringableIri() {
		$this->assertInstanceOf(Iri::class, new Iri(new StringableObject('https://example.com/')));
	}
}
