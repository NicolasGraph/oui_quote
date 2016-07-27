<?php

/**
 * Register tags.
 */
Txp::get('\Textpattern\Tag\Registry')
    ->register('oui_quote')
    ->register('oui_quote_body')
    ->register('oui_quote_cite')
    ->register('oui_quote_author');

/**
 * Register callbacks.
 */
if (txpinterface === 'admin') {
    add_privs('prefs.oui_quote', '1');
    add_privs('plugin_prefs.oui_quote', '1');

    register_callback('oui_quote_welcome', 'plugin_lifecycle.oui_quote');
    register_callback('oui_quote_install', 'prefs', null, 1);
    register_callback('oui_quote_options', 'plugin_prefs.oui_quote', null, 1);
    register_callback('oui_quote_inject_data', 'prefs', 'prefs_save', 1);

    $prefList = oui_quote_preflist();
    foreach ($prefList as $pref => $options) {
        register_callback('oui_quote_pophelp', 'admin_help', $pref);
    }
}

/**
 * Get external popHelp contents
 */
function oui_quote_pophelp($evt, $stp, $ui, $vars)
{
    return str_replace(HELP_URL, 'http://help.ouisource.com/', $ui);
}

/**
 * Handler for plugin lifecycle events.
 *
 * @param string $evt Textpattern action event
 * @param string $stp Textpattern action step
 */
function oui_quote_welcome($evt, $stp)
{
    switch ($stp) {
        case 'installed':
        case 'enabled':
            oui_quote_install();
            break;
        case 'deleted':
            remove_pref(null, 'oui_quote');
            safe_delete('txp_lang', "name LIKE 'oui\_quote%'");
            break;
    }
}

/**
 * Set prefs through:
 *
 * PREF_PLUGIN for 4.5
 * PREF_ADVANCED for 4.6+
 */
function oui_quote_preflist()
{
    $prefList = array(
        'oui_quote_services' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'oui_quote_sercices_select',
            'position'   => '10',
            'is_private' => false,
        ),
        'oui_quote_body' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'text_input',
            'position'   => '20',
            'is_private' => false,
        ),
        'oui_quote_author' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'text_input',
            'position'   => '30',
            'is_private' => false,
        ),
        'oui_quote_cite' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'text_input',
            'position'   => '40',
            'is_private' => false,
        ),
        'oui_quote_url' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'text_input',
            'position'   => '50',
            'is_private' => false,
        ),
        'oui_quote_cache_time' => array(
            'value'      => '60',
            'event'      => 'oui_quote',
            'visibility' => PREF_PLUGIN,
            'widget'     => 'text_input',
            'position'   => '60',
            'is_private' => false,
        ),
        'oui_quote_cache_set' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => PREF_HIDDEN,
            'widget'     => 'text_input',
            'position'   => '70',
            'is_private' => false,
        ),
    );
    return $prefList;
}

function oui_quote_install()
{
    $prefList = oui_quote_preflist();

    foreach ($prefList as $pref => $options) {
        if (get_pref($pref, null) === null) {
            set_pref(
                $pref,
                $options['value'],
                $options['event'],
                $options['visibility'],
                $options['widget'],
                $options['position'],
                $options['is_private']
            );
        }
    }
}

/**
 * Set Services pref function using selectInput()
 */
function oui_quote_sercices_select($name, $val)
{
    $vals = array(
        'oui_quote_service_quotes_on_design' => gTxt('oui_quote_quotes_on_design'),
        'oui_quote_service_they_said_so' => gTxt('oui_quote_they_said_so'),
        'oui_quote_service_le_monde' => gTxt('oui_quote_le_monde'),
        'oui_quote_service_le_figaro' => gTxt('oui_quote_le_figaro')
    );
    return selectInput($name, $vals, $val, '1', '1');
}

/**
 * Jump to the prefs panel.
 */
function oui_quote_options()
{
    header('Location: ?event=prefs#prefs_group_oui_quote');
}

/**
 * Force pulled data injection in the prefs fields on service selection.
 *
 * if service has changed;
 * or the quote field is empty;
 * or the cache is outdated.
 */
function oui_quote_inject_data()
{
    $serviceChanged = $_POST['oui_quote_services'] !== get_pref('oui_quote_services');
    $cacheOutdated = (time() - get_pref('oui_quote_cache_set')) > ($_POST['oui_quote_cache_time'] * 60);

    if ($serviceChanged || $cacheOutdated || (!get_pref('oui_quote_body'))) {
        switch ($_POST['oui_quote_services']) {
            case 'oui_quote_service_they_said_so':
                unset(
                    $_POST['oui_quote_body'],
                    $_POST['oui_quote_cite'],
                    $_POST['oui_quote_author'],
                    $_POST['oui_quote_url']
                );
                $feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
                set_pref('oui_quote_body', $feed->contents->quotes[0]->{'quote'});
                set_pref('oui_quote_cite', '');
                set_pref('oui_quote_author', $feed->contents->quotes[0]->{'author'});
                set_pref('oui_quote_url', 'https://theysaidso.com');
                break;
            case 'oui_quote_service_quotes_on_design':
                unset(
                    $_POST['oui_quote_body'],
                    $_POST['oui_quote_cite'],
                    $_POST['oui_quote_author'],
                    $_POST['oui_quote_url']
                );
                $feed = json_decode(
                    file_get_contents(
                        'http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'
                    )
                );
                set_pref('oui_quote_body', strip_tags($feed[0]->{'content'}));
                set_pref('oui_quote_cite', '');
                set_pref('oui_quote_author', $feed[0]->{'title'});
                set_pref('oui_quote_url', $feed[0]->{'link'});
                break;
            case 'oui_quote_service_le_monde':
                unset(
                    $_POST['oui_quote_body'],
                    $_POST['oui_quote_cite'],
                    $_POST['oui_quote_author'],
                    $_POST['oui_quote_url']
                );
                $feed = simplexml_load_string(file_get_contents('http://dicocitations.lemonde.fr/xml-rss2.php'));
                set_pref('oui_quote_url', $feed->channel->item->link);
                $feed = preg_split("/(\[|\])/", strip_tags($feed->channel->item->description));
                set_pref('oui_quote_body', trim($feed[0]));
                set_pref('oui_quote_cite', trim($feed[2]));
                set_pref('oui_quote_author', trim($feed[1]));
                break;
            case 'oui_quote_service_le_figaro':
                unset(
                    $_POST['oui_quote_body'],
                    $_POST['oui_quote_cite'],
                    $_POST['oui_quote_author'],
                    $_POST['oui_quote_url']
                );
                $feed = simplexml_load_string(file_get_contents('http://evene.lefigaro.fr/rss/citation_jour.xml'));
                set_pref('oui_quote_url', $feed->channel->item->link);
                $feed = preg_split("/ - /", strip_tags($feed->channel->item->title));
                set_pref('oui_quote_body', $feed[1]);
                set_pref('oui_quote_cite', '');
                set_pref('oui_quote_author', $feed[0]);
                break;
        }
        unset($_POST['oui_quote_cache_set']);
        set_pref('oui_quote_cache_set', time());
    }
    update_lastmod();
}

/**
 * Main plugin function.
 *
 * Pull the quote if needed;
 * store data in the prefs fields;
 * display the content.
 */
function oui_quote($atts, $thing = null)
{
    global $quote, $by, $cite, $via, $url, $service;

    $services = get_pref('oui_quote_services');
    $via = gTxt($services);

    extract(lAtts(array(
        'service' => ($via ? 1 : 0),
        'wraptag'    => 'figure',
        'class'      => '',
        'label'      => '',
        'labeltag'   => '',
    ), $atts));

    $cache_time = get_pref('oui_quote_cache_time');
    $now = time();

    // No quote stored ot outdated cache.
    $needquery = (!empty($via) && ($now - get_pref('oui_quote_cache_set')) > ($cache_time * 60)) ? true : false;

    if ($needquery) {
        switch ($services) {
            case 'oui_quote_service_they_said_so':
                $feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
                $quote = $feed->contents->quotes[0]->{'quote'};
                $by = $feed->contents->quotes[0]->{'author'};
                $url = 'https://theysaidso.com';
                break;
            case 'oui_quote_service_quotes_on_design':
                $feed = json_decode(
                    file_get_contents(
                        'http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'
                    )
                );
                $quote = strip_tags($feed[0]->{'content'});
                $by = $feed[0]->{'title'};
                $url = $feed[0]->{'link'};
                break;
            case 'oui_quote_service_le_monde':
                $feed = simplexml_load_string(file_get_contents('http://dicocitations.lemonde.fr/xml-rss2.php'));
                $url = $feed->channel->item->link;
                $feed = preg_split("/(\[|\])/", strip_tags($feed->channel->item->description));
                $quote = $feed[0];
                $cite = $feed[2];
                $by = $feed[1];
                break;
            case 'oui_quote_service_le_figaro':
                $feed = simplexml_load_string(file_get_contents('http://evene.lefigaro.fr/rss/citation_jour.xml'));
                $url = $feed->channel->item->link;
                $feed = preg_split("/ - /", strip_tags($feed->channel->item->title));
                $quote = $feed[1];
                $by = $feed[0];
                break;

            update_lastmod();
        }

        // Cache needed.
        if ($cache_time > 0) {
            // Time stamp and store the new data in the prefs.
            set_pref('oui_quote_cache_set', $now);
            !$quote ?: set_pref('oui_quote_body', $quote);
            !$by ?: set_pref('oui_quote_author', $by);
            !$cite ?: set_pref('oui_quote_cite', $cite);
            !$url ?: set_pref('oui_quote_url', $url);
        }

    // Cache is set and is not outdated.
    } else {
        $quote = get_pref('oui_quote_body');
        $cite = get_pref('oui_quote_cite');
        $by = get_pref('oui_quote_author');
        $url = get_pref('oui_quote_url');
    }

    if ($thing === null) {
        $service
            ? $reference = '<br /><cite>'.($cite ? $cite : '').' via '.($url ? href($via, $url) : $via).'</cite>'
            : $reference = ($cite ? '<br /><cite>'.($url ? href($cite, $url) : $cite).'</cite>' : '');

        $data = '<blockquote>'.$quote.'</blockquote>'.n.'<figcaption>'.$by.n.$reference.'</figcaption>';
    } else {
        $data = parse($thing);
    }

    return (($label) ? doLabel($label, $labeltag) : '').n.doTag($data, $wraptag, $class);
}

/**
 * Display the body of the quote.
 */
function oui_quote_body($atts)
{
    global $quote;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => 'blockquote',
    ), $atts));

    return ($wraptag) ? doTag($quote, $wraptag, $class) : $quote;
}

/**
 * Display the reference of the quote.
 */
function oui_quote_cite($atts)
{
    global $cite, $via, $url, $service;

    extract(lAtts(array(
        'service' => $service,
        'class'   => '',
        'wraptag' => 'cite',
    ), $atts));

    $service == 1
        ? $reference = ($cite ? $cite : '').' via '.($url ? href($via, $url) : $via)
        : $reference = ($cite ? ($url ? href($cite, $url) : $cite) : '');

    return ($wraptag) ? doTag($reference, $wraptag, $class) : $reference;
}

/**
 * Display the author of the quote.
 */
function oui_quote_author($atts)
{
    global $by;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => 'span',
    ), $atts));

    return ($wraptag) ? doTag($by, $wraptag, $class) : $by;
}
