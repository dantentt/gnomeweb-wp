<?php
/*
Plugin Name: WPPO
Description: A hack to make wordpress become a multilingual site using gettext
Version: 0.1
Author: Lincoln de Sousa <lincoln@comum.org>
Author URI: http://lincoln.comum.org
License: AGPLv3
*/

/* Copyright 2010  Lincoln de Sousa <lincoln@comum.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once ("wppo.genxml.php");

define (PO_DIR, ABSPATH . "po/");
define (POT_FILE, PO_DIR . "gnomesite.pot");

/* Setting up where compiled po files are located and which translation
 * domain to use. */
bindtextdomain ('gnomesite', PO_DIR);
bind_textdomain_codeset ('gnomesite', 'UTF-8');
textdomain ('gnomesite');

/* This action will be fired when a post/page is updated. It's used to
 * update (regenerate, actually) the pot file with all translatable
 * strings of the gnome.org website. */
function wppo_update_pot_file ($post) {
  $xml_file = PO_DIR . "gnomesite.xml";
  file_put_contents ($xml_file, wppo_generate_po_xml ());
  exec ("/usr/bin/xml2po -o " . POT_FILE . " $xml_file");
}
add_action ('post_updated', 'wppo_update_pot_file');


/* this action will be fired when damned lies system send an updated version of
 * a po file. This function needs to take care of creating the translated
 * xml file and separate its content to the wordpress database */
function wppo_receive_po_file () {
  if ($handle = opendir (PO_DIR)) {
    while (false !== ($po_file = readdir ($handle))) {
    
      /* Gets all the .po files from PO_DIR. Then it will generate a translated
       * XML for each language.
       */
      if (strpos ($po_file, '.po', 1) !== false && strpos ($po_file, '.pot', 1) === false) {
        $po_file_array = explode ('.', $po_file);
        
        /* Arranging the name of the translated xml to something like
         * "gnomesite.pt-br.xml".
         */
        $translated_xml_file = PO_DIR . 'gnomesite.' . implode ('.', array_pop ($po_file_array)) . '.xml';
        
        exec ("/usr/bin/xml2po -p $po_file -o $translated_xml_file " . POT_FILE);
        
        $translated_xml = file_get_contents ($translated_xml_file);
        
        /* TODO
         * We still don't do anything other than generating the translated XML.
         * We have to store this in a database table, separating the posts.
         */
      }
    }
  
  }

}

/* Using gettext to get the translated version of received strings */
function wppo_get_translated_string ($content) {
  $lang = isset ($_REQUEST['lang']) ? $_REQUEST['lang'] : $_COOKIE['lang'];
  if (!$lang)
    return $content;

  setlocale (LC_MESSAGES, $lang);

  /* If there's a new line in the content, we use wpautop() function,
   * because the script that generates the xml with translatable strings
   * has to call it otherwise we'll lose paragraphs inserted by the
   * user. */
  if (stristr ($content, "\n") === FALSE)
    return gettext ($content);
  else
    $content = wpautop ($content);

  /* Parsing the content to split up <p> tags */
  $newct = '';
  $parser = xml_parser_create ();
  xml_parse_into_struct ($parser, "<r>$content</r>", $vals, $index);
  foreach ((array) $index['P'] as $p)
    $newct .= "<p>" . gettext ($vals[$p]['value']) . "</p>\n";
  xml_parser_free ($parser);
  return $newct;
}
add_filter ('the_title', 'wppo_get_translated_string', 1);
add_filter ('the_content', 'wppo_get_translated_string', 1);

/* Saving the language code choosen by the user */
if (isset ($_REQUEST['lang']))
  setcookie ("lang", $_REQUEST['lang'], time () + 36000, "/");

/* A nice url to show the pot file */
if (isset ($_REQUEST['pot'])) {
  header ("Content-Type: text/plain");
  die (file_get_contents (PO_DIR . "gnomesite.pot"));
}

?>
