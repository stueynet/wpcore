<div class="wrap">
	<?php screen_icon(); ?>
	<h2>WPCore Plugin Collections</h2>
	<div class="grid">
		<div class="unit three-quarters">
			<form method="post" action="options.php">
				<?php settings_fields( 'default' ); ?>
				<table class="wp-list-table widefat fixed pages" id="wpcore_keys">
					<thead valign="top">
					<tr>
						<th class="manage-column column-title" scope="row" width="30%">Collection name and key</th>
						<th class="manage-column column-title" scope="row" width="70%"><label for="wpcore_key">Plugins in the collection</label></th>
						<th width="10%"></th>
					</tr>
					</thead>
					<tbody>
					<?php
					//get the keys serialized keys
					//$wpCoreKeys = unserialize(get_option('wpcore_key'));

					// grab the settings which has multiple keys
					$keys = get_option('wpcore_keys');
					if($keys):

						// iterate over each key

						foreach($keys as $key){
							// grab the json and break it into the collection info and the plugin array
							$response =  wp_remote_get('http://wpcore.com/collections/'.$key.'/json', array('timeout' => 1));
							$json =  wp_remote_retrieve_body($response);
							$collection = json_decode($json);
							if( ! $collection->success ) {
								$error = '<p>Bad key</p>';
							} else {
								$error = false;
								$name = $collection->data->name;
								$plugins = $collection->data->plugins;
							}

							?>
							<tr>
								<td>
									<h3><?php echo isset( $name ) ? $name : 'Bad Key'; ?></h3>
									<p><input type="text" id="wpcore_keys" name="wpcore_keys[]" value="<?php echo $key; ?>" required="required"></p>
									<?php if( isset($name) ): ?>
										<a href="http://wpcore.com/collections/<?php echo $key; ?>" target="_blank">View on WPCore.com</a>
									<?php endif; ?>
								</td>
								<td>
									<ul>
										<?php
										if( $error ){
											echo $error;
										} else {
											$count = count( $plugins );
											$i = 1;
											foreach( $plugins as $plugin ){
												?>
												<a href=""><?php echo $plugin->name; ?></a>
												<?php
												if( $i != $count )
													echo ', ';
												$i++;
											}
										}
										?>
									</ul>
								</td>
								<td align="right">
									<input type="button" class="wpcore_ibtnDel button button-small"  value="Delete">
								</td>
							</tr>
						<?php }

					else:

						echo '<p>Click the add key button below to add a collection key</p>';

					endif;
					?>
					</tbody>
				</table>
				</p>
				<?php if( $keys ):?>
					<input type="button" id="wpcore_addrow" class="button button-large" value="Add another collection key" />
					<a href="admin.php?page=wpcore-install-plugins" class="button button-large float-right">Install Plugins</a>
				<?php else: ?>
					<input type="button" id="wpcore_addrow" class="button button-large" value="Add a collection key" />
				<?php endif; ?>
				</p>
				<hr>
				<?php submit_button(); ?>
			</form>
		</div>
		<div class="unit one-quarter metabox-holder">
			<div class="postbox">
				<h3>A little more guidance</h3>
				<div class="inside">
					<div class="main">
						<p>On this page you can store your collection keys. You can create and manage your collections at <a href="http://wpcore.com" target="_blank">WPCore.com</a>. For example <a href="http://wpcore.com/collections/6ib8eOIBndO5u0DTrmOt" target="_blank">this</a> is a collection.</p>
						<?php echo '<img src="' . plugins_url( '../assets/img/key.png' , __FILE__ ) . '" > '; ?>
						<p>Collection keys appear at the top of each <a href="http://wpcore.com/collections/6ib8eOIBndO5u0DTrmOt" target="_blank">collection</a>. When you add a key to this table, it will bring the plugins from that collection into the plugin staging area where you can install and activate them.</p>
						<p>You of course should make your own collection and add it to your WordPress sites.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>