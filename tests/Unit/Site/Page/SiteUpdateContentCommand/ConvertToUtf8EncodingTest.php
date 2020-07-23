<?php

namespace Tests\Unit\Site\Page\SiteUpdateContentCommand;

use App\Console\Commands\Site\SiteUpdateContentCommand;
use App\Site;
use App\SitePage;
use PHPUnit\Framework\TestCase;

class ConvertToUtf8EncodingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConvert1251()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>
<meta charset="windows-1251" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $input = iconv('utf-8', 'windows-1251', $input);

        $expectOutput = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>
<meta charset="windows-1251" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $command = new SiteUpdateContentCommand();
        $output = $command->convertToUtf8Encoding($input, 'windows-1251');

        $this->assertEquals($expectOutput, $output);
    }
}
