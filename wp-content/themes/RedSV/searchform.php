<form action="<?php echo home_url(); ?>/" id="searchform" class="searchform" method="get">
	<input type="text" id="s" name="s" placeholder="Buscar" value="<?php _e('Buscar', 'redthemesv') ?>" onfocus="if(this.value=='<?php _e('Buscar', 'redthemesv') ?>')this.value='';" onblur="if(this.value=='')this.value='<?php _e('Buscar', 'redthemesv') ?>';" autocomplete="off" />
	<button class="search-button"><i class="icon-entypo-search"></i></button>
</form>