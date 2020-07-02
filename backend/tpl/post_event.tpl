<form action='{formaction}' class='form-horizontal' id='editpage' method='post' name="editpage">
	<div class="row">
		<div class="col-md-9">
			<div class="card">
				<div class="card-header">
			<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#intro">{tab_intro}</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#content">{tab_content}</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#info">{tab_info}</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#prices">{tab_prices}</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#prefs">{tab_preferences}</a></li>
			</ul>
				</div>
				<div class="card-body">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="intro">
					<div class="row">
						<div class="col-md-6">
							<label>{label_title}</label>
							<input class="form-control" name="post_title" type="text" value="{post_title}">
							<label>{label_description}</label> 
							<textarea class='mceEditor_small' name='post_teaser'>{post_teaser}</textarea>
						</div>
						<div class="col-md-6">
							<div class="well well-sm">
								<label>{label_image}</label> <input class="filter-images form-control" name="filter-images" placeholder="Filter ..." type="text">
								<div class="images-list scroll-container">
									{widget_images}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="content">
					<textarea class='mceEditor' name='post_text'>{post_text}</textarea>
				</div>
				<div class="tab-pane fade" id="info">
					<fieldset>
						<legend>{label_eventdates}</legend>
					
						<div class="row">
							<div class="col">
						<div class="input-group mb-2">
							<div class="input-group-prepend"><span class="input-group-text">Beginn</span></div>
							<input class='dp form-control' name="event_start" type="text" value="{event_start}">
						</div>
							</div>
							<div class="col">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text">Ende</span></div>
							<input class='dp form-control' name="event_end" type="text" value="{event_end}">
						</div>
						</div>
						</div>
					</fieldset>
					<fieldset>
						<legend>{label_event_location}</legend>
					
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_street}</label>
									<input class="form-control" name="post_event_street" type="text" value="{post_event_street}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_street_nbr}</label>
									<input class="form-control" name="post_event_street_nbr" type="text" value="{post_event_street_nbr}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_zip}</label>
									<input class="form-control" name="post_event_zip" type="text" value="{post_event_zip}">
								</div>
							</div>
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_city}</label>
									<input class="form-control" name="post_event_city" type="text" value="{post_event_city}">
								</div>
							</div>
						</div>

					</fieldset>
					
					<fieldset>
						<legend>{label_event_hotline}</legend>
						<input class="form-control" name="post_event_hotline" type="text" value="{post_event_hotline}">
					</fieldset>
					
				</div>
				<div class="tab-pane fade" id="prices">

					
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_price_desc1}</label>
									<input class="form-control" name="post_event_price_cat1_description" type="text" value="{post_event_price_cat1_description}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_price}</label>
									<input class="form-control" name="post_event_price_cat1" type="text" value="{post_event_price_cat1}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_price_desc2}</label>
									<input class="form-control" name="post_event_price_cat2_description" type="text" value="{post_event_price_cat2_description}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_price}</label>
									<input class="form-control" name="post_event_price_cat2" type="text" value="{post_event_price_cat2}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_price_desc3}</label>
									<input class="form-control" name="post_event_price_cat3_description" type="text" value="{post_event_price_cat3_description}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_price}</label>
									<input class="form-control" name="post_event_price_cat3" type="text" value="{post_event_price_cat3}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_price_desc4}</label>
									<input class="form-control" name="post_event_price_cat4_description" type="text" value="{post_event_price_cat4_description}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_price}</label>
									<input class="form-control" name="post_event_price_cat4" type="text" value="{post_event_price_cat4}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_price_desc5}</label>
									<input class="form-control" name="post_event_price_cat5_description" type="text" value="{post_event_price_cat5_description}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_price}</label>
									<input class="form-control" name="post_event_price_cat5" type="text" value="{post_event_price_cat5}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="form-group">
									<label>{label_price_desc6}</label>
									<input class="form-control" name="post_event_price_cat6_description" type="text" value="{post_event_price_cat6_description}">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{label_price}</label>
									<input class="form-control" name="post_event_price_cat6" type="text" value="{post_event_price_cat6}">
								</div>
							</div>
						</div>
						
						<label>{label_price_note}</label> 
						<textarea class='mceEditor_small' name='post_event_price_note'>{post_event_price_note}</textarea>
						
				</div>
				<div class="tab-pane fade" id="prefs">
					<div class="form-group">
						<label>{label_author}</label>
						<input class='form-control' name="post_author" type="text" value="{post_author}">
					</div>
					<div class="form-group">
						<label>{label_source}</label>
						<input class='form-control' name="post_source" type="text" value="{post_source}">
					</div>
					<div class="form-group">
						<label>{label_slug}</label>
						<input class='form-control' name="post_slug" type="text" value="{post_slug}">
					</div>
					<div class="form-group">
						<label>{label_keywords}</label>
						<textarea class='form-control' name="post_tags">{post_tags}</textarea>
					</div>

					<fieldset>
						<legend>RSS</legend>
						<div class="form-group">
							<label>{label_rss}</label>
							{select_rss}
						</div>
						<div class="form-group">
							<label>{label_rss_url}</label>
							<input class='form-control' name="post_rss_url" type="text" value="{post_rss_url}">
						</div>
					</fieldset>

				</div><!-- #prefs -->
			</div>
		</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="well well-sm">
				<fieldset>
					<legend>{label_language}</legend>
					<div class="">
						{checkboxes_lang}
					</div>
				</fieldset>
				<fieldset>
					<legend>{label_categories}</legend>
					<div class="scroll-container" style="max-height: 150px;">
						{widget_categories}
					</div>
				</fieldset>
				<fieldset>
					<legend>{label_releasedate}</legend>
					<input class='dp form-control' name="post_releasedate" type="text" value="{post_releasedate}">
				</fieldset>
				<fieldset>
					<legend>{label_priority}</legend> {select_priority} {checkbox_fixed}
				</fieldset>
				<fieldset>
					<legend>{label_status}</legend> {select_status}
				</fieldset>
				<input name="post_type" type="hidden" value="{post_type}">
				<input name="modus" type="hidden" value="{modus}">
				<input name="post_id" type="hidden" value="{post_id}">
				<input type="hidden" name="csrf_token" value="{token}">
				<input type="hidden" name="post_date" value="{post_date}">
				{submit_button}
			</div>
		</div>
	</div>
</form>