<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

/*-------------------------------------------------------------------------------*/
/* Add Icon Picker in Editor menu
/*-------------------------------------------------------------------------------*/
function icon_icon_picker_button()
{

    $img = ICON_URL.'/inc/admin/assets/img/icon-picker.png';
    printf( '<a class="thickbox button" id="wpicons_gut_shorcode-button" title="Icon Picker" style="outline: medium none !important; cursor: pointer;" >
	<img src="'.$img.'" alt="Icon Picker" width="20" height="20" class="icon_ico"/>%s'.'</a>', __( 'Add Icon', 'icon' ) );

}

/*-------------------------------------------------------------------------------*/
/* Icon Picker HTML Markup
/*-------------------------------------------------------------------------------*/
function icon_picker_content()
{
    ?>
    <div id="iconmarkup" style="display:none;">
    	<div id="tinyicon">
    		<div class="icon_input"><!-- Icon Library -->
    			<label class="label_option_icon has_loader" for="icon_select_icon_source">Icon library</label>
				<span class="icon_loader"></span><select data-sc-param="lib" data-icnnonce="<?php echo wp_create_nonce( 'icon_get_icons' ); ?>" class="icon_select iconval" name="icon_select_icon_source" id="icon_select_icon_source">
    				<option data-lib="none" value="none">- Select library -</option>
    				<option data-lib="fa" value="fontawesome">Font Awesome</option>
    				<option data-lib="ft" value="fontello">Fontello</option>
    				<option data-lib="im" value="icomoon">IcoMoon</option>
                    <option data-lib="oi" value="openiconic">Open Iconic</option>
                    <option data-lib="di" value="dashicons">WP Dashicons</option>
                    <option data-lib="gmi" value="gmaterialicons">Google Material Icons</option>
                    <option data-lib="jv" value="justvector">JustVector Social Font</option>
                    <option data-lib="pf" value="paymentfont">Payment Font</option>

    			</select>
            </div>
            <div class="icon_input"><!-- Icon Picker -->
            <label class="label_option_icon" for="icon_list">Icon</label><input data-sc-param="type" class="iconval" type="text" id="icon_list" name="icon_list" />
            </div>
    		<div class="icon_input"><!-- Icon Color -->
    			<label class="label_option_icon label_option_cpick" for="icon_set_color">Icon color</label><input data-sc-param="color" class="iconval" id="icon_set_color" type="text" value="" data-default-color="#effeff" />
            </div>
    		<div class="icon_input"><!-- Icon Size -->
    			<label class="label_option_icon" for="icon_icon_size">Icon Size</label>
				<select data-sc-param="size" class="icon_select iconval" name="icon_icon_size" id="icon_icon_size">
                  <option value="xs">Mini</option>
                  <option value="sm">Small</option>
                  <option value="md" selected="selected">Medium</option>
                  <option value="nm">Normal</option>
                  <option value="lg" disabled>Large [ Pro Version ]</option>
                  <option value="xl" disabled>Extra Large [ Pro Version ]</option>
                  <option value="xxl" disabled>Double Extra Large [ Pro Version ]</option>
    			</select>
            </div>
    		<div class="icon_input"><!-- Icon Align -->
    			<label class="label_option_icon" for="icon_icon_align">Icon Align</label>
				<select class="icon_select" name="icon_icon_align" id="icon_icon_align">
                  <option value="alignnone" selected="selected">None</option>
                  <option value="alignleft">Left</option>
                  <option value="alignright">Right</option>
                  <option value="aligncenter">Center</option>
    			</select>
                <span data-balloon="PRO VERSION ONLY!" data-balloon-pos="left" class="icon_locked"></span>
            </div>
    		<div class="icon_input"><!-- Background shape -->
    			<label class="label_option_icon" for="icon_select_icon_bg_shape">Background Shape</label>
				<select class="icon_select" name="icon_select_icon_bg_shape" id="icon_select_icon_bg_shape">
    				<option value="none">None</option>
    				<option value="rounded">Circle</option>
    				<option value="boxed">Square</option>
    				<option value="rounded-less">Rounded</option>
                    <option value="rounded-outline">Outline Circle</option>
                    <option value="boxed-outline">Outline Square</option>
                    <option value="rounded-less-outline">Outline Rounded</option>
    			</select>
                <span data-balloon="PRO VERSION ONLY!" data-balloon-pos="left" class="icon_locked"></span>
            </div>
    		<div class="icon_input icon_bg_shape_cont"><!-- Background Color -->
    			<label class="label_option_icon label_option_cpick" for="icon_bg_shape_col">Background Color</label><input id="icon_bg_shape_col" type="text" value="" class="" />
            </div>
			<div class="icon_input icon_custom_text"><!-- Custom Text -->
    			<label class="label_option_icon" for="icon_custom_text">Custom Text</label><textarea id="icon_custom_text" rows="4" cols="50" class="icon_cstm_text icon_pro_ver" spellcheck="false" disabled="disabled">Custom text will allow you to use text near of the icon (side by side)</textarea>
				<span data-balloon="PRO VERSION ONLY!" data-balloon-pos="left" class="icon_locked"></span>
            </div>
    		<div class="icon_input input_no_border"><!-- Add Shortcode -->
    			<input type="button" value="Insert Icon" name="icon_insert_icon" id="icon_insert_icon" class="btn" /><input type="hidden" value="" id="is_edit"/><input type="hidden" value="" id="icon_sc_id" data-sc-param="id" class="iconval sc_id"/><a target="_blank" class="buypro" href="https://ghozylab.com/plugins/ordernow.php?order=iconpro">BUY NOW $5</a>
    		</div><div style="clear:both;"></div>
    </div>
    <div style="clear:both;"></div>
</div>
<?php
}

add_action( 'admin_footer', 'icon_picker_content' );