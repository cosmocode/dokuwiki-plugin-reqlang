<?php

/**
 * DokuWiki Plugin reqlang (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
 */
class action_plugin_reqlang extends \dokuwiki\Extension\ActionPlugin
{

    /** @inheritDoc */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('INIT_LANG_LOAD', 'BEFORE', $this, 'handleLangLoading');

    }

    /**
     * Initialize the correct language
     *
     * @param Doku_Event $event event object by reference
     * @param mixed $param optional parameter passed when event was registered
     * @return void
     */
    public function handleLangLoading(Doku_Event $event, $param)
    {
        global $INPUT;
        global $conf;

        $langs = $this->parseAcceptLang($INPUT->server->str('HTTP_ACCEPT_LANGUAGE'));
        $langs[] = $conf['lang']; // add fallback to default at the end

        foreach ($langs as $lang) {
            if (is_dir(DOKU_INC . 'inc/lang/' . $lang)) {
                $conf['lang'] = $lang;
                $event->data = $lang;
                return;
            }
        }
    }

    /**
     * This returns the list of wanted languages in the correct order
     *
     * @param string $header
     * @return string[]
     */
    public function parseAcceptLang($header)
    {
        $header = strtolower($header);
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $header, $lang_parse);
        if (!count($lang_parse[1])) return [];

        // create a list like "en" => 0.8
        $langs = array_combine($lang_parse[1], $lang_parse[4]);

        // set default to 1 for any without q factor
        foreach ($langs as $lang => $prio) {
            if ($prio === '') $langs[$lang] = 1;
        }

        // add primary language if missing
        foreach ($langs as $lang => $prio) {
            $split = strpos($lang, '-');
            if ($split !== false) {
                $primary = substr($lang, 0, $split);
                if (!isset($langs[$primary])) {
                    $langs[$primary] = $prio - 0.01;
                }
            }
        }

        // sort list based on value
        arsort($langs, SORT_NUMERIC);

        return array_unique(array_keys($langs));
    }
}

