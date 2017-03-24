<?php
namespace Rmccue\RequestTests\Transport;

use Rmccue\Requests as Requests;
use Rmccue\Requests\Transport\Hooks as Hooks;
use Rmccue\RequestTests\Transport\Base as Base;

class fsockopen extends Base {
	protected $transport = '\\Rmccue\\Requests\\Transport\\fsockopen';
}
