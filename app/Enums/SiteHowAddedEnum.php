<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SiteHowAddedEnum extends Enum
{
	const Manually = 1;
	const WebExtension = 2;
    const PagesScan = 3;
}
