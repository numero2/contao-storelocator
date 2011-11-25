<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>
<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

	<?php foreach( $this->entries as $entry ) { ?>
	<div class="entry">
		<h3><?php echo $entry['name']; ?></h3>
		<em>Entfernung ca. <?php echo number_format($entry['distance'],2,',','.'); ?>km</em>
		<p>
			<?php echo $entry['street']; ?><br />
			<?php echo $entry['postal']; ?> <?php echo $entry['city']; ?><br />
			<?php echo $entry['country_name']; ?>
		</p>
		<p>
			<?php if( !empty($entry['phone']) ) { ?>Tel.: <?php echo $entry['phone']; ?><br /><?php } ?>
			<?php if( !empty($entry['fax']) ) { ?>Fax: <?php echo $entry['fax']; ?><br /><?php } ?>
			<?php if( !empty($entry['email']) ) { ?>E-Mail: {{email::<?php echo $entry['email']; ?>}}<br /><?php } ?>
			<?php if( !empty($entry['url']) ) { ?>WWW: <?php echo $entry['url']; ?><br /><?php } ?>
		</p>
	</div>
	<?php } ?>

</div>
<!-- indexer::continue -->