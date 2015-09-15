<?php

namespace Newup\Test;

use NewUp\Templates\BasePackageTemplate;

class Package extends BasePackageTemplate
{

	public function getPackageName()
	{
		return "Hello, World!";
	}

}