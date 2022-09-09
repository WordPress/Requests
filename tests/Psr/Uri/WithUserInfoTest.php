<?php

namespace WpOrg\Requests\Tests\Psr\Uri;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use WpOrg\Requests\Iri;
use WpOrg\Requests\Psr\Uri;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Tests\TypeProviderHelper;

final class WithUserInfoTest extends TestCase {

	/**
	 * Tests changing the user-info when using withUserInfo().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withUserInfo
	 *
	 * @return void
	 */
	public function testWithUserInfoReturnsUriInstance() {
		$uri = Uri::fromIri(new Iri(''));

		$this->assertInstanceOf(UriInterface::class, $uri->withUserInfo('user'));
	}

	/**
	 * Tests changing the user-info when using withUserInfo().
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withUserInfo
	 *
	 * @return void
	 */
	public function testWithUserInfoReturnsNewInstance() {
		$uri = Uri::fromIri(new Iri(''));

		$this->assertNotSame($uri, $uri->withUserInfo('http'));
	}

	/**
	 * Tests receiving an exception when the withUserInfo() method received an invalid input type as `$user`.
	 *
	 * @dataProvider dataInvalidTypeNotString
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withUserInfo
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithUserInfoWithoutStringInUserThrowsInvalidArgumentException($input) {
		$uri = Uri::fromIri(new Iri(''));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withUserInfo(): Argument #1 ($user) must be of type string', Uri::class));

		$uri = $uri->withUserInfo($input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotString() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING);
	}

	/**
	 * Tests receiving an exception when the withUserInfo() method received an invalid input type as `$password`.
	 *
	 * @dataProvider dataInvalidTypeNotStringOrNull
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withUserInfo
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithUserInfoWithoutStringInPasswordThrowsInvalidArgumentException($input) {
		$uri = Uri::fromIri(new Iri(''));

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('%s::withUserInfo(): Argument #2 ($password) must be of type null|string', Uri::class));

		$uri = $uri->withUserInfo('user', $input);
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataInvalidTypeNotStringOrNull() {
		return TypeProviderHelper::getAllExcept(TypeProviderHelper::GROUP_STRING, TypeProviderHelper::GROUP_NULL);
	}

	/**
	 * Tests receiving an exception when the withUserInfo() method received an invalid input type as `$password`.
	 *
	 * @dataProvider dataWithUserInfo
	 *
	 * @covers \WpOrg\Requests\Psr\Uri::withUserInfo
	 *
	 * @param mixed $input Invalid parameter input.
	 *
	 * @return void
	 */
	public function testWithUserInfoChangesUserInfo($user, $password, $expected) {
		$uri = Uri::fromIri(new Iri(''));

		$uri = $uri->withUserInfo($user, $password);

		$this->assertSame($expected, $uri->getUserInfo());
	}

	/**
	 * Data Provider.
	 *
	 * @return array
	 */
	public function dataWithUserInfo() {
		return [
			'empty' => ['', null, ''],
			'with the specified user information' => ['mail@example.org', 'password?[]', 'mail%40example.org:password%3F%5B%5D'],
			'Password is optional' => ['user', '', 'user'],
			'Password can bi null' => ['user', null, 'user'],
			'an empty string for the user is equivalent to removing user information' => ['', 'password', ''],
		];
	}
}
