<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>

	<?php if ($this->headline) { ?>
		<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
	<?php } ?>

	<form method="post" action="<?php echo $this->action; ?>" id="<?php echo $this->formId; ?>">
		<input type="hidden" name="FORM_SUBMIT" value="<?php echo $this->formId; ?>">
		<input type="hidden" name="REQUEST_TOKEN" value="{{request_token}}">
		<input type="hidden" name="search" value="1">
		<input type="hidden" id="storelocator_search_country" name="storelocator_search_country" value="<?php echo $this->country; ?>" />
		
		<div class="search">
			<label for="storelocator_search_<?php echo $this->moduleId; ?>"><?php echo $GLOBALS['TL_LANG']['tl_storelocator']['field']['postal']; ?></label>
			<input type="text" class="text" id="storelocator_search_<?php echo $this->moduleId; ?>" name="storelocator_search_name" value="<?php echo $this->searchVal; ?>" />
		</div>
		
		<?php if( count($this->countries) > 1 ) { ?>
		<div class="country">
			<label><?php echo $GLOBALS['TL_LANG']['tl_storelocator']['field']['country']; ?></label>
			<select name="sl_search_country" onchange="document.getElementById('storelocator_search_country').value = this.options[this.selectedIndex].value;">
			<?php foreach( $this->countries as $k => $v ) { ?>
				<option value="<?php echo $k; ?>" <?php if( $this->country == $k ) { ?>selected="selected"<?php } ?>><?php echo $v; ?></option>
			<?php } ?>
			</select>
		</div>
		<?php } ?>
		
        <div class="submit_container">
            <input class="submit" type="submit" value="<?php echo $GLOBALS['TL_LANG']['tl_storelocator']['field']['search']; ?>" />
        </div>
	</form>

</div>
<!-- indexer::continue -->