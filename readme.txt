=== Admin Helper ===
Contributors: lucdecri
Tags: framework, admin pages, custom taxonomy, custom type, custom media
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.83
License: GPLv2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ND25N8WPAC2W8

Fornisce alcune funzioni utili per creare custom posttype, personalizzare le taxonomie e i mediatype

== Description ==
Attivando questo plugin sono disponibili le seguenti funzioni :

+ *register_post_box*($posttype,$boxname,$description,$position,$priority,$form_fields)
	inserisce un box con customfield per i posttype specificati. I customfields creati sono automaticamente salvati e sono presenti come campi nell'oggetto $post
	*TODO* Utilizzo di un array per posttype (ora accetta un solo posttype)
	*TODO* Sanificare i nomi dei campi. Ritornare un array con i noi dei campi sanificati
+ *register_attachment_field*($form_field)
	Inserisce un custom field in un post media_type. Il customfield creati sono automaticamente salvati e sono presenti nell'oggetto $post
	*TODO* tutto da verificare
+ *register_taxonomy_fields*($taxonomy, $fields)
	inserisce una serie di customfields affiuntivi ad una tassonomia. I customfields registrati sono automaticamente salvati e sono presenti come campi dell'oggetto $taxonomy restituito dalle varie funzioni wp
	*TODO* sanificare il nome dei campi e restituire i nomi sanificati
+ *add_taxonomy_meta*($term,$taxonomy,$name,$value)
	assegna da un campo di un termine di una tassonomia un valore. 
+ *admin_debug*($var, $name)
	stampa in un'area di debug una variabile. Se e' presente debug_bar installato, usa l'area predisposta, altrimenti crea un'area al di sotto del footer
+ *admin_check_url*($url)
	ritorna true se l'url della pagina e' quello passato tramite $url
+ *admin_scandirectory*($dir)
	ritorna una array con i nomi dei file di una cartella
+ *set_post_meta*($id,$name,$value)
	aggiunge o aggiorna un customfield (come update_post_meta, ma funziona anche se il customfield non esiste)
+ *admin_table_post*($options,$columns, $post_array)
        genera una tabella simile a quella utilizzata per "Tutti i post", solo con i risultati della query in $post_array. 
+ *admin_move_menu*($menutomove_slug,$menumoveafter_slug)
        sposta il menu con slug $menutomove_slug dopo il menu $menumoveafter_slug
+ *add_menu_div*($menuafter_slug)
        aggiunge un separatore dopo il menu con slug $menuafter_slug


possiede anche altre funzioni per la realizzazione di pagine dal lato admin


Assicurati di verificare che ADMIN_HELPER is defined and in correct revision, before activate your plugin!!


== Installation ==
1. Upload `admin-helper` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use new function in your plugin/template



== Frequently Asked Questions ==

nothing yet!


== Change Log ==

*version 0.83*
-bugfix on data field


*version 0.82*
-bugfix sui campi di tipo file
-correzione dimensione degli inputbox di tipo text
-correzione datepicker
-correzione textarea di tipo formattedtext


*version 0.8*
-bugfix su admin_table_posts

*version 0.7*
-aggiunto admin_move_menu
-aggiunto add_menu_div

*version 0.6*
- Aggiunto admin_table_posts
- Inserito select&text come tipo di campo
- inserito alcuni hook per modificare le opzioni dei campi nei box al momento del rendering
- bugfix

