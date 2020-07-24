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
<meta name="test" content="description" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $input = iconv('utf-8', 'windows-1251', $input);

        $expected = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>

<meta name="test" content="description" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $command = new SiteUpdateContentCommand();
        $output = $command->convertToUtf8Encoding($input, 'windows-1251');

        $this->assertEquals($expected, $output);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConvertGb2312Encoding()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>
<meta charset="gb2312" />
<meta name="test" content="description" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $input = iconv('utf-8', 'gb2312', $input);

        $expected = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>

<meta name="test" content="description" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $command = new SiteUpdateContentCommand();
        $output = $command->convertToUtf8Encoding($input, 'gb2312');

        $this->assertEquals($expected, $output);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConvertWindows1254Encoding()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>Merhaba nasılsın</title>
</head>
<body>
</body>
</html>
EOF;
        $input = iconv('utf-8', 'windows-1254', $input);

        $expected = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>Merhaba nasılsın</title>
</head>
<body>
</body>
</html>
EOF;

        $command = new SiteUpdateContentCommand();
        $output = $command->convertToUtf8Encoding($input, 'windows-1254');

        $this->assertEquals($expected, $output);
    }
}
