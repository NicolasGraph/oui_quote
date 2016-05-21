<?php

$plugin['name'] = 'oui_quote';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.1.3';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'https://github.com/NicolasGraph';
$plugin['description'] = 'Display a custom quote or pull one from a web service';

$plugin['order'] = 5;

$plugin['type'] = 5;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use.
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

// $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;
$plugin['flags'] = 3;

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
$plugin['textpack'] = <<< EOT
#@public
#@language en-gb
oui_quote => Quote
oui_quote_services => Service
oui_quote_body => Quote
oui_quote_cite => Reference
oui_quote_author => Author
oui_quote_url => Url
oui_quote_cache_time => Cache time in minutes
oui_quote_quotes_on_design => Random quote from Quotes on Design (en)
oui_quote_service_quotes_on_design => Quotes on Design
oui_quote_they_said_so => Quote of the day from They Said So (en)
oui_quote_service_they_said_so => They Said So
oui_quote_le_monde => Quote of the day from Le Monde (fr)
oui_quote_service_le_monde => Le Monde
oui_quote_le_figaro => Quote of the day from Le Figaro (fr)
oui_quote_service_le_figaro => Le Figaro
#@language fr-fr
oui_quote => Citation
oui_quote_services => Service
oui_quote_body => Citation
oui_quote_cite => Référence
oui_quote_author => Author
oui_quote_url => Url
oui_quote_cache_time => Durée du cache en minutes
oui_quote_quotes_on_design => Citation aléatoire de Quotes on Design (en)
oui_quote_service_quotes_on_design => Quotes on Design
oui_quote_they_said_so => Citation du jour de They Said So (en)
oui_quote_service_they_said_so => They Said So
oui_quote_le_monde => Citation du jour du Monde (fr)
oui_quote_service_le_monde => Le Monde
oui_quote_le_figaro => Citation du jour du Figaro (fr)
oui_quote_service_le_figaro => Le Figaro
EOT;

if (!defined('txpinterface'))
    @include_once('zem_tpl.php');

if (0) {

?>
# --- BEGIN PLUGIN HELP ---
h1. oui_quote

Easily display your own quote or pull one from the following services:
* "Le Figaro":http://evene.lefigaro.fr/citations (fr).
* "Le Monde":http://le_monde.lemonde.fr/ (fr);
* "Quotes on Design":http://quotesondesign.com/ (en);
* "They Said So":https://theysaidso.com/ (en);

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Preferences":#prefs
* "Tags":#tags
** "oui_quote":#oui_quote
** "oui_quote_body":#oui_quote_body
** "oui_quote_author":#oui_quote_author
** "oui_quote_cite":#oui_quote_cite
* "Examples":#examples
** "Single tag":#single_tag
** "Container tag":#container_tag
* "Author":#author
* "Licence":#licence

h2(#requirements). Plugin requirements

oui_quote’s minimum requirements:

* Textpattern 4.5+

h2(#installation). Installation

# Paste the content of the plugin file under the *Admin > Plugins*, upload it and install;
# Click _Options_ or visit your *Admin>Preferences* tab to fill the plugin prefs.

h2(#prefs). Preferences / options

* *Service* — _Default: none_ - The service you want to use to pull the quote;
* *Quote* — _Default: unset_ - The quote in use (automatically filled if a service is selected);
* *Reference* — _Default: unset_ - The reference of the quote in use (automatically filled by Le Monde only);
* *Author* — _Default: unset_ - The author of the quote (automatically filled if a service is selected);
* *Url* — _Default: unset_ - The url of the quote source (automatically filled if a service is selected);
* *Cache time* — _Default: 60_ - Duration of the cache in minutes; avoid too many external queries.

h2(#tags). Tags

h3(#oui_quote). oui_quote

bc. <txp:oui_quote />

or

bc. <txp:oui_quote>
[…]
</txp:oui_quote>

Displays the quote.

h4. Attributes

_(Alphabetical order)_

* @class="…"@ – _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.
* @service="…"@ - _Default: 1 if a service is selected, or 0_ - display the name/link of/to the service from which the quote was pulled (usually required, read terms and conditions of use of the service in use);
* @wraptag="…"@ - _Default: figure_ (see "here":http://alistapart.com/blog/post/more-thoughts-about-blockquotes-than-are-strictly-required) - The HTML tag to use around the generated content.

h3(#oui_quote_body). oui_quote_body

bc. <txp:oui_quote_body />

Displays the body of the quote.

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the @img@ HTML tag or to the HTML tag assigned to @wraptag@.
* @wraptag="…"@ — _Default: blockquote_ - The HTML tag to use around the generated content.

h3(#oui_quote_author). oui_quote_author

bc. <txp:oui_quote_author />

Displays the author.

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @wraptag="…"@ — _Default: span_ - The HTML tag to use around the generated content.

h3(#oui_quote_cite). oui_quote_cite

bc. <txp:oui_quote_cite />

Displays the refernce of the quote and the service from which it was pulled.
If an the url preference is filled, it will wrap the service or the source into a link.

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @service="…"@ - _Default: 1 (inherited from the container tag) - display the name/link of the service from which the quote was pulled (usually required, read terms and conditions of use of the service in use);
* @wraptag="…"@ — _Default: cite_ - The HTML tag to use around the generated content.

h2(#examples). Examples

h3(#single_tag). Example 1: single tag use

bc. <txp:oui_quote label="Citation du jour" labeltag="h1" />

when used with Le Monde will return:

bc.. <h1>Citation du jour</h1>
<figure>
    <p>Ce n'est pas parce-que vous êtes nombreux à avoir tort que vous avez raison.</p>
    <figcaption>
        <span>Bernard Werber</span>
        <cite>Le Mystère des dieux (2007) via <a href="http://dicocitations.lemonde.fr/item-5133.html">Le Monde</a></cite>
    </figcation>
</figure>

h3(#container_tag). Example 2: container tag use

The previous example with the use of a container tag would look like:

bc. <txp:oui_quote label="Citation du jour" labeltag="h1">
    <txp:oui_quote_body />
    <figcaption>
        <txp:oui_quote_author />
        <txp:oui_quote_cite />
    </figcaption>
</txp:oui_quote>

h2(#author). Author

"Nicolas Morand":https://github.com/NicolasGraph
_Thank you to the Textpattern community and the core team._

h2(#licence). Licence

This plugin is distributed under "GPLv2":http://www.gnu.org/licenses/gpl-2.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

/**
 * Register tags for Txp 4.6+.
 */
if (class_exists('\Textpattern\Tag\Registry')) {
    Txp::get('\Textpattern\Tag\Registry')
        ->register('oui_quote')
        ->register('oui_quote_body')
        ->register('oui_quote_cite')
        ->register('oui_quote_author');
}

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
function oui_quote_pophelp($evt, $stp, $ui, $vars) {
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
            function_exists('remove_pref')
                ? remove_pref(null, 'oui_quote')
                : safe_delete('txp_prefs', "event='oui_quote'");
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
function oui_quote_preflist() {
    $prefList = array(
        'oui_quote_services' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'oui_quote_sercices_select',
            'position'   => '10',
            'is_private' => false,
        ),
        'oui_quote_body' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'text_input',
            'position'   => '20',
            'is_private' => false,
        ),
        'oui_quote_author' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'text_input',
            'position'   => '30',
            'is_private' => false,
        ),
        'oui_quote_cite' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'text_input',
            'position'   => '40',
            'is_private' => false,
        ),
        'oui_quote_url' => array(
            'value'      => '',
            'event'      => 'oui_quote',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
            'widget'     => 'text_input',
            'position'   => '50',
            'is_private' => false,
        ),
        'oui_quote_cache_time' => array(
            'value'      => '60',
            'event'      => 'oui_quote',
            'visibility' => defined('PREF_PLUGIN') ? PREF_PLUGIN : PREF_ADVANCED,
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

function oui_quote_install() {

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
function oui_quote_sercices_select($name, $val) {
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
function oui_quote_options() {
    $url = defined('PREF_PLUGIN') ? '?event=prefs' : '?event=prefs&step=advanced_prefs';
    header('Location: ' . $url);
}

/**
 * Force pulled data injection in the prefs fields on service selection.
 *
 * if service has changed;
 * or the quote field is empty;
 * or the cache is outdated.
 */
function oui_quote_inject_data() {

    if (($_POST['oui_quote_services'] !== get_pref('oui_quote_services')) || (!get_pref('oui_quote_body') || (time() - get_pref('oui_quote_cache_set')) > ($_POST['oui_quote_cache_time'] * 60))) {
        switch ($_POST['oui_quote_services']) {
            case 'oui_quote_service_they_said_so':
                unset($_POST['oui_quote_body'], $_POST['oui_quote_cite'], $_POST['oui_quote_author'], $_POST['oui_quote_url']);
                $feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
                set_pref('oui_quote_body', $feed->contents->quotes[0]->{'quote'});
                set_pref('oui_quote_cite', '');
                set_pref('oui_quote_author', $feed->contents->quotes[0]->{'author'});
                set_pref('oui_quote_url', 'https://theysaidso.com');
                break;
            case 'oui_quote_service_quotes_on_design':
                unset($_POST['oui_quote_body'], $_POST['oui_quote_cite'], $_POST['oui_quote_author'], $_POST['oui_quote_url']);
                $feed = json_decode(file_get_contents('http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'));
                set_pref('oui_quote_body', strip_tags($feed[0]->{'content'}));
                set_pref('oui_quote_cite', '');
                set_pref('oui_quote_author', $feed[0]->{'title'});
                set_pref('oui_quote_url', $feed[0]->{'link'});
                break;
            case 'oui_quote_service_le_monde':
                unset($_POST['oui_quote_body'], $_POST['oui_quote_cite'], $_POST['oui_quote_author'], $_POST['oui_quote_url']);
                $feed = simplexml_load_string(file_get_contents('http://dicocitations.lemonde.fr/xml-rss2.php'));
                set_pref('oui_quote_url', $feed->channel->item->link);
                $feed = preg_split( "/(\[|\])/", strip_tags($feed->channel->item->description));
                set_pref('oui_quote_body', trim($feed[0]));
                set_pref('oui_quote_cite', trim($feed[2]));
                set_pref('oui_quote_author', trim($feed[1]));
                break;
            case 'oui_quote_service_le_figaro':
                unset($_POST['oui_quote_body'], $_POST['oui_quote_cite'], $_POST['oui_quote_author'], $_POST['oui_quote_url']);
                $feed = simplexml_load_string(file_get_contents('http://evene.lefigaro.fr/rss/citation_jour.xml'));
                set_pref('oui_quote_url', $feed->channel->item->link);
                $feed = preg_split( "/ - /", strip_tags($feed->channel->item->title));
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
function oui_quote($atts, $thing=null) {
    global $quote, $author, $cite, $via, $url, $service;

    $services = get_pref('oui_quote_services');
    $via = gTxt($services);

    extract(lAtts(array(
        'service' => ($via ? 1 : 0),
        'wraptag'    => 'figure',
        'class'      => '',
        'label'      => '',
        'labeltag'   => '',
    ),$atts));

    $cache_time = get_pref('oui_quote_cache_time');
    $now = time();

    // No quote stored ot outdated cache.
    $needquery = (!empty($via) && ($now - get_pref('oui_quote_cache_set')) > ($cache_time * 60)) ? true : false;

    if ($needquery) {
        switch ($services) {
            case 'oui_quote_service_they_said_so':
                $feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
                $quote = $feed->contents->quotes[0]->{'quote'};
                $author = $feed->contents->quotes[0]->{'author'};
                $url = 'https://theysaidso.com';
                break;
            case 'oui_quote_service_quotes_on_design':
                $feed = json_decode(file_get_contents('http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'));
                $quote = strip_tags($feed[0]->{'content'});
                $author = $feed[0]->{'title'};
                $url = $feed[0]->{'link'};
                break;
            case 'oui_quote_service_le_monde':
                $feed = simplexml_load_string(file_get_contents('http://dicocitations.lemonde.fr/xml-rss2.php'));
                $url = $feed->channel->item->link;
                $feed = preg_split( "/(\[|\])/", strip_tags($feed->channel->item->description));
                $quote = $feed[0];
                $cite = $feed[2];
                $author = $feed[1];
                break;
            case 'oui_quote_service_le_figaro':
                $feed = simplexml_load_string(file_get_contents('http://evene.lefigaro.fr/rss/citation_jour.xml'));
                $url = $feed->channel->item->link;
                $feed = preg_split( "/ - /", strip_tags($feed->channel->item->title));
                $quote = $feed[1];
                $author = $feed[0];
                break;

            update_lastmod();
        }

        // Cache needed.
        if ($cache_time > 0) {
            // Time stamp and store the new data in the prefs.
            set_pref('oui_quote_cache_set', $now);
            !$quote ?: set_pref('oui_quote_body', $quote);
            !$author ?: set_pref('oui_quote_author', $author);
            !$cite ?: set_pref('oui_quote_cite', $cite);
            !$url ?: set_pref('oui_quote_url', $url);
        }

    // Cache is set and is not outdated.
    } else {
        $quote = get_pref('oui_quote_body');
        $cite = get_pref('oui_quote_cite');
        $author = get_pref('oui_quote_author');
        $url = get_pref('oui_quote_url');
    }

    if ($thing === null) {

        $service
            ? $reference = '<br /><cite>'.($cite ? $cite : '').' via '.($url ? href($via, $url) : $via).'</cite>'
            : $reference = ($cite ? '<br /><cite>'.($url ? href($cite, $url) : $cite).'</cite>' : '');

        $data = '<blockquote>'.$quote.'</blockquote>'.n.'<figcaption>'.$author.n.$reference.'</figcaption>';

    } else {

        $data = parse($thing);

    }

    return (($label) ? doLabel($label, $labeltag) : '').n.doTag($data, $wraptag, $class);
}

/**
 * Display the body of the quote.
 */
function oui_quote_body($atts) {
    global $quote;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => 'blockquote',
    ),$atts));

    return ($wraptag) ? doTag($quote, $wraptag, $class) : $out;
}

/**
 * Display the reference of the quote.
 */
function oui_quote_cite($atts) {
    global $cite, $via, $url, $service;

    extract(lAtts(array(
        'service' => $service,
        'class'   => '',
        'wraptag' => 'cite',
    ),$atts));

    $service == 1
        ? $reference = ($cite ? $cite : '').' via '.($url ? href($via, $url) : $via)
        : $reference = ($cite ? ($url ? href($cite, $url) : $cite) : '');

    return ($wraptag) ? doTag($reference, $wraptag, $class) : $out;
}

/**
 * Display the author of the quote.
 */
function oui_quote_author($atts) {
    global $author;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => 'span',
    ),$atts));

    return ($wraptag) ? doTag($author, $wraptag, $class) : $out;
}
# --- END PLUGIN CODE ---

?>
