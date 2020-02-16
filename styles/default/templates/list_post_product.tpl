<div class="post-type-{post_type}">
	<div class="row">
		<div class="col-md-4">
			<div class="teaser-image">
				<img src="{post_img_src}" class="img-fluid">
			</div>
		</div>
		<div class="col-md-8">
			<div class="price-tag">
				{post_currency} {post_price_gross} <span class="product-unit">{post_product_unit}</span>
			</div>
			<span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span>
			<a class="post-headline-link" href="{post_href}"><h3>{post_title}</h3></a>
	
			{post_teaser}
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 offset-md-4">
			<p style="float:right;">{post_cats}</p>
			<p><a class="btn btn-primary {read_more_class}" href="{post_href}">{read_more_text}</a></p>
		</div>
	</div>
</div>