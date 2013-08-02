<?php
/**
 * DokuWiki Plugin hidepages (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Matthias Schulte <dokuwiki@lupo49.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'action.php';

class action_plugin_hidepages extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler &$controller) {
        $controller->register_hook('PAGEUTILS_ID_HIDEPAGE', 'BEFORE', $this, 'handle_hidepages');
    }

    public function handle_hidepages(Doku_Event &$event, $param) {
        global $ACT;
        global $conf;
        global $INPUT;

        // get page id from current result
        $pageID = $event->data['id'];
        $isHidden = $event->data['hidden'];

        // skip if page is already marked as hidden or when the admin wants to see all pages
        if($isHidden || $this->getConf('ignorepattern')) return true;

        $metaSearch = (p_get_metadata($pageID, 'hidepage_search') ? true : false);
        $metaSitemap = (p_get_metadata($pageID, 'hidepage_sitemap') ? true : false);

        // check if event is fired by quicksearch or sitemap ajax request
        $isQsearch = ($INPUT->post->str('call') == 'qsearch' ? true : false);
        $isAjaxIndex = ($INPUT->post->str('call') == 'index' ? true : false);

        // Hide pages from quicksearch and search result page
        if(($ACT == 'search' || $isQsearch) && $metaSearch == true) {
            if($conf['allowdebug']) dbg("hidepages plugin - suppressed page: " . $pageID);
            $event->data['hidden'] = true;
        }

        if(($ACT == 'index' || $isAjaxIndex) && $metaSitemap == true) {
            if($conf['allowdebug']) dbg("hidepages plugin - suppressed page: " . $pageID);
            $event->data['hidden'] = true;
        }

        return true;
    }
}

// vim:ts=4:sw=4:et:
