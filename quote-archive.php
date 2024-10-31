<?php
/*
Plugin Name: Quote Archive
Author: Rodolfo Martínez
Author URI: http://www.escritoenelagua.com/
Version: 1.1
Description: It allows to view and manage a quotation archive. The user can add quotes and decide where to wiew them (post, pages, sidebar...)
Plugin URI: 

*/


//Parametrizar según idioma
$currentLocale = get_locale();
if(!empty($currentLocale)) {
$moFile = dirname(__FILE__) . "/languages/quote-archive-" . $currentLocale . ".mo";
if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('citas', $moFile);
}

global $wpdb;
$tabla_citas = $wpdb->prefix . "citas";


//Insertar código en los posts para ver una cita aleatoria
add_shortcode('ver-una-cita', 'ver_una_cita_shortcode');
function ver_una_cita_shortcode($atts) {
      return ver_una_cita();
}

//Insertar código en los posts para ver todas las citas
add_shortcode('ver-citas', 'ver_citas_shortcode');
function ver_citas_shortcode($atts) {
      return quote_archive_listar();
}


/* Crea un menú y sus opciones para el plugin */
add_action('admin_menu', 'quote_archive_menu');
function quote_archive_menu()
{ 
    add_menu_page(__('Gestión', 'citas'), __('Citas', 'citas'), 10 , 'quote-archive/quote-archive-manage.php', '', plugins_url('quote-archive/libro_thn.jpg'));
 		add_submenu_page('quote-archive/quote-archive-manage.php', __('Citas', 'citas'), __('Gestión', 'citas'), 10, 'quote-archive/quote-archive-manage.php');
		add_submenu_page('quote-archive/quote-archive-manage.php', __('Citas', 'citas'), __('Configuración', 'citas'), 10, 'quote-archive/quote-archive.php', 'quote_archive_settings');		

}



/* Configuración de las citas */
function quote_archive_settings() {
   if ($_POST) {
                if($_POST["ver"] == "")
			$_POST["ver"] = "si";
                if($_POST["donde"] == "")
			$_POST["donde"] = "fin";
                if($_POST["tam"] == "")
			$_POST["tam"] = "+1";
                if($_POST["color"] == "")
                        $_POST["color"] = "";
                if($_POST["estilo"] == "")
                        $_POST["estilo"] = "normal";
                if($_POST["parrafo"] == "")
                        $_POST["parrafo"] = "justify";
		update_option('ver', $_POST['ver']);
		update_option('donde', $_POST['donde']);
		update_option('tam', $_POST['tam']);
		update_option('estilo', $_POST['estilo']);
		update_option('color', $_POST['color']);
		update_option('parrafo', $_POST['parrafo']);
	}
	// Get options
	$ver = get_option('ver');
	$donde = get_option('donde');
	$tam = get_option('tam');
	$estilo = get_option('estilo');
	$color = get_option('color');
	$parrafo = get_option('parrafo');

?>
<div class="wrap">
<h2><?php  _e('Visualizar las citas', 'citas'); ?></h2>

<?php
//Mensaje de opciones actualizadas
	if ($_POST) {
echo '<div id="message" class="updated fade"><p>';
_e("Opciones actualizadas", 'citas');
echo '.</p></div>';
};

?>

<form target="_self" method="post">
<table width=70%>
<tr>
<td valign=top></td>
<td valign=top><h3><?php _e('Datos a visualizar', 'citas'); ?></h3></td>
<td width=3% valign=top></td>
<td valign=top><h3><?php _e("Valores actuales", 'citas'); ?></h3></td>
</tr>
<tr>
<td width=20% valign=top><strong><?php _e("Ver en el post", 'citas'); ?>: </strong></td>
<td valign=top>
<input name="ver" type="hidden" style="width:100%;" value="<?php echo $ver; ?>" />
<input type="radio" <?php if ($ver == 'si') echo ' checked'; ?> name="ver" value="si"><?php _e('Sí', 'citas'); ?><br/>
<input type="radio" <?php if ($ver == 'no') echo ' checked'; ?> name="ver" value="no"><?php _e('No', 'citas'); ?>
</td>
<td width=3%></td> 
<td width=20%><?php
 if ($ver == "si") _e('Sí', 'citas');
 if ($ver == "no") _e('No', 'citas');
 ?></td>
</tr>
<tr><td colspan=4>&nbsp;</td></tr>
<tr>
<td width=20% valign=top><strong><?php _e("Situar en", 'citas'); ?>: </strong></td>
<td valign=top>
<input name="donde" type="hidden" style="width:100%;" value="<?php echo $donde; ?>" />
<input type="radio" <?php if ($donde == 'ini') echo ' checked'; ?> name="donde" value="ini"><?php _e('Inicio', 'citas'); ?><br/>
<input type="radio" <?php if ($donde == 'fin') echo ' checked'; ?> name="donde" value="fin"><?php _e('Final', 'citas'); ?>
</td>
<td width=3%></td> 
<td width=20%><?php
 if ($donde == "ini") _e('Inicio', 'citas');
 if ($donde == "fin") _e('Final', 'citas');
 ?></td>
</tr>

<tr>
<td valign=top></td>
<td valign=top><h3><?php _e('Formato', 'citas'); ?></h3></td>
<td width=3% valign=top></td>
<td valign=top></td>
</tr>

<tr>
<td width=20% valign=top><strong><?php _e("Tamaño nombre autor", 'citas'); ?>: </strong></td>
<td valign=top>
<input name="tam" type="hidden" style="width:100%;" value="<?php echo $tam; ?>" />
<select name="tam">
<?php
$i=8;
while ($i<=30)
{ 
?>
    <option <?php if ($tam==$i) echo 'selected'; ?> value="<?php echo $i; ?>"><?php echo $i; ?> </option>
<?php
$i++;
}
?>
                    </select>
</td>
<td width=3%></td> 
<td width=20%><?php echo $tam; ?></td>
</tr>
<tr><td colspan=4>&nbsp;</td></tr>

<tr>
<td width=20$><strong><?php _e("Color nombre autor", 'citas'); ?>: </strong></td>
<td><input name="color" type="hidden" style="width:100%;" value="<?php echo $color; ?>" />
<select name="color">
<?php
$color1 = array ("", "#000000", "#696969", "#8B0000", "#FF4500", "#006400", "#FFFF00", "#FFFFFF");
$color2 = array (__("Neutro", 'citas'), __("Negro", 'citas'), __("Gris", 'citas'), __("Rojo oscuro", 'citas'), __("Naranja", 'citas'), __("Verde", 'citas'), __("Amarillo", 'citas'), __("Blanco", 'citas'));
$i = 0;
while ($i < 8)
{
?>
              <option <?php if ($color==$color1[$i]) echo 'selected'; ?> value="<?php echo $color1[$i]; ?>"><?php echo $color2[$i]; ?></option>
<?php
$i++;
}
?>
                 </select>
</td>
<td width=3%></td>
<td width=20%>
<span style="background-color: <?php echo $color; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
</td>
</tr>
<tr><td colspan=4>&nbsp;</td></tr>

<tr>
<td width=20%><strong><?php _e("Estilo nombre autor", 'citas'); ?>: </strong></td>
<td><input name="estilo" type="hidden" style="width:100%;" value="<?php echo $estilo; ?>" />
<select name="estilo">
<?php
$tipo1 = array ("normal", "em", "strong");
$tipo2 = array (__("Normal", 'citas'), __("Cursiva", 'citas'), __("Negrita", 'citas'));
$i = 0;
while ($i < 3)
{
?>
                    	<option <?php if ($estilo==$tipo1[$i]) echo 'selected'; ?> value="<?php echo $tipo1[$i]; ?>"><?php echo $tipo2[$i]; ?></option>
<?php
$i++;
}
?>
                    </select> 
</td>
<td width=3%></td>
<td width=20%><?php
$i = 0;
while ($i < 3)
{
 if ($estilo==$tipo1[$i]) echo $tipo2[$i];
 $i++;
}
 ?></td>
</tr>
<tr><td colspan=4>&nbsp;</td></tr>

<tr>
<td width=20%><strong><?php _e("Alineación", 'citas'); ?>: </strong></td>
<td><input name="parrafo" type="hidden" style="width:100%;" value="<?php echo $parrafo; ?>" />
<select name="parrafo">
<?php
$parr1 = array ("left", "right", "center", "justify");
$parr2 = array (__("Izquierda", 'citas'), __("Derecha", 'citas'), __("Centro", 'citas'), __("Justificada", 'citas'));
$i = 0;
while ($i < 4)
{
?>
                    	<option <?php if ($parrafo==$parr1[$i]) echo 'selected'; ?> value="<?php echo $parr1[$i]; ?>"><?php echo $parr2[$i]; ?></option>
<?php
$i++;
}
?>
                    </select> 
</td>
<td width=3%></td>
<td width=20%><?php
$i = 0;
while ($i < 4)
{
 if ($parrafo==$parr1[$i]) echo $parr2[$i];
 $i++;
}
 ?></td>
</tr>
<tr><td colspan=4>&nbsp;</td></tr>



</table>

<p class="submit">
		<input name="submitted" type="hidden" value="yes" />
		<input type="submit" name="Submit" value="<?php _e("Actualizar opciones", 'citas'); ?>" />
</p>

</form>
<?php 


}


function install_quote_archive(){
   global $wpdb;
   $tabla_citas = $wpdb->prefix . "citas";
   if($wpdb->get_var("show tables like '$tabla_citas'") != $tabla_citas) {

   $sql = "CREATE TABLE " . $tabla_citas . " (
	      ID int(5) unsigned NOT NULL auto_increment,
              autor varchar(60) NOT NULL default '',
              cita longtext NOT NULL default '',
              visualiza varchar(2) NOT NULL default 'si';
	      PRIMARY KEY  (ID, autor) 		); 		";
    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
    dbDelta($sql);
    quote_archive_restaurar();
   }
}


$ver = get_option('ver');
$donde = get_option('donde');
$tam = get_option('tam');
$estilo = get_option('estilo');
$color = get_option('color');
$parrafo = get_option('parrafo');


function quote_archive() {
    global $ver;
    global $donde;
    global $tam;
    global $estilo;
    global $color;
    global $parrafo;
    global $wpdb;
    if ($ver == 'no') {
    }
    else {
       $tabla_citas = $wpdb->prefix . "citas";
       $consulta = "SELECT ID, autor, cita FROM " . $tabla_citas . "  WHERE visualiza = 'si' ORDER BY RAND() LIMIT 70";    
       $fila = $wpdb->get_results($consulta);
       if ($fila) {
              if ($estilo == 'strong') {
                  $peso = 'bold';
                  $tipo = 'normal';
              }
               else {
                  $peso = 'normal';
                  $tipo = $estilo;
               }
               echo '<STYLE type="text/css">';
               echo '#cita {font-size:12px;text-align:' . $parrafo .';font-style:normal;}';
               echo '#autor{font-size:' . $tam . 'px;text-align:' . $parrafo . ';font-style:' . $tipo .';font-weight:'. $peso .';font-color:'. $color .';}';
               echo '</STYLE>';

               $ID = $fila[0]->ID;
               $autor = $fila[0]->autor;
               $cita = $fila[0]->cita;

               $linea_nombre = '<div id=autor>'. $autor . '</div>'; 
               if ($cita != '') {
                    $linea_cita = '<div id=cita>' . $cita . '</div>';
               }
               else {
                   $linea_cita = '';
               }

               //Visualizar
               $cita_retorno =  '<blockquote>' . $linea_cita .  $linea_nombre . '<p> </p></blockquote>';
               return $cita_retorno;
            }
    }
}


//Añadir el texto al post automáticamente
add_action('the_content', 'add_quote_archive');

function add_quote_archive( $content) {
        if ($donde == 'fin') $content = $content.quote_archive();
        else $content = quote_archive().$content;
        return $content;
}



function quote_archive_listar() {
    global $ver;
    global $donde;
    global $tam;
    global $estilo;
    global $color;
    global $parrafo;
    global $wpdb;
    $tabla_citas = $wpdb->prefix . "citas";
    $consulta = "SELECT ID, autor, cita FROM " . $tabla_citas . "  WHERE visualiza = 'si' ORDER BY autor ASC, cita ASC";    
    $listado = $wpdb->get_results($consulta);
    if ($listado) {
       if ($estilo == 'strong') {
           $peso = 'bold';
           $tipo = 'normal';
       }
       else {
            $peso = 'normal';
            $tipo = $estilo;
       }
       echo '<STYLE type="text/css">';
       echo '#cita {font-size:14px;text-align:' . $parrafo .';font-style:normal;}';
       echo '#autor{font-size:' . $tam . 'px;text-align:' . $parrafo . ';font-style:' . $tipo .';font-weight:'. $peso .';font-color:'. $color .';}';
       echo '</STYLE>';
       foreach($listado as $fila) {
            $ID = $fila->ID;
            $autor = $fila->autor;
            $cita = $fila->cita;
            $linea_nombre = '<div id=autor>'. $autor . '</div>'; 
            if ($cita != '') {
                 $linea_cita = '<div id=cita>' . $cita . '</div>';
            }
            else {
                $linea_cita = '';
            }
            //Visualizar
            $todas_las_citas = $linea_cita . $linea_nombre . '<p></p>';
            echo $todas_las_citas;
            }
    };
}

function ver_una_cita() {
    global $ver;
    global $donde;
    global $tam;
    global $estilo;
    global $color;
    global $parrafo;
    global $wpdb;
       $tabla_citas = $wpdb->prefix . "citas";
       $consulta = "SELECT ID, autor, cita FROM " . $tabla_citas . "  WHERE visualiza = 'si' ORDER BY RAND() LIMIT 70";    
       $fila = $wpdb->get_results($consulta);
       if ($fila) {
              if ($estilo == 'strong') {
                  $peso = 'bold';
                  $tipo = 'normal';
              }
               else {
                  $peso = 'normal';
                  $tipo = $estilo;
               }
               echo '<STYLE type="text/css">';
               echo '#cita {font-size:14px;text-align:' . $parrafo .';font-style:normal;}';
               echo '#autor{font-size:' . $tam . 'px;text-align:' . $parrafo . ';font-style:' . $tipo .';font-weight:'. $peso .';font-color:'. $color .';}';
               echo '</STYLE>';

               $ID = $fila[0]->ID;
               $autor = $fila[0]->autor;
               $cita = $fila[0]->cita;

               $linea_nombre = '<div id=autor>'. $autor .'</div>'; 
               if ($cita != '') {
               			$linea_cita = '<div id=cita>' . $cita . '</div>';
               }
               else {
                   $linea_cita = '';
               }
               //Visualizar
               $cita_retorno =  $linea_cita . $linea_nombre . '<p> </p>';
               echo $cita_retorno;
            }
}



register_activation_hook( __FILE__, 'install_quote_archive' );

?>