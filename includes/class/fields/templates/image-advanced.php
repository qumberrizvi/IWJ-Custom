<script id="tmpl-iwjmb-image-item" type="text/html">
	<input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="iwjmb-media-input">
	<div class="iwjmb-media-preview">
		<div class="iwjmb-media-content">
			<div class="centered">
				<# if ( 'image' === data.type && data.sizes ) { #>
					<# if ( data.sizes.thumbnail ) { #>
						<img src="{{{ data.sizes.thumbnail.url }}}">
					<# } else { #>
						<img src="{{{ data.sizes.full.url }}}">
					<# } #>
				<# } else { #>
					<# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
						<img src="{{ data.image.src }}" />
					<# } else { #>
						<img src="{{ data.icon }}" />
					<# } #>
				<# } #>
			</div>
		</div>
	</div>
	<div class="iwjmb-overlay"></div>
	<div class="iwjmb-media-bar">
        <?php if(is_blog_admin()) { ?>
            <a class="iwjmb-edit-media" title="{{{ i18niwjmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
                <span class="dashicons dashicons-edit"></span>
            </a>
        <?php } ?>
		<a href="#" class="iwjmb-remove-media" title="{{{ i18niwjmbMedia.remove }}}">
			<span class="dashicons dashicons-no-alt"></span>
		</a>
	</div>
</script>

