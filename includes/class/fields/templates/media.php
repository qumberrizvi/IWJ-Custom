<script id="tmpl-iwjmb-media-item" type="text/html">
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
  <div class="iwjmb-media-info">
    <h4>
      <a href="{{{ data.url }}}" target="_blank" title="{{{ i18niwjmbMedia.view }}}">
        <# if( data.title ) { #> {{{ data.title }}}
          <# } else { #> {{{ i18niwjmbMedia.noTitle }}}
        <# } #>
      </a>
    </h4>
    <p>{{{ data.mime }}}</p>
    <p>
      <?php if(is_blog_admin()) { ?>
      <a class="iwjmb-edit-media" title="{{{ i18niwjmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
        <span class="dashicons dashicons-edit"></span>{{{ i18niwjmbMedia.edit }}}
      </a>
      <?php } ?>
      <a href="#" class="iwjmb-remove-media" title="{{{ i18niwjmbMedia.remove }}}">
        <span class="dashicons dashicons-no-alt"></span>{{{ i18niwjmbMedia.remove }}}
      </a>
    </p>
  </div>
</script>

<script id="tmpl-iwjmb-media-status" type="text/html">
	<# if ( data.maxFiles > 0 ) { #>
		{{{ data.length }}}/{{{ data.maxFiles }}}
		<# if ( 1 < data.maxFiles ) { #>  {{{ i18niwjmbMedia.multiple }}} <# } else {#> {{{ i18niwjmbMedia.single }}} <# } #>
	<# } #>
</script>
