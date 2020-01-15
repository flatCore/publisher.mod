<div class="post-type-{post_type}">
	<h1>{post_title}</h1>
	<p>{post_author} am {post_releasedate}</p>
	
	
	<div class="row">
		<div class="col-md-4">
			<div class="teaser-image">
				<img src="{post_img_src}" class="img-thumbnail">
			</div>
		</div>
		<div class="col-md-8">
			{post_teaser}
			<p><a href="{post_external_link}" target="_blank">{post_external_link}</a></p>
		</div>
	</div>
	
	<div class="post-footer">
		<p class="text-right">{post_cats}</p>
		<hr>
		<p><a class="btn btn-link" href="{back_link}">{back_to_overview}</a></p>
	</div>
</div>