<?php

$plugin['name'] = 'oui_quote';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.1.0-beta';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'https://github.com/NicolasGraph';
$plugin['description'] = 'Display a custom quote or pull one from some web services';

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
oui_quote_text => Quote
oui_quote_cite => Source
oui_quote_author => Author
oui_quote_cache_time => Cache time in minutes
oui_quote_quotes_on_design => Random quote from Quotes on Design (en)
oui_quote_they_said_so => Quote of the day from They Said So (en)
oui_quote_dicocitations => Quote of the day from Le Monde (fr)
oui_quote_le_figaro => Quote of the day from Le Figaro (fr)
#@language fr-fr
oui_quote => Citation
oui_quote_services => Service
oui_quote_text => Citation
oui_quote_cite => Source
oui_quote_author => Author
oui_quote_cache_time => Durée du cache en minutes
oui_quote_quotes_on_design => Citation aléatoire de Quotes on Design (en)
oui_quote_they_said_so => Citation du jour de They Said So (en)
oui_quote_dicocitations => Citation du jour du Monde (fr)
oui_quote_le_figaro => Citation du jour du Figaro (fr)
EOT;

if (!defined('txpinterface'))
    @include_once('zem_tpl.php');

if (0) {

?>
# --- BEGIN PLUGIN HELP ---
h1. oui_quote

Easily display your own quote or pull one from "Quotes on Design":http://quotesondesign.com/ or "They Said So":https://theysaidso.com/.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Preferences":#prefs
* "Tags":#tags
** "oui_quote":#oui_quote
** "oui_quote_text":#oui_quote_text
** "oui_quote_cite":#oui_quote_cite
** "oui_quote_author":#oui_quote_author
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
* *Quote* — _Default: unset_ - The quote in use (automatically filled after saving if a service is selected);
* *Source* — _Default: unset_ - The source of the quote in use (automatically filled by Le Monde only);
* *Author* — _Default: unset_ - The author of the quote (automatically filled after saving if a service is selected);
* *Cache time* — _Default: 60_ - Duration of the cache in minutes; avoid too many external queries.

h2(#tags). Tags

h3(#oui_quote). oui_quote

Displays the quote.

bc. <txp:oui_quote />

or

bc. <txp:oui_quote>
[…]
</txp:oui_quote>

h4. Attributes

_(Alphabetical order)_

* @class="…"@ – _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.
* @wraptag="…"@ - _Default: blockquote_ - The HTML tag to use around the generated content.

h3(#oui_quote_text). oui_quote_text

Displays the body of the quote.

bc. <txp:oui_quote_text />

h4. Attributes 

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the @img@ HTML tag or to the HTML tag assigned to @wraptag@.
* @wraptag="…"@ — _Default: p_ - The HTML tag to use around the generated content.

h3(#oui_quote_cite). oui_quote_cite

Displays the source of the quote if available.

bc. <txp:oui_quote_cite />

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@. 
* @wraptag="…"@ — _Default: cite_ - The HTML tag to use around the generated content.

h3(#oui_quote_author). oui_quote_author

Displays the author.

bc. <txp:oui_quote_author />

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@. 
* @wraptag="…"@ — _Default: span_ - The HTML tag to use around the generated content.

h2(#examples). Examples

h3(#single_tag). Example 1: single tag use

bc. <txp:oui_quote />

will return:

bc.. <blockquote>
    <p>The quote.</p>
    <footer>
        <span>The author</span>, <cite>The reference</cite>
    </footer>    
</blockquote>

h3(#container_tag). Example 2: container tag use

bc. <txp:oui_quote>
    <txp:oui_quote_text />
    <txp:oui_quote_cite />
    <txp:oui_quote_author />
</txp:oui_quote>

h2(#author). Author

"Nicolas Morand":https://github.com/NicolasGraph
_Thank you to the "Textpattern core team":http://textpattern.com/patrons and "the CMS community":http://forum.textpattern.com/._ 

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
        ->register('oui_quote_text')
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
            if (function_exists('remove_pref')) {
                // Txp 4.6
                remove_pref(null, 'oui_quote');
            } else {
                safe_delete('txp_prefs', "event='oui_quote'");
            }
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
function oui_quote_install() {
    if (get_pref('oui_quote_services', null) === null) {
        if (defined('PREF_PLUGIN')) {
            set_pref('oui_quote_services', '', 'oui_quote', PREF_PLUGIN, 'oui_quote_sercices_select', 10);
        } else {
            set_pref('oui_quote_services', '', 'oui_quote', PREF_ADVANCED, 'oui_quote_sercices_select', 10);
        }
    }
    if (get_pref('oui_quote_text', null) === null) {
        if (defined('PREF_PLUGIN')) {
            set_pref('oui_quote_text', '', 'oui_quote', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_quote_text', '', 'oui_quote', PREF_ADVANCED, 'text_input', 20);
        }
    }
    if (get_pref('oui_quote_author', null) === null) {
        if (defined('PREF_PLUGIN')) {
            set_pref('oui_quote_cite', '', 'oui_quote', PREF_PLUGIN, 'text_input', 30);
        } else {
            set_pref('oui_quote_cite', '', 'oui_quote', PREF_ADVANCED, 'text_input', 30);
        }
    }
    if (get_pref('oui_quote_author', null) === null) {
        if (defined('PREF_PLUGIN')) {
            set_pref('oui_quote_author', '', 'oui_quote', PREF_PLUGIN, 'text_input', 40);
        } else {
            set_pref('oui_quote_author', '', 'oui_quote', PREF_ADVANCED, 'text_input', 40);
        }
    }
    if (get_pref('oui_quote_cache_time', null) === null) {
        if (defined('PREF_PLUGIN')) {
            set_pref('oui_quote_cache_time', '60', 'oui_quote', PREF_PLUGIN, 'text_input', 50);
        } else {
            set_pref('oui_quote_cache_time', '60', 'oui_quote', PREF_ADVANCED, 'text_input', 50);
        }
    }
    if (get_pref('oui_instagram_cache_set', null) === null) {
        set_pref('oui_instagram_cache_set', time(), 'oui_instagram', PREF_HIDDEN, 'text_input', 60);
    }
}

/**
 * Set Services pref function using selectInput()
 */
function oui_quote_sercices_select($name, $val) {
    $vals = array('quotes_on_design' => gTxt('oui_quote_quotes_on_design'), 'they_said_so' => gTxt('oui_quote_they_said_so'), 'dicocitations' => gTxt('oui_quote_dicocitations'), 'le_figaro' => gTxt('oui_quote_le_figaro'));
    return selectInput($name, $vals, $val, '1', '1');
}

/**
 * Jump to the prefs panel.
 */
function oui_quote_options() {
    if (defined('PREF_PLUGIN')) {
        $link = '?event=prefs';
    } else {
        $link = '?event=prefs&step=advanced_prefs';
    }
    header('Location: ' . $link);
}

/**
 * Force pulled data injection in the prefs fields.
 * 
 * if service has changed;
 * or the quote field is empty;
 * or the cache is outdated.
 */
function oui_quote_inject_data() {
	
    if (($_POST['oui_quote_services'] !== get_pref('oui_quote_services')) || (!get_pref('oui_quote_text') || (time() - get_pref('oui_quote_cache_set')) > ($_POST['oui_quote_cache_time'] * 60))) {
	    switch ($_POST['oui_quote_services']) {
	        case 'they_said_so':
	        	unset($_POST['oui_quote_text'], $_POST['oui_quote_cite'], $_POST['oui_quote_author']);
	        	$feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
				set_pref('oui_quote_text', $feed->contents->quotes[0]->{'quote'});
				set_pref('oui_quote_cite', '');
				set_pref('oui_quote_author', $feed->contents->quotes[0]->{'author'});
	        	break;
	        case 'quotes_on_design':
	        	unset($_POST['oui_quote_text'], $_POST['oui_quote_cite'], $_POST['oui_quote_author']);
				$feed = json_decode(file_get_contents('http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'));
				set_pref('oui_quote_text', strip_tags($feed[0]->{'content'}));
				set_pref('oui_quote_cite', '');
				set_pref('oui_quote_author', $feed[0]->{'title'});
	        	break;
	        case 'dicocitations':
	        	unset($_POST['oui_quote_text'], $_POST['oui_quote_cite'], $_POST['oui_quote_author']);
				$feed = simplexml_load_string(file_get_contents('http://dicocitations.lemonde.fr/xml-rss2.php'));
				$feed = preg_split( "/(\[|\])/", strip_tags($feed->channel->item->description));
				set_pref('oui_quote_text', $feed[0]);
				set_pref('oui_quote_cite', $feed[2]);
				set_pref('oui_quote_author', $feed[1]);
	        	break;
	        case 'le_figaro':
	        	unset($_POST['oui_quote_text'], $_POST['oui_quote_cite'], $_POST['oui_quote_author']);
				$feed = simplexml_load_string(file_get_contents('http://evene.lefigaro.fr/rss/citation_jour.xml'));
				$feed = preg_split( "/ - /", strip_tags($feed->channel->item->title));
				set_pref('oui_quote_text', $feed[1]);
				set_pref('oui_quote_cite', '');
				set_pref('oui_quote_author', $feed[0]);
	        	break;	        	
	    }
	    unset($_POST['oui_quote_cache_set']);	    
	    set_pref('oui_quote_cache_set', time());
	}
}

/**
 * Main plugin function.
 * 
 * Pull the quote if needed;
 * store data in the prefs fields;
 * display the content.
 */
function oui_quote($atts, $thing=null) {
    global $quote, $cite, $author;

    extract(lAtts(array(
        'wraptag'    => 'blockquote',
        'class'      => '',
        'label'      => '',
        'labeltag'   => '',
    ),$atts));

    $cache_time = get_pref('oui_quote_cache_time');
    
    // No quote stored ot outdated cache.
    $needquery = ((!get_pref('oui_quote_text') || (time() - get_pref('oui_quote_cache_set')) > ($cache_time * 60)) ? true : false);

    if ($needquery) {
	    switch (get_pref('oui_quote_services')) {
	        case 'they_said_so':
	        	$feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
				$quote = $feed->contents->quotes[0]->{'quote'};
				$cite = '';
				$author = $feed->contents->quotes[0]->{'author'};
	        	break;
	        case 'quotes_on_design':
				$feed = json_decode(file_get_contents('http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'));
				$quote = strip_tags($feed[0]->{'content'});
				$cite = '';
				$author = $feed[0]->{'title'};
	        	break;
	        case 'dicocitations':
				$feed = simplexml_load_string(file_get_contents('http://dicocitations.lemonde.fr/xml-rss2.php'));
				$feed = preg_split( "/(\[|\])/", strip_tags($feed->channel->item->description));
				$quote = $feed[0];
				$cite = $feed[2];
				$author = $feed[1];
	        	break;
	        case 'le_figaro':
				$feed = simplexml_load_string(file_get_contents('http://evene.lefigaro.fr/rss/citation_jour.xml'));
				$feed = preg_split( "/ - /", strip_tags($feed->channel->item->title));
				$quote = $feed[1];
				$cite = '';
				$author = $feed[0];
	        	break;	
			default:
				$quote = get_pref('oui_quote_text');
				$cite = get_pref('oui_quote_cite');
				$author = get_pref('oui_quote_author');
				break;	    
		}
		update_lastmod();

	    // Cache needed.
	    if ($cache_time > 0) {
	        // Time stamp and store the new data in the prefs.
	        set_pref('oui_quote_cache_set', time());
	        set_pref('oui_quote_text', $quote);
	        set_pref('oui_quote_text', $cite);
	        set_pref('oui_quote_author', $author);
	    }

	// Cache is set and is not outdated, data exists.  
    } else if (!$needquery && $cache_time > 0) {
        $quote = get_pref('oui_quote_text');
        $cite = get_pref('oui_quote_cite');
        $author = get_pref('oui_quote_author');
    }

    // single tag use.
    if ($thing === null) {
        $data = '<p>'.$quote.'</p>'.n.'<footer>'.$author.($cite ? n.'<cite>'.$cite.'</cite>' : '').'</footer>';
        $out = (($label) ? doLabel($label, $labeltag) : '').\n
               .doTag($data, $wraptag, $class);
    // Conatiner tag use.
    } else {
        $data = parse($thing);
        $out = (($label) ? doLabel($label, $labeltag) : '').\n
               .doTag($data, $wraptag, $class);
    }
    
    return $out;
}

/**
 * Display the body of the quote.
 */
function oui_quote_text($atts) {
    global $quote;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => 'p',
    ),$atts));

    return ($wraptag) ? doTag($quote, $wraptag, $class) : $out;
}

/**
 * Display the reference of the quote.
 */
function oui_quote_cite($atts) {
    global $cite;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => 'cite',
    ),$atts));

    return ($wraptag) ? doTag($cite, $wraptag, $class) : $out;
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