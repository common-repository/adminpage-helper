<?php
/*
Plugin Name: admin page Helper
Description: Some function to help developer to write admin page e panel   
Plugin URI:  http://www.decristofano.it/
Version:     0.83
Author:      lucdecri
Author URI:  http://www.decristofano.it/
*/

define('ADMIN_HELPER','0.83');

function ah_first() {
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
	if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
		array_splice($active_plugins, $this_plugin_key, 1);
		array_unshift($active_plugins, $this_plugin);
		update_option('active_plugins', $active_plugins);
	}
}



function admin_menu($parent, $page_title, $menu_title, $function_name, $menu_slug, $position='', $capability='edit_plugins', $icon_url='') {
	// add a panel in wordpres menu
	
	
	switch ($parent) {
		case '' : $parent = ''; break;
		case 'Dashboard': $parent='index.php'; break;
		case 'Posts': $parent='edit.php'; break;
		case 'Media': $parent='upload.php'; break;
		case 'Links': $parent='link-manager.php'; break;
		case 'Pages': $parent='edit.php?post_type=page'; break;
		case 'Comments': $parent='edit-comments.php'; break;
		case 'Appearance': $parent='themes.php'; break;
		case 'Plugins': $parent='plugins.php'; break;
		case 'Users': $parent='users.php'; break;
		case 'Tools': $parent='tools.php'; break;
		case 'Settings': $parent='options-general.php'; break;
	}
	
	if ($parent=='') 
		add_menu_page   (          $page_title, $menu_title, $capability, $menu_slug, $function_name, $icon_url, $position );
	else
		add_submenu_page( $parent, $page_title, $menu_title, $capability, $menu_slug, $function_name ); 
}

function admin_panel($name, $action, $title, $description, $info='', $localization='') {
  // create a form for admin panel
    echo '<div class="wrap">
    		<h2>'.$title.'</h2>
    		<h5>'.$info.'</h5>
    		<p>'.$description.'</p>
			<p><form name="'. $name.'" action="'.$action.'" method="post" id="'.$name.'">
			<fieldset>
    			<div class="UserOption">
   					<input type="hidden" name="page" value="'.$name.'" />';
}

function admin_field($id, $type, $options, $default, $localization='', $message='', $return=false ) {
  // add a field in form for admin panel
  //   type is the field type :
  //      littlenumber
  //      text
  //      color
  //      page : a page-break in the admin panel
  //      longtext
  //	  checkbox
  //	  hidden
  //	  button
  //	  readonly
  //	  date
  //	  file
  //	  files
  //      formattedtext
	
	
    if (is_array($options)) {
        $text  = $options['description'];
        $text2 = $options['values'];
        $text3 = $options['conclusion'];
        $default2 = $options['default2'];
    } else {
        @list($text,$text2,$text3) = @explode('|',$options,3);
    }
    $string = '<div class="field_wrapper form-field">';
    switch($type) {
	case 'break':
	    $string.= '<p></p>';
	break;
        case 'color':
        	$string.= '
          <label for="'.$id.'" class="label">'.__($text,$localization).'</label>
          #<input type="text" id="'.$id.'" maxlength="6" name="'.$id.'" value="'.$default.'"
	      size="6" onchange="ChangeColor(\''.$id.'_box\',this.value)" onkeyup="ChangeColor(\''.$id.'_box\',this.value)"/>
	      <span id="'.$id.'_box" style="background:#'.$default.'">&nbsp;&nbsp;&nbsp;</span>
	      <p>'.__($message,$localization).'</p>';
        break;
	case 'date':
	       $default= date_create_from_format('Y-m-d',$default);
	       if(!$default) $default=date_create();
		$default= $default->format('d-m-Y');
		$string.= '
		<label for="'.$id.'" class="label">'.__($text,$localization).'</label>
		<input type="text" name="'.$id.'" id="'.$id.'" value="'.$default.'" class="Datepicker" style="width:100px;"/>
		<p>'.__($message,$localization).'</p>
		';
	break;
	case 'file':
	    $string.= '<label for="'.$id.'">'.__($text,$localization).'</label>';
            if ($default) $string.="&nbsp;<a href='$default'>$default</a> <input type='checkbox' name='{$id}_delete' id='$id' />".__('Delete',$localization);
            else $string.='<div class="file-input"><input id="'.$id.'" class="async-upload" type="file" name="'.$id.'" /></div>';
            $string.='<p>'.__($message,$localization).'</p>';
	break;
	case 'files':
	    $string.= '<label for="'.$id.'[]">'.__($text,$localization).'</label>
		<div class="new-files">
		    <div class="file-input"><input id="'.$id.'[]" class="async-upload" type="file" name="'.$id.'[]" /></div>
		    <a class="admin-add-file" href="javascript:void(0)">' . _('Add more files') . '</a>
		</div>
		<p>'.__($message,$localization).'</p>';
	    $string.= '<script type="text/javascript">
		jQuery(document).ready(function($) {
			// add more file
			$(".admin-add-file").click(function(){
				var $first = $(this).parent().find(".file-input:first");
				$first.clone().insertAfter($first).show();
				return false;
			});
		});
		</script>';
	break;

        case 'page':
		$string.= '
             	</div>
            	</fieldset>
	            <br />
                <fieldset>
	            <legend><b>'.__($text,$localization).'</b></legend>
	            <div class="Option">
                <p><i>'.__($message,$localization).'</i></p>';
        break;
        case 'select':
		$string.= '
		<label for="'.$id.'" class="label">'.__($text,$localization).'</label>
          <select class="field_select" id="'.$id.'" name="'.$id.'" >';
          	$options = explode(",",$text2);
          	foreach($options as $opt) {
				$v=$opt;$k=$opt;
				
          			if (strpos($opt,':')) list($k,$v)=explode(':',$opt,2);
				if ($k=='') $k=$v;
				
				if ($default==$k)	$d = ' selected="selected" ';
          			else			$d = ' ';
				
          			$string.= "<option value='$k' $d >".__($v,$localization)."</option>";
          	}
          $string.= '
          </select>
          <p>'.__($message,$localization).'</p>';
        break;
        case 'select&text':
		$string.= '
		<label for="'.$id.'" class="label">'.__($text,$localization).'</label>
          <select class="field_select" id="'.$id.'" name="'.$id.'" >';
          	$options = explode(",",$text2);
          	foreach($options as $opt) {
				$v=$opt;$k=$opt;
				
          			if (strpos($opt,':')) list($k,$v)=explode(':',$opt,2);
				if ($k=='') $k=$v;
				
				if ($default==$k)	$d = ' selected="selected" ';
          			else			$d = ' ';
				
          			$string.= "<option value='$k' $d >".__($v,$localization)."</option>";
          	}
          $string.= '
          </select>
          <input type="text" maxlength="300" name="'.$id.'_option" id="'.$id.'_option" value="'.$default2.'" style="width:150px;" />'.$text3.'<br />
          <p>'.__($message,$localization).'</p>';
        break;
        case 'littlenumber':
	case 'smallesttext':
	  $string.= '
          <label class="label" for="'.$id.'">'.__($text,$localization).'</label><input type="text" maxlength="5" name="'.$id.'" value="'.$default.'" style="width:50px;" /> '.__($text2,$localization).'<br />
	   <p>'.__($message,$localization).'</p>';
        break;
        case 'smalltext':
	  $string.= '<label class="label" for="'.$id.'">'.__($text,$localization).'</label>
          <input type="text" maxlength="100" name="'.$id.'" id="'.$id.'" value="'.$default.'" style="width:100px;" /> '.__($text2,$localization).'<br />
	  <p>'.__($message,$localization).'</p>';
        break;
        case 'text':
          $string.= '
          <label class="label" for="'.$id.'">'.__($text,$localization).'</label>
          <input type="text" maxlength="300" name="'.$id.'" id="'.$id.'" value="'.$default.'"  />'.__($text2,$localization).'<br />
	  <p>'.__($message,$localization).'</p>';
        break;
        case 'hidden':
          $string.= '<input type="hidden" name="'.$id.'" id="'.$id.'" value="'.$default.'" />';
        break;
        case 'button':
        	// @TODO button non funziona
           $string.= '<input type="button" name="'.$id.'" id="'.$id.'" value="'.$text.'" />';
        break;
        case 'longtext':
		if ($text!='') 
			$string.= '<label for="'.$id.'" class="label">'.__($text,$localization).'</label><br />';
		$string.= '<textarea  name="'.$id.'" id="'.$id.'" cols="60" rows="5" style="width:99%">'.$default.'</textarea> '.__($text2,$localization).'<br />
			<p>'.__($message,$localization).'</p>';
        break;
        case 'formattedtext':
		if ($text!='') 
			$string.= '<label for="'.$id.'" class="label">'.__($text,$localization).'</label><br />';
		
                $string.= '<textarea  name="'.$id.'" id="'.$id.'" cols="60" rows="5" style="width:99%" class="additional-info form-input-tip code" size="20" autocomplete="off" >'.$default.'</textarea> '.__($text2,$localization).'<br />
			<p>'.__($message,$localization).'</p>';
        break;
       /* 
        <textarea cols="16" rows="5" id="dt-additional-info" name="dt-additional-info" class="additional-info form-input-tip code" size="20" autocomplete="off" tabindex="6" style="width:90%"/><?php echo wpautop( $value['additional-info'] ); ?></textarea>
                        <table id="post-status-info" cellspacing="0" style="line-height: 24px;">
                            <tbody>
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                            </tbody>
                        </table>
       */
       case 'readonly':
	  $size = strlen($default);  
          $string.= '
          <label class="label" for="'.$id.'">'.__($text,$localization).'</label>
	  <input type="hidden" name="'.$id.'" id="'.$id.'" value="'.$default.'" />
	  <span style="color:darkgray; font-family: Consolas,Monaco,monospace; font-style: italic;border: solid 1px; background: none repeat scroll 0 0 #EAEAEA;">&nbsp;'.$default.'&nbsp;</span>'.__($text2,$localization).'<br />
          <p>'.__($message,$localization).'</p>';
        break;
        case 'checkbox':
		$string.= '<label for="'.$id.'" class="label">'.__($text,$localization).'</label><input type="checkbox" name="'.$id.'" id="'.$id.'" ';
		if($default == '1') { $string.= ' checked="checked" '; }
		$string.= ' /><br /><p>'.__($message,$localization).'</p>';
        break;
    }
    $string.= '</div>';
    
    if ($return) return $string;
    else	 echo   $string;
}

function admin_field_save($name, $val=null) {
// save option named $name, if $val is defined
	$old = get_option($name);
	if ($val!=null) {
		if (get_option($name,'')!='') {
			update_option($name,$val);
		} else {
			add_option($name,$val,'',true);
		}
	}
}

function admin_field_save_post_meta($id,$name,$value) {
// save post meta named $name. Create new if don't exist, overwrite if exist
  if (!get_post_meta($id,$name)) 
        add_post_meta($id,$name,$value,true);
  else 
        update_post_meta($id,$name,$value);
}

function admin_field_save_post_meta_multiple($id,$name,$value) {
// save post meta named $name. Create new if don't exist, add new if exist
        add_post_meta($id,$name,$value,true);
}


function admin_table($columns_name) {
// create a table with specified columns
	echo '<table class="wp-list-table widefat fixed users" cellspacing="0">
		<thead>
			<tr>';
	foreach($columns_name as $col) echo '	<th>'.$col.'</th>';
	echo '		</thead>
		<tbody>';

}

function admin_table_row($row) {
// add row to table
	echo '<tr>';
	foreach($row as $td) echo '	<td>'.$td.'</td>';
	echo '</tr>';
}

function admin_table_close($columns_name) {
// add footer to table and close it
	echo '			</tbody>
				<tfoot>';
	foreach($columns_name as $col) echo '	<th>'.$col.'</th>';
	echo '</tfoot>
	     </table>';
}

function admin_panel_close() {
  // close a form of admin panel
  echo '
    </fieldset>
    '.submit_button().'	
    </form>
    </p>
</div>
  ';
}

// ritorna i files in una cartella
function admin_scandirectory( $dirname = '.' ) { 
		$files = array(); 
		if( $handle = opendir( $dirname ) ) { 
			while( false !== ( $file = readdir( $handle ) ) ) {
				$info = pathinfo( $file );
                if ( isset($info['extension']) )
					   $files[] = utf8_encode( $file );
			}		
			closedir( $handle ); 
		} 
		sort( $files );
		return ( $files ); 
} 

function admin_fileinformation( $name ) {
		
		//Sanitizes a filename replacing whitespace with dashes
		$name = sanitize_file_name($name);
		
		//get the parts of the name
		$filepart = pathinfo ( strtolower($name) );
		
		if ( empty($filepart) )
			return false;
		
		// required until PHP 5.2.0
		if ( empty($filepart['filename']) ) 
			$filepart['filename'] = substr($filepart['basename'],0 ,strlen($filepart['basename']) - (strlen($filepart['extension']) + 1) );
		
		$filepart['filename'] = sanitize_title_with_dashes( $filepart['filename'] );
		
		//extension jpeg will not be recognized by the slideshow, so we rename it
		$filepart['extension'] = ($filepart['extension'] == 'jpeg') ? 'jpg' : $filepart['extension'];
		
		//combine the new file name
		$filepart['basename'] = $filepart['filename'] . '.' . $filepart['extension'];
		
		return $filepart;
}



function admin_widget($slug,$title,$widget_form,$widget_save) {
 // create a admin widget
 
 // alias
 wp_add_dashboard_widget($slug, $title, $widget_form,$widget_save);	

 // hook with add_action('wp_dashboard_setup', 'function_name' );

	
}


// e' true se la pagina ha l'url specificato
function admin_check_url($url='') {
	if (!$url) return;
	
	$_REQUEST_URI = explode('?', $_SERVER['REQUEST_URI']);
	$url_len 	= strlen($url);
	$url_offset = $url_len * -1;
	// If out test string ($url) is longer than the page URL. skip
	if (strlen($_REQUEST_URI[0]) < $url_len) return;
	if ($url == substr($_REQUEST_URI[0], $url_offset, $url_len))
			return true;
}

// inserisce un termine di tassonomia, specificando anche custom field
function admin_insert_term($term,$taxonomy,$args) {
       
       wp_insert_term($term,$taxonomy,$args);       
       unset($args['description'],$args['parent'],$args['slug']);
       $data = serialize($args);
       $name = 'field_'.$taxonomy.'_'.$term;
       if (get_option($name,'')!='') {
		update_option($name,$data);
       } else {
		add_option($name,$data,'',true);
       }
}

// ritorna il valore di un termine di tassonomia
function admin_get_term_field_value($field,$term,$taxonomy) {
		$name = 'field_'.$taxonomy.'_'.$term;
		$args = unserialize(get_option($name));
		return $args[$field];
}
// ritorna tutti i field di una tassonomia
function admin_get_term_fields($term,$taxonomy) {
		$name = 'field_'.$taxnomomy.'_'.$term;
		$args = unserialize(get_option($name));
		return array_keys($args);
}

function admin_debug($var,$name='', $permanent=false) {
    global $admin_debug_data;
	if (function_exists('dbgx_trace_var')) {
		if ($name=='') dbgx_trace_var( $var );
		else		dbgx_trace_var( $var,$name );
	} else {
	    $bt = debug_backtrace();
	    $refer = $bt[0]['file']."@".$bt[0]['line'];
	    $string = print_r($var,true);
	    $admin_debug_data[]=array(
		    'name' => $name, "var" => $string, "type" => 'info', "time" => microtime(true), 'refer' => $refer
		    );
	}
        if ($permanent) {
            $md = get_option('admin_debug', array());
            $md[]=array($name,$var); 
            update_option('admin_debug', $md);
}
}

function admin_debug_reset() {
    delete_option('admin_debug');
}

function admin_set_post_meta($id,$name,$value) {
    set_post_meta($id,$name,$value);
}

// funzione utile
function set_post_meta($id,$name,$value) {
  if (!get_post_meta($id,$name)) 
        add_post_meta($id,$name,$value,true);
  else 
        update_post_meta($id,$name,$value);
}

// add meta field to taxonomy
function add_taxonomy_meta($term,$taxonomy,$name,$value) {
       $name = 'field_'.$taxonomy.'_'.$term;
       $data = get_option($name,'');
       if ($data=='') return;
       $data[$name]=$value;
       update_option($name,$data);
}


// add custom fields for taxonomy user interface
function register_taxonomy_fields($taxonomy,$fields) {
    global $admin_taxonomy_data;
    
    $admin_taxonomy_data[$taxonomy]= $fields;
       // aggiungo gli hook alla tassonomia $taxonomy
    add_action($taxonomy.'_add_form_fields', 'ah_taxonomy_add');
    add_action($taxonomy.'_edit_form_fields', 'ah_taxonomy_edit');
    add_action('edited_'.$taxonomy, 'ah_taxonomy_save');
    add_action('created_'.$taxonomy, 'ah_taxonomy_save');
    add_action('get_'.$taxonomy,'ah_taxonomy_get',1,2);
    
}


// aggiunge i campi alla taxonomia, quando edito la tassonomia
function ah_taxonomy_edit($term) {
    global $admin_taxonomy_data;
    $taxonomy = $term->taxonomy;
    foreach($admin_taxonomy_data[$taxonomy] as $meta => $data) {
	admin_field($meta,$data['type'],$data['label'],$term->$meta,'',$data['message']);
    }
/*
     echo '<tr class="form-field">
            <th valign="top" scope="row">
            <label for="disabilita">Disabilita</label>
            </th>
            <td>
                <input id="disabilita" type="text"  size="40" value="'.$term->disabled.'" name="disabilita">
                <p class="description">Campi disabilitati nel post (id separati da virgole).</p>
            </td>
        </tr>';
    echo '<tr class="form-field">
            <th valign="top" scope="row">
            <label for="nascondi">Nascondi</label>
            </th>
            <td>
                <input id="nascondi" type="text"  size="40" value="'.$term->hidden.'" name="nascondi">
                <p class="description">Campi disabilitati nel post (id separati da virgole).</p>
            </td>
        </tr>';
    echo '<tr class="form-field">
            <th valign="top" scope="row">
            <label for="ordinamento">Campo di ordinamento</label>
            </th>
            <td>
                <input id="ordinamento" type="text"  size="40" value="'.$term->orderfield.'" name="ordinamento">
                <p class="description">Indica rispetto quale campo effettuare l\'ordinamento.</p>
            </td>
        </tr>';
*/


}


// aggiunge i campi alla tassonomia organization, quando la creo nella finestra
function ah_taxonomy_add($taxonomy) {
    global $admin_taxonomy_data;

    foreach($admin_taxonomy_data[$taxonomy] as $meta => $data) {
	admin_field($meta,$data['type'],$data['label'],'','',$data['message']);
    }
}

// salva i dati della taxonomia 
function ah_taxonomy_save( $term_id ) {
    global $admin_taxonomy_data;

    // tutti i controlli sono gia' stati fatti, o almeno dovrebbe essere cos�
    $term = get_term_by('id',$term_id,$_POST['taxonomy']);
    $slug = $term->slug;
    $taxonomy = $term->taxonomy;

    $args=array();
    foreach($admin_taxonomy_data[$taxonomy] as $meta => $data) {
	$args[$meta]=$_POST[$meta];
    }    
    
    // definisco il nome e serializzo l'array
    $data = serialize($args);
    $name = 'field_'.$taxonomy.'_'.$slug;
    if (get_option($name,'')!='') {
	    update_option($name,$data);
    } else {
	    add_option($name,$data,'',true);
    }
    
}



// modifica l'oggetto term cosi' contiene anche i custom field
function ah_taxonomy_get($term,$taxonomy) {
    global $admin_taxonomy_data;

    // deserializzo i dati
    
    $name = 'field_'.$term->taxonomy.'_'.$term->slug;
    $args = unserialize(get_option($name));
    if (is_array($args))   foreach($args as $meta=>$value) $term->$meta = $value;
    
    return $term;
}

function ah_taxonomy_get_terms($terms, $id, $taxonomy) {
    
    foreach($terms as $term_id => $term) {
        $terms[$term_id] = ah_taxonomy_get($term,$taxonomy);
    }
        
    return $terms;
    
}

function register_attachment_fields($form_field) {
global $admin_attachment_data;
    foreach($form_field as $k=>$d)
	    $admin_attachment_data[$k]=$d;
}

// registra un box per un posttype
function register_post_box($posttype,$boxname,$description,$position,$priority,$form_fields) {
global $admin_post_data;
global $admin_boxes_data;
    
    foreach($form_fields as $k=>$d)
                $admin_post_data[$posttype][$boxname][$k]=$d;
    
    $admin_boxes_data[$boxname]= array(
			    'type' => $posttype,
			    'description' => $description,
			    'position' => $position,
			    'priority' => $priority
			);
}

function ah_add_boxes() {
global $admin_boxes_data;
    if ($admin_boxes_data==array()) return;
    foreach($admin_boxes_data as $boxname => $data)
	add_meta_box( $boxname, $data['description'], 'ah_add_box', $data['type'], $data['position'], $data['priority'], '' );
}


function ah_add_box($post,$box) {
/* il singolo campo ha le seguenti opzioni
 * type         => indica il tipo di campo. vedi admin_field
 * hidden       => se true, il campo e' nascosto a prescindere dal type
 * readonly     => se true, il campo e' in sola lettura a prescindere dal type
 * default      => indica il valore da assegnare al campo nel caso non ve ne sia uno memorizzato
 * option       => indica il valore da assegnare al campo opzionale nel caso non ve ne sia uno memorizzato
 * values       => indica i valori tra cui vengono scelte le opzioni (nel caso, ad esempio di una select)
 * description  => il testo di descrizione del campo
 * howto        => il testo di spiegazione
 * closer       => un testo subito dopo l'input @TODO closer option
 * automation   => va inserito nel tag dell'input @TODO automation option
 */
    
    
global $admin_post_data;

    $boxname=$box['id'];
    $post_type = $post->post_type;
    
    $fields = $admin_post_data[$post_type][$boxname];
    wp_nonce_field( plugin_basename( __FILE__ ), $post_type.'_'.$boxname.'_noncename' );
    foreach($fields as $name => $field) {
	      $field = apply_filters($post_type.'_field', $field, $name);
              $field = apply_filters($post_type.'_'.$boxname.'_field', $field, $name);
              if        (@$field['hidden'])     $type='hidden';
	      elseif    (@$field['readonly'])   $type='readonly';
	      else                              $type = $field['type'];
              
              $description = $field['description'];
              if (@$field['values']) $description .= '|'. $field['values'];
              if (@$field['closer']) $description .= '|'. $field['closer']; //@FIXME se values non e' presente, non e' corretto
	      
              $value = get_post_meta($post->ID,$name,true);
	      if (!$value) $value=@$field['default'];
	      if ($field['type']=='date') {
			//$value = date_create_from_format('Y-m-d',$value);
			//if (!is_object($value)) $value = new DateTime();
			//$value = $value->format('d-m-Y');
              }
              $value_option = get_post_meta($post->ID,$name.'_option',true);
              if (!$value_option) $value_option=@$field['option'];
	      
	      admin_field($name, $type, $description, $value, '', @$field['howto'] );
    }
}

// salva i dati del post.
// Purtroppo devo date una priorit√† tra di dati che ricevo.
// i dati in $_POST (relativi a quell'id) hanno la priorit√† su quelli in $post
function ah_post_save($post_id,$post) {
global $admin_post_data_value;
global $admin_post_data;

    // verifico se sto salvando da un mediatype(o elenco di post), da un form singolo o da codice
  $verified=false;
  //@TODO devo correggere anche la $files perchè così non funziona quando ho i mediatype
  if (isset($_POST['attachments'][$post_id])) 
        {$data=$_POST['attachments'][$post_id]; $verified=true; $files = @$_FILES[$post_id]; }// è un mediatype o un elenco di post
  elseif (isset($_POST['post_ID']) && ($_POST['post_ID']==$post_id) ) 	
        { $data=$_POST;  $files = @$_FILES; } // è da pannello admin
  elseif (isset($admin_post_data_value[$post_id]))  
        { $data=$admin_post_data_value[$post_id];$verified=true; $files=array(); } // è da codice ed è un update
  else	
        { $data=$admin_post_data_value[0];$verified=true; $admin_post_data_value[0]=NULL;} // √® da codice ed √® un inserimento o non ha campi custom 

  $type = $post->post_type;
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )   return;
  if ( $type != $data['post_type'] ) return;
  //if ( !current_user_can( 'edit_'.$type, $post_id )) return;  //@FIXME : dovrei usare il plurale per creare la capability
  
  foreach($admin_post_data[$type] as $boxname => $fields) {
    if ( wp_verify_nonce( @$_POST[$type.'_'.$boxname.'_noncename'], plugin_basename( __FILE__ ) ) || $verified ) {
	foreach($fields as $name => $field) {
	    if ($field['type']==='date') {
		$value = date_create_from_format('d-m-Y',$data[$name]);
		if (!is_object($value)) $value = new DateTime();
		$value = $value->format('Y-m-d');
	    } elseif ($field['type']==='file') {
                // mi calcolo comunque il percorso
                $uploaded = @$files[$name];
                $uploads = wp_upload_dir();
                $UPLOAD_REL = $field['position'];
                $UPLOAD_DIR = $uploads['basedir'].'/'.$UPLOAD_REL; 
                $UPLOAD_URL = $uploads['baseurl'].'/'.$UPLOAD_REL;
                // se non esiste creo la cartella per la posizione
		if (!is_dir($UPLOAD_DIR)){
			mkdir($UPLOAD_DIR, 0777);
	}
		$fileslist = admin_scandirectory( $UPLOAD_DIR ); // vedo cosa c'� nella cartella
                admin_debug($uploaded, 'file caricato',true);
                if (isset($data[$name.'_delete'])) {
                    // devo eliminare il file
                    $value = ''; // lo scollego al post-meta
                    $args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post_id ); 
                    $attachments = get_posts( $args );
                    if ($attachments) 
                        foreach ( $attachments as $atta ) 
                            if ($atta->guid==$old_file) wp_delete_attachment( $atta->post_id);
                } elseif($uploaded['error'] == UPLOAD_ERR_OK and is_uploaded_file($uploaded['tmp_name'])) {
                    // � stato caricato un file
                                admin_debug('nessuno', 'Errore',true);
                                $parent = $post_id;
 				$filepart = admin_fileinformation( $uploaded['name'] );	
				// creo il nome del file di destinazione
				$filebase = sanitize_file_name($field['pre-name']).sanitize_file_name($data['post_title']);	
				$filename = $filebase.'.'.$filepart['extension'];
				// check if this filename already exist in the folder
				admin_debug($filepart, 'informazioni',true);
                                $i = 2;
				while ( in_array( $filename, $fileslist ) ) {
					$filename = $filebase . '_' . $i++ . '.' .$filepart['extension'];
    }
				$file =  $UPLOAD_DIR.'/'.$filename;
				$url  =  $UPLOAD_URL.'/'.$filename;
				$path =  $UPLOAD_REL.'/'.$filename;
				move_uploaded_file($uploaded["tmp_name"], $file);
    
                                // creiamo il media-post
                                $wp_filetype = wp_check_filetype(basename($file), null );
                                $attachment = array(
     					'post_mime_type' => $wp_filetype['type'],
     					'post_title' => $data['post_title'],
     					'post_name' => preg_replace('/\.[^.]+$/', '', basename($file)),
     					'post_content' => $data['post_content'],
     					'post_excerpt' => $data['post_excerpt'],
     					'post_status' => 'inherit',
     					'post_parent' => $post->ID,
     					'guid' => $url
                                );
   
                                $attach_id = wp_insert_attachment( $attachment, $url, $parent );
                                // aggiorniamo i metadata
                                admin_set_post_meta($attach_id,'_wp_attached_file', $path);
                                admin_set_post_meta($attach_id,'_wp_attachment_metadata', 'a:0:{}');
                                $value = $url;
                                admin_debug($url, 'url',true);
                        } else $value = get_post_meta($post_id,$name,true);
            } else $value = $data[$name];
            // in ogni caso salva il postmeta
	    admin_set_post_meta($post_id,$name,$value);
	} // foreach $fields
    } // if $verified
  } // foreach $boxex per il $type
   
}

function ah_post_insert($data,$postarr) {
global $admin_post_data_value;
global $admin_post_data;
    $type = $data['post_type'];
    if (key_exists($type,$admin_post_data)) {
	foreach($admin_post_data[$type] as $boxname => $fields) {
	    foreach($fields as $name => $field) {
		    if (isset($postarr[$name])) $admin_post_data_value[$postarr['ID']][$name] = $postarr[$name];
		    else $admin_post_data_value[$postarr['ID']][$name] = get_post_meta($postarr['ID'],$name,true);
	    }
	}
    }
    $admin_post_data_value[$postarr['ID']]['post_type'] = $type;
    return $data;
}

function ah_post_get($post) {
global $admin_post_data;
    $type = $post->post_type;
    if (key_exists($type,$admin_post_data)) {
	foreach($admin_post_data[$type] as $boxname => $fields) {
	    foreach($fields as $name => $field) {
		    $value = get_post_meta($post->ID,$name,true);
		    if ($field['type']=='date') {
			$value = date_create_from_format('Y-m-d',$value);
			if (!is_object($value)) $value=new DateTime();
			$value = $value->format('d-m-Y');
		    }
		    $post->$name =  $value;
	    }
	}
    }
}

function ah_hooks() {
    add_action('activated_plugin', 'ah_first');
    add_action('admin_init','ah_init');
    add_filter('wp_footer','ah_footer');  
    add_filter('admin_footer','ah_footer');
    add_action('save_post','ah_post_save',5, 2);
    add_filter('wp_insert_post_data','ah_post_insert',null,2);
    add_filter("the_post", "ah_post_get", 5, 2);
    add_filter("attachment_fields_to_edit", "ah_attachment_fields_to_edit", null, 2);
    add_filter("attachment_fields_to_save", "ah_attachment_fields_to_save", null, 2);
    add_filter('get_the_terms','ah_taxonomy_get_terms',1,3);
    add_action('admin_enqueue_scripts','ah_load_script');
    add_action('plugins_loaded','ah_loaded');
    add_action('wp_before_admin_bar_render','ah_prefooter');
    add_action('post_edit_form_tag', 'ah_edit_form_tag');
}



function ah_attachment_fields_to_edit($form,$post) {
    return $form;
}

function ah_attachment_fields_to_save($post,$attachment) {
    return $post;
}

function ah_posttype_fields_to_edit($form,$post) {
    return $form;
}

function ah_posttype_fields_to_save($post,$attachment) {
    return $post;
}

function ah_load_script() {
    wp_register_script('admin_helper_js', plugins_url( 'admin_helper.js' , __FILE__ ));
    wp_register_script('jquery-style','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');  
    wp_register_script('tiny_mce.js',get_bloginfo('url').'/wp-includes/js/tinymce/langs/wp-langs-en.js');
    wp_register_script('wp-langs-en.js',get_bloginfo('url').'/wp-includes/js/tinymce/langs/wp-langs-en.js');
    wp_register_script('wp-langs-it.js',get_bloginfo('url').'/wp-includes/js/tinymce/langs/wp-langs-it.js');
    
    wp_enqueue_script('jquery');
    wp_enqueue_style('jquery-style');
    wp_enqueue_script('admin_helper_js');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('tiny_mce.js');
    wp_enqueue_script('wp-langs-en.js');
    wp_enqueue_script('wp-langs-en.js'); 
    
   wp_enqueue_style('jquery-style','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
 }

function ah_loaded() {
    global $admin_debug_data;
    global $admin_post_data;	

    	$admin_debug_data = array();
	$admin_post_data = array();

    
}

function ah_init() {
	// sistema la gestione degli errori e del debug
	if (!function_exists('dbgx_trace_var')) {
	    set_error_handler("ah_error");
	}
	wp_register_style('admin_helper_css', plugins_url( 'admin_helper.css' , __FILE__ ));
	wp_enqueue_style('admin_helper_css');

	// agginge eventuali box
	ah_add_boxes();
	
}

function ah_prefooter() {
global $admin_post_data;
global $admin_boxes_data;
global $admin_taxonomy_data;
global $admin_post_data_value;
global $menu;
global $submenu;


    // debug delle variabili globali
    admin_debug($admin_post_data,'admin_post_data');
    admin_debug($admin_post_data_value,'admin_post_data_value');
    admin_debug($admin_boxes_data,'admin_boxes_data');
    admin_debug($admin_taxonomy_data,'admin_taxonomy_data');
    admin_debug(get_option('admin_debug','-'),'debug permanent');

    admin_debug($menu,'WP MENU');
    admin_debug($submenu,'WP SUBMENU');
}

function ah_footer() {
global $admin_debug_data;

// carico i js che mi potrebbero servire

    echo '<script type="text/javascript">
	function ChangeColor(id,color) {
		jQuery(id).css("background-color","#"+color);
	}
	jQuery(document).ready(function(){
                jQuery(".datepicker").datepicker({ dateFormat: "dd-mm-yy" });
	});
        tinyMCE.init({
            mode : "textareas",
            language : "en"
        });
	</script>';

// stampa il debug, se non non ho altro
    if (count($admin_debug_data)==0) return '';
    $debug='';
    $debug.= '
	    <style>
			.debug_wrapper {
				width:80%;
				background:black;
				color : darkgray;
			}
			.debug_name {
				font-weight:bold;
				width : 60px;
			}
			.debug_time {
				font-style : italic;
			}
			.debug_log {
				color : blue;
			}
			.debug_message {
				color : yellow;
			}
			.debug_extend {
				background : #555555;
			}
			.debug_error {
				color : red;
			}
			.debug_warning {
				color : #FF7F00;
			}
			.debug_notice {
				color : #FFCC66;
			}
			
			.debug_strict {
				color : #FFCC66;
			}

			.debug_action {
				color : #9966FF;
			}
			
			.debug_filter {
				color : #6666CC;
			}
		
	    </style>
    ';
    $debug.= "<p class='debug_wrapper'>";
    foreach($admin_debug_data as $k => $line) {
			$data = htmlentities(print_r($line['var'],true));
			if (strlen($data)>100) $abstract=substr($data,0,100)." ...";
			else $abstract = $data;
			$data = nl2br("\n&nbsp;&nbsp;".$data);
			$debug.= "<span class='debug_{$line['type']}'><span class='debug_name'>{$k}:{$line['name']}</span>";
			$debug.= " @ ";
			$debug.= "<span class='debug_time'>".date('d-m-y H:i:s',$line['time'])."</span>";
			$debug.= "<span class='debug_view_refer'> # </span>";
			$debug.= "<span class='debug_refer'>".@$line['refer']." <br>&nbsp;&nbsp;&nbsp;</span>";
			$debug.= " &gt; ";
			$debug.= "<span class='debug_data'>$abstract</span>";
			$debug.= "<span class='debug_extend'>$data</span>";
			$debug.= "</span>";
			$debug.= '</span></br>';
	}
	$debug.= "</p>";
	$debug.= '
	<script>
	jQuery(document).ready(function() {
			jQuery(".debug_extend").hide();
			jQuery(".debug_refer").hide();
			
			jQuery(".debug_extend").click(function() {
				jQuery(this).toggle();
				jQuery(this).prev().toggle();
			});
			jQuery(".debug_view_refer").click(function() {
				jQuery(this).next().toggle();
			});
			jQuery(".debug_data").click(function() {
				jQuery(this).toggle();
				jQuery(this).next().toggle();
			});
	}); 
	</script>
	';
	// cancello cosi' ogni cosa la vedo una sola volta
	
    return $debug;
}


function ah_error($errno, $errstr, $errfile, $errline) {
global $admin_debug_data;
	switch ($errno) {
    case E_USER_ERROR:
    case E_ERROR:
        	$type='error';
        break;
    case E_USER_WARNING:
    case E_WARNING:
        	$type='warning';
        break;
    case E_USER_NOTICE:
    case E_NOTICE:
        	$type='notice';
        break;
	case E_DEPRECATED:
        	$type='deprecated';
    break;    
    case E_STRICT:
    		$type='strict';
    break;
    default:
        	$type='unknow:'.$errno;
        break;
    }
	$admin_debug_data[] = array(
		'name' => 'error', "var" => $errstr, "type" => $type, "time" => microtime(true), "refer" => $errfile."@".$errline
	); 
   return true;
}


/*
 *classe per gestire le tabelle di visualizzazione dei posttype 
 * 
 * 
 * 
 */


if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ah_posts_Table extends WP_List_Table {
    
   var $_cols;
   var $_ords;
   var $_acts;
    
    function __construct($options ){
        global $status, $page;
                
        $default = array (
            'singular'  => 'post',
            'plural'    => 'posts',
            'evidence'  => '',
            'comment'   => '',
            'ajax'      => false,
            'actions'   => array('edit'=> 'Edit')
        );
        $options = array_merge($default, $options);
        //Set parent defaults
        parent::__construct( $options);
        
    }
    
    
    function column_default($item, $column_name){
        //@TODO controllare meglio se la colonna esiste
       $my = get_post($item['ID']);
       return apply_filters('manage_'.$my->post_type.'_posts_custom_column', $column_name,$my->ID);
        
    }
    
    // nel caso del titolo richiamo una funzione diversa, visto che ho da aggiungere le azioni
    function column_title($item){
        
        //Build row actions
        $post = @$_REQUEST['post'];
        if ($post=='') $post = @$_REQUEST['page'];
        if ($post=='') $post = @$_REQUEST['ID'];
        foreach ($this->_acts as $name => $label) 
            $actions[$name] =  sprintf('<a href="?post=%s&action=%s&from=%s&position=%s">%s</a>',$item['ID'],$name,$post,$item['position'],$label);
        
        
        //Return the title contents
        return sprintf('<b>%s</b> %s <span style="color:silver">%s</span>%s',
                     @$item[$this->_args['evidence']],
            /*$1%s*/ $item['title'],
            /*$2%s*/ @$item[$this->_args['comment']],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
   // va lasciato cosi'
   function column_cb($item){
       admin_debug($item, 'Dati passati');
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label 
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    function set_culumns($cols = array()) {
	if ($cols==array()) $cols = array('cb' => '<input type="checkbox" />', 'title'     => 'Title');
	$this->_cols=$cols;
    }
    
    function set_ordinable_columns($ords = array()) {
	$this->_ords = $ords;
    }
    
    function get_columns(){
        return $this->_cols;
    }
    
    function get_sortable_columns() {
        return $this->_cols;
    }
    
    function set_actions($act = array() ) {
	$this->_acts = $act;
    }
     
    function get_bulk_actions() {
        return $this->_acts;
    }
    
    function display_tablenav( $which ) {
    // gestisco anche questo perchè devo togliere in nonce_field e bulk_actions, 
    // altrimenti mi sballano la pagina quando salvo
    
		//if ( 'top' == $which )
		//	wp_nonce_field( 'bulk-' . $this->_args['plural'] );
        if ( 'bottom' == $which) return;
?>
    
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<div class="alignleft actions">
			<?php //$this->bulk_actions( $which ); ?>
		</div>
<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
                 
<?php
                 
	}

    
    function prepare_items($posts) {
        
        $per_page = 5;
        

        $columns = $this->get_columns();
        $hidden = array(); //array('ID' => true);
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // creo un hook per gestire le azioni
        if (isset($_REQUEST['page'])) do_action('do_action', $this->current_action(), $_REQUEST['page'], $posts[$_REQUEST['position']]);

        $data=array();
        // riempio il file data, leggendo dai post
	foreach($posts as $id => $post) {
                foreach($this->_column_headers[0] as $col => $name)
                                if (isset($post->$col))     $data[$id][$col] = $post->$col;
                                else                            $data[$id][$col] = '-';
                $data[$id]['title'] = $post->post_title;
                $data[$id]['ID'] = $post->ID;
                $data[$id]['position'] = $id;
        }
        //immagino che i post siano gia' ordinati...
        //@TODO da verificare l'ordinamento degli array
        
        
        // preparo la paginazione
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}

// produce una tabella di visualizzazione dei post
// come parametri accetta l'array di query_post.
// non modifica il main-loop
function admin_table_posts($options, $columns, $query_array) {
       // definisco l'header della tabella
    
    $postsTable = new ah_Posts_Table($options);
    $postsTable->set_culumns($columns);
    $postsTable->set_ordinable_columns(@$options['order']);
    $postsTable->set_actions(@$options['actions']);
    // cerco i post da visualizzare e li metto in $posts
    $posts = get_posts($query_array); 
    
    //Fetch, prepare, sort, and filter our data...
    $postsTable->prepare_items($posts);
    $postsTable->display();
    
}

function admin_move_menu($menutomove, $menumoveafter) {
global $menu;
    
    $menumoveto=max(array_keys($menu));
    $menutomove_data=array();
    $menutomove_id=-1;
    $count = 0;
    // cerco il menu da spostare e l'id della nuova posizione
    foreach ($menu as $menuid => $menudata) {
            $count++;
            if (@$menudata[5]==$menutomove)      {$menutomove_data = $menudata;  $menutomove_id=$menuid;}
            if (@$menudata[5]==$menumoveafter)   {$menumoveto_id = $menuid+1;    $position=$count; }
    }
    // non ho trovato il menu da spostare
    if ($menutomove_data===array()) return;
    if ($menutomove_id==-1) return;
    // qui ho trovato cosa spostare
    
    // lo tolgo dalla posizione attuale
    unset($menu[$menutomove_id]);
    // se ho gia' lo spazio libero lo posiziono
    if (!isset($menu[$menumoveto_id])) $menu[$menumoveto_id] = $menutomove_data; 
    else {  // altrimenti devo farmi spazio
        $menu_a = array_slice($menu, 0,$position,true);
        $menu_b = array_slice($menu,$position); //fino alla fine
        
        $menu = array_merge($menu_a, array($menumoveto_id=>$menutomove_data),$menu_b);
    }
    ksort($menu);


}

// inserisce un divisore di menu' dopo il menu indicato 
function add_menu_div($after) {
global $menu;
    $id = 'separator'.rand(100,1000);
    $div = array (
    0 => '',
    1 => 'read',
    2 => $id,
    3 => '',
    4 => 'wp-menu-separator',
    5 => $id
    );
    $appendto=max(array_keys($menu));
    $menu[$appendto+10] = $div;
    admin_move_menu($id, $after);
    
}    

function ah_edit_form_tag() {
    echo ' enctype="multipart/form-data"';
}



ah_hooks();