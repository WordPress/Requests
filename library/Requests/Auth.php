<?php

interface Requests_Auth {
	public function before_request(&$url, &$headers, &$data, &$type, &$options);

	public function before_send($type, &$transport_data, $url, $headers, $data, $options);
}