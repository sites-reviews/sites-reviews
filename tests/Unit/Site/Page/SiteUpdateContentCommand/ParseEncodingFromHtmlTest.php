<?php

namespace Tests\Unit\Site\Page\SiteUpdateContentCommand;

use App\Console\Commands\Site\SiteUpdateContentCommand;
use App\Site;
use App\SitePage;
use PHPUnit\Framework\TestCase;

class ParseEncodingFromHtmlTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetFirstEncoding()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>
<meta charset="  WINDOWS-1251" />
<meta charset="utf-8" />
</head>
<body>
содержание
</body>
</html>
EOF;

        $input = iconv('utf-8', 'windows-1251', $input);

        $command = new SiteUpdateContentCommand();

        $this->assertEquals('windows-1251', $command->parseEncodingFromHtml($input));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReturnFalseIfNoMeta()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<title>название</title>
</head>
</html>
EOF;

        $input = iconv('utf-8', 'windows-1251', $input);

        $command = new SiteUpdateContentCommand();

        $this->assertEquals(false, $command->parseEncodingFromHtml($input));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParseFromContentType()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GB2312" />
<title>название</title>
</head>
</html>
EOF;

        $command = new SiteUpdateContentCommand();

        $this->assertEquals('gb2312', $command->parseEncodingFromHtml($input));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReturnFalseIfNoCharset()
    {
        $input = <<<EOF
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" />
<title>название</title>
</head>
</html>
EOF;

        $input = iconv('utf-8', 'gb2312', $input);

        $command = new SiteUpdateContentCommand();

        $this->assertEquals(false, $command->parseEncodingFromHtml($input));
    }
}
