<?php

$plugin['name'] = 'oui_quote';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '0.1.0-beta';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'https://github.com/NicolasGraph';
$plugin['description'] = 'Display a custom quote or pull one from some web services';

$plugin['order'] = 5;

$plugin['type'] = 1;

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
oui_quote_services => Source
oui_quote_text => Quote
oui_quote_author => Author
oui_quote_cache_time => Default cache time
oui_quote_hash_key => Cache file hash key
#@language fr-fr
oui_quote => Citation
oui_quote_services => Source
oui_quote_text => Citation
oui_quote_author => Author
oui_quote_cache_time => Durée du cache par défaut
oui_quote_hash_key => Clé de hachage du fichier cache
EOT;

if (!defined('txpinterface'))
    @include_once('zem_tpl.php');

if (0) {

?>
# --- BEGIN PLUGIN HELP ---
h1. oui_quote

Easily display your own quote or pull one from "Quotes on design":http://quotesondesign.com/ or "They said so":https://theysaidso.com/.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Preferences":#prefs
* "Tags":#tags
** "oui_quote":#oui_quote
** "oui_quote_text":#oui_quote_text
** "oui_quote_author":#oui_quote_image_author
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

* Service — _Default: none_ - 
* Quote — _Default: unset_ - 
* Author — _Default: unset_ - 
* Cache time — _Default: 0_ - Duration of the cache in seconds.

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

* @break="…"@ - _Default: li_ - The HTML tag used around each generated image.
* @class="…"@ – _Default: oui_quote_images_ - The css class to apply to the HTML tag assigned to @wraptag@.
* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.
* @wraptag="…"@ - _Default: ul_ - The HTML tag to use around the generated content.

h3(#oui_quote_text). oui_quote_text

Displays the quote without the author.

bc. <txp:oui_quote_text />

h4. Attributes 

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the @img@ HTML tag or to the HTML tag assigned to @wraptag@.
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h3(#oui_quote_author). oui_quote_author

Displays the author.

bc. <txp:oui_quote_author />

h4. Attributes

_(Alphabetical order)_

* @class="…"@ — _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@. 
* @wraptag="…"@ — _Default: unset_ - The HTML tag to use around the generated content.

h2(#examples). Examples

h3(#single_tag). Example 1: single tag use

bc. <txp:oui_quote />

h3(#container_tag). Example 2: container tag use

bc. <txp:oui_quote>
    <txp:oui_quote_text />
    <txp:oui_quote_author />
</txp:oui_quote>

h2(#author). Author

"Nicolas Morand":https://github.com/NicolasGraph

h2(#licence). Licence

This plugin is distributed under "GPLv2":http://www.gnu.org/licenses/gpl-2.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

if (class_exists('\Textpattern\Tag\Registry')) {
    // Register Textpattern tags for TXP 4.6+.
    Txp::get('\Textpattern\Tag\Registry')
        ->register('oui_quote')
        ->register('oui_quote_text')
        ->register('oui_quote_author');
}

if (txpinterface === 'admin') {
    add_privs('prefs.oui_quote', '1');
    add_privs('plugin_prefs.oui_quote', '1');
    register_callback('oui_quote_welcome', 'plugin_lifecycle.oui_quote');
    register_callback('oui_quote_install', 'prefs', null, 1);
    register_callback('oui_quote_options', 'plugin_prefs.oui_quote', null, 1);
}

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
            safe_delete('txp_lang', "name LIKE 'oui\_instagram%'");
            break;
    }
}

function oui_quote_install() {
    if (get_pref('oui_quote_services', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_quote_services', '', 'oui_quote', PREF_PLUGIN, 'oui_quote_sercices_select', 10);
        } else {
            set_pref('oui_quote_services', '', 'oui_quote', PREF_ADVANCED, 'oui_quote_sercices_select', 10);
        }
    }
    if (get_pref('oui_quote_text', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_quote_text', '', 'oui_quote', PREF_PLUGIN, 'text_input', 20);
        } else {
            set_pref('oui_quote_text', '', 'oui_quote', PREF_ADVANCED, 'text_input', 20);
        }
    }
    if (get_pref('oui_quote_author', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_quote_author', '', 'oui_quote', PREF_PLUGIN, 'text_input', 30);
        } else {
            set_pref('oui_quote_author', '', 'oui_quote', PREF_ADVANCED, 'text_input', 30);
        }
    }
    if (get_pref('oui_quote_cache_time', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_quote_cache_time', '0', 'oui_quote', PREF_PLUGIN, 'text_input', 40);
        } else {
            set_pref('oui_quote_cache_time', '0', 'oui_quote', PREF_ADVANCED, 'text_input', 40);
        }
    }
    if (get_pref('oui_quote_hash_key', null) === null) {
        if (defined('PREF_PLUGIN')) {
            // Txp 4.6
            set_pref('oui_quote_hash_key', mt_rand(100000, 999999), 'oui_quote', PREF_PLUGIN, 'text_input', 50);
        } else {
            set_pref('oui_quote_hash_key', mt_rand(100000, 999999), 'oui_quote', PREF_ADVANCED, 'text_input', 50);
        }
    }
}

function oui_quote_sercices_select($name, $val) {
    $vals = array('quotes_on_design'=> 'Quotes on design', 'they_said_so'=> 'They said so');
    return selectInput($name, $vals, $val, '1', '1');
}

function oui_quote_options() {
    if (defined('PREF_PLUGIN')) {
        $link = '?event=prefs';
    } else {
        $link = '?event=prefs&step=advanced_prefs';
    }
    header('Location: ' . $link);
}

function oui_quote($atts, $thing=null) {
    global $username, $thisquote;

    extract(lAtts(array(
        'link'       => 'auto',
        'cache_time' => get_pref('oui_quote_cache_time'),
        'wraptag'    => 'ul',
        'class'      => 'oui_quote_images',
        'break'      => 'li',
        'label'      => '',
        'labeltag'   => '',
    ),$atts));

    $hash_key =  get_pref('oui_quote_hash_key');

    // Prepare cache variables
    $cachedate = get_pref('oui_quote_cache_set');
    $cacheexists = get_pref('oui_quote_text') ? true : false;

    $needcache = (($cache_time > 0) && ((!$cacheexists) || (time() - $cachedate) > ($cache_time * 60))) ? true : false;
    $readcache = ((!$needcache) && ($cache_time > 0) && ($cacheexists)) ? true : false;

    // Cache_time is not set, or a new cache file is needed; throw a new request
    if ($needcache || $cache_time == 0) {

	    switch (get_pref('oui_quote_services')) {
			case '':
				$quote = get_pref('oui_quote_text');
				$author = get_pref('oui_quote_author');
				break;
	        case 'they_said_so':
	        	$feed = json_decode(file_get_contents('http://quotes.rest/qod.json'));
				$quote = $feed->contents->quotes[0]->{'quote'};
				$author = $feed->contents->quotes[0]->{'author'};
				set_pref('oui_quote_text', $quote);
				set_pref('oui_quote_author', $author);
	        	break;
	        case 'quotes_on_design':
				$feed = json_decode(file_get_contents('http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1'));
				$quote = strip_tags($feed[0]->{'content'});
				$author = $feed[0]->{'title'};
				set_pref('oui_quote_text', $quote);
				set_pref('oui_quote_author', $author);
	        	break;	
	    }

        // …and check the result
        if(isset($quote)){

            // single tag use
            if ($thing === null) {

                $data[] = '<blockquote>'.n.'<p>'.$quote.'</p>'.n.'</blockquote>'.n.'<span>'.$author.'</span>';
                $out = (($label) ? doLabel($label, $labeltag) : '').\n
                       .doWrap($data, $wraptag, $break, $class);

            // Conatiner tag use
            } else {
                $data[] = parse($thing);
                $out = (($label) ? doLabel($label, $labeltag) : '').\n
                       .doWrap($data, $wraptag, $break, $class);
            }
            
        } else {
            trigger_error("oui_quote was unable to get any content.");
            return;
        }
    }
    // Cache file is needed
    if ($needcache) {
        // Time stamp and write the new cache files and return
        set_pref('oui_quote_cache_set', time(), 'oui_quote', PREF_HIDDEN, 'text_input');
        set_pref('oui_quote_text', $quote);
        set_pref('oui_quote_author', $author);
    }

    // Cache is on and file is found, get it!
    if ($readcache) {
        $quote = get_pref('oui_quote_text');
        $author = get_pref('oui_quote_author');

        $data[] = '<blockquote>'.n.'<p>'.$quote.'</p>'.n.'</blockquote>'.n.'<span>'.$author.'</span>';
        $cache_out = (($label) ? doLabel($label, $labeltag) : '').\n
               .doWrap($data, $wraptag, $break, $class);        
        return $cache_out;
    // No cache file :(
    } else {
        return $out;
    }
}

function oui_quote_text($atts) {
    global $thisquote;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => '',
    ),$atts));

    $quote = $thisquote->{'quote'};

    return ($wraptag) ? doTag($quote, $wraptag, $class) : $out;
}

function oui_quote_author($atts) {
    global $thisquote;

    extract(lAtts(array(
        'class'   => '',
        'wraptag' => '',
    ),$atts));

    $author = $thisquote->{'author'};

    return ($wraptag) ? doTag($author, $wraptag, $class) : $out;
}

# --- END PLUGIN CODE ---

?>