<div class="wrap stf_options_page"> 
	<h2><?php echo esc_html( $title ); ?></h2>
	<div id="nav"><?php if(!empty($navigation)){echo $navigation;} ?></div>
	
<!-- Notifications -->
<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) { ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php } ?>
<?php if ( isset($_GET['error']) && isset($errors[$_GET['error']]) ) { ?>
<div id="message" class="error fade"><p><?php echo $errors[$_GET['error']]; ?></p></div>
<?php } ?>
<!-- [End] Notifications -->

<div class="stf_opts_wrap">
<div class="stf_options">
<form method="post">
<div id="options-tabs">

<div class="tab_container">
<?php foreach ($options as $field) {
switch ( $field['type'] ) {
	
case 'section': 

	echo '<h3>' . $field[ 'name' ] . '</h3>'; 

break;

	case 'open': ?>

<?php break;
	
	case 'close': ?>

</div>
</div>

<?php break;
	
	case 'text': ?>

<div class="stf_input stf_text clearfix">
	<label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label>
 	<input name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" value="<?php if ( isset($current[ $field['id'] ]) && $current[ $field['id'] ] != "") { echo esc_html(stripslashes($current[ $field['id'] ] ) ); } ?>" />
	<small><?php echo $field['desc']; ?></small>
</div>

<?php
break;
 
case 'textarea':
?>

<div class="stf_input stf_textarea clearfix">
	<label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label>
 	<textarea name="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" cols="" rows=""><?php if ( $current[ $field['id'] ] != "") { echo stripslashes($current[ $field['id'] ] ); } else { echo $field['std']; } ?></textarea>
 <small><?php echo $field['desc']; ?></small>
 
 </div>
  
<?php
break;
 
case 'select':
?>

<div class="stf_input stf_select clearfix">
	<label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label>
	
<select name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>">
<?php foreach ($field['options'] as $key=>$name) { ?>
		<option <?php if ( isset($current[ $field['id'] ]) && $current[ $field['id'] ] == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key;?>"><?php echo $name; ?></option><?php } ?>
</select>

	<small><?php echo $field['desc']; ?></small>
</div>
<?php
break;
 
case "checkbox":
?>

<div class="stf_input stf_checkbox clearfix">
	<label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label>

	<input type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="on" <?php if( array_key_exists( $field['id'], $current ) ){ checked( $current[ $field['id'] ], "on" ); } ?> />

	<small><?php echo $field['desc']; ?></small>
 </div>
<?php break; 
case "section":

?>

<div class="stf_section tab_content" id="<?php echo sanitize_title( $field['name'] ); ?>">
<!-- <div class="stf_title"><h3><?php echo $field['name']; ?></h3><span class="submit">
</span><div class="clear"></div></div> -->
<div class="stf_options">

 
<?php break;
 
}
}
?>

<div id="tabs-footer" class="clearfix">
	<p class="submit">
		<input name="save" type="submit" class="button-primary" value="Save changes" />
		<input type="hidden" name="action" value="save" />
	</p>
	</form>
	
	<form method="post">
		<input name="reset" type="submit" class="button-secondary" value="Reset Options" />
		<input type="hidden" name="action" value="reset" />
	</form>
	
</div>
</div>
</div>
</div>

</div> 