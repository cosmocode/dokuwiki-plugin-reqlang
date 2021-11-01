<?php

namespace dokuwiki\plugin\reqlang\test;

use DokuWikiTest;

/**
 * FIXME tests for the reqlang plugin
 *
 * @group plugin_reqlang
 * @group plugins
 */
class ParseAcceptLangTest extends DokuWikiTest
{
    public function testHeaderParsing()
    {

        $header = 'en-ca,en-US ; q=0.6 , en;q=0.8,de-de;q=0.4';
        $plugin = new \action_plugin_reqlang();

        $expect = ['en-ca','en','en-us','de-de', 'de'];

        $this->assertSame($expect, $plugin->parseAcceptLang($header));
    }
}
