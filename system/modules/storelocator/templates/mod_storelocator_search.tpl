<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

	<form method="post" action="">
		<input type="text" name="storelocator_search_name" value="<?php echo $this->searchVal; ?>" />
		<input type="submit" value="Suchen" />
	</form>

</div>
<!-- indexer::continue -->