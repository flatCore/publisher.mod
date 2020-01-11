<h1>{post_title}</h1>
<p>{post_author} am {post_releasedate}</p>
<div class="row">
	<div class="col-md-12">
		<div class="well well-sm">
			<iframe id="video-player" type="text/html" width="100%" height="350px"
    src="https://www.youtube.com/embed/{video_id}?rel=0&showinfo=0&color=white&iv_load_policy=3" frameborder="0" allowfullscreen></iframe>
			{post_teaser}
		</div>
	</div>
</div>

<div class="post-footer">
	<p class="text-right">{post_cats}</p>
	<hr>
	<p><a class="btn btn-link" href="{back_link}">{back_to_overview}</a></p>
</div>