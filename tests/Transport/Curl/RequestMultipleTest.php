<?php

namespace WpOrg\Requests\Tests\Transport\Curl;

use ArrayIterator;
use ArrayObject;
use WpOrg\Requests\Hooks;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Response;
use WpOrg\Requests\Tests\TestCase;
use WpOrg\Requests\Transport\Curl;

/**
 * @covers \WpOrg\Requests\Transport\Curl::request_multiple
 */
final class RequestMultipleTest extends TestCase {

	public function set_up() {
		$this->skipWhenTransportNotAvailable(Curl::class);
	}

	/**
	 * @dataProvider dataEmptyRequests
	 *
	 * @param array|\ArrayAccess&\Traversable $requests Empty request collection.
	 */
	public function testEmptyRequestsDoNotDispatchMultiHooks($requests) {
		$events = [];
		$hooks  = new Hooks();
		foreach (['curl.before_multi_exec', 'curl.after_multi_exec'] as $hook) {
			$hooks->register(
				$hook,
				static function () use (&$events, $hook) {
					$events[] = $hook;
				}
			);
		}

		$options   = [
			'transport' => Curl::class,
			'hooks'     => $hooks,
		];
		$transport = new Curl();

		$this->assertSame([], $transport->request_multiple($requests, $options));
		$this->assertSame([], Requests::request_multiple($requests, $options));
		$this->assertSame([], $events);
	}

	/**
	 * @return array
	 */
	public static function dataEmptyRequests() {
		return [
			'array'         => [[]],
			'ArrayIterator' => [new ArrayIterator([])],
			'ArrayObject'   => [new ArrayObject([])],
		];
	}

	/**
	 * @dataProvider dataHookOverrides
	 *
	 * @param bool $override_first  Whether the first request overrides the global hooks.
	 * @param bool $override_second Whether the second request overrides the global hooks.
	 * @param bool $global_hooks    Whether a global hook manager is supplied.
	 */
	public function testMultiHooksUseGlobalHooks($override_first, $override_second, $global_hooks) {
		$url           = $this->httpbin('/get');
		$events        = [];
		$completed     = [];
		$multihandles  = [];
		$hook_managers = [];

		foreach (['global', 'first', 'second'] as $name) {
			$hooks = new Hooks();
			$hooks->register(
				'curl.before_multi_add',
				static function () use (&$events, $name) {
					$events[] = 'add:' . $name;
				}
			);
			$hooks->register(
				'curl.before_multi_exec',
				function (&$multihandle) use (&$events, &$multihandles, $name) {
					$events[]       = 'before:' . $name;
					$multihandles[] = $multihandle;
					$this->assertTrue(curl_multi_setopt($multihandle, CURLMOPT_MAXCONNECTS, 1));
				}
			);
			$hooks->register(
				'curl.after_multi_exec',
				function (&$multihandle) use (&$events, &$completed, &$multihandles, $name) {
					$events[]       = 'after:' . $name;
					$multihandles[] = $multihandle;
					$this->assertCount(2, $completed);
				}
			);
			$hooks->register(
				'multiple.request.complete',
				static function ($response, $id) use (&$completed, $name) {
					$completed[$id] = $name;
				}
			);
			$hook_managers[$name] = $hooks;
		}

		$first_hooks  = $override_first ? 'first' : 'global';
		$second_hooks = $override_second ? 'second' : 'global';
		$requests     = [
			'first' => [
				'url'     => $url,
				'options' => ['hooks' => $hook_managers[$first_hooks]],
			],
			'second' => [
				'url'     => $url,
				'options' => ['hooks' => $hook_managers[$second_hooks]],
			],
		];
		$options      = [
			'transport' => Curl::class,
		];
		if ($global_hooks) {
			$options['hooks'] = $hook_managers['global'];
		}

		$responses = Requests::request_multiple($requests, $options);

		$this->assertCount(2, $responses);
		foreach ($responses as $response) {
			$this->assertInstanceOf(Response::class, $response);
			$this->assertSame(200, $response->status_code);
		}

		$expected_events = ['add:' . $first_hooks, 'add:' . $second_hooks];
		if ($global_hooks) {
			$expected_events[] = 'before:global';
			$expected_events[] = 'after:global';
		}

		$this->assertSame($expected_events, $events);
		$this->assertSame($first_hooks, $completed['first']);
		$this->assertSame($second_hooks, $completed['second']);
		if ($global_hooks) {
			$this->assertCount(2, $multihandles);
			$this->assertSame($multihandles[0], $multihandles[1]);
		} else {
			$this->assertSame([], $multihandles);
		}
	}

	/**
	 * @return array
	 */
	public static function dataHookOverrides() {
		return [
			'shared hooks'     => [false, false, true],
			'first overrides'  => [true, false, true],
			'second overrides' => [false, true, true],
			'both override'    => [true, true, true],
			'no global hooks'  => [true, true, false],
		];
	}
}
