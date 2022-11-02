<?
//Регистрация rest api new endpoint
add_action('rest_api_init', function () {
	register_rest_route('api/v1', '/estate_property', array(
		'methods' => 'POST',
		'callback' => 'post_estate',
		'permission_callback' => '__return_true',
	));
});

// post custom estate 
function post_estate($request)
{
	// вставляем запись в базу данных
	$my_post = array(
		'import_id' 				=> $_POST['ID'],
		'post_title'    			=> $_POST['post_title'],
		'post_content'  			=> $_POST['post_content'],
		'post_type'     			=> 'estate_property',
		'post_status'   			=> 'publish',
		'property_price'			=> $_POST['property_price'],
		'post_author' 				=> $_POST['post_author'],
		'property_user' 			=> $_POST['property_user'],
		'property_status' 			=> $_POST['property_status'],
		'local_pgpr_slider_type' 	=> $_POST['local_pgpr_slider_type'],
		'property_address' 			=> $_POST['property_address'],
		'property_country' 			=> $_POST['property_country'],
		'property_size' 			=> $_POST['property_size'],
		'property_bathrooms' 		=> $_POST['property_bathrooms'],
		'property_bedrooms' 		=> $_POST['property_bedrooms'],
		'$stories-number' 			=> $_POST['$stories_number'],
	);

	$post_ID = wp_insert_post($my_post); //Returns the post ID on success. 
	return get_posts($args);
}
//Добавление пункта меню	
add_action('admin_menu', 'true_top_menu_page', 25);

function true_top_menu_page()
{

	add_menu_page(
		'Обновление объектов', // тайтл страницы
		'Объекты Google', // текст ссылки в меню
		'manage_options', // права пользователя, необходимые для доступа к странице
		'estate_property', // ярлык страницы
		'true_estate_page_callback', // функция, которая выводит содержимое страницы
		'dashicons-admin-multisite', // иконка, в данном случае из Dashicons
		27 // позиция в меню
	);
}

function true_estate_page_callback()
{

	echo  "<br>", "<br>", ' 
	<form method="POST">
                 
        <label for="Выберите неделю "> Выберите застройщика: </label>
        <select id="cmbMake" name="Make">
        <option value="build1">Застрощик #1</option>
    	<option value="build2">Застрощик #2</option>
        <option value="build3">Застрощик #3</option>
        <option value="build4">Застрощик #4</option>
        </select>
        <input type="hidden" name="selected_text" id="selected_text" value="" />
        <input type="submit" style="    /* display: inline-flex; */
			appearance: none;
			-webkit-box-align: center;
			justify-content: center;
			white-space: nowrap;
			vertical-align: middle;    
			width: 20%;
			line-height: 1.2;    
			padding-inline-start: 6;
			color: white;
			font-weight: 600;
			padding-inline-end: var(--chakra-space-6);
			background: skyblue;
			color: white;
			border-color: white;" name="menu_update" value="Обновить объекты"/>
                         
    </form>';

	if (isset($_POST['menu_update'])) {
		$makerValue = $_POST['Make']; // make value

		function get_google_sheet_data($query)
		{
			$results = false;
			$sheet = '15Ftg48U4zY9aKLy79BGPAn45xqkw9zC41bWAnG3kxss';
			$key = 'AIzaSyAdBE5IZAgpKPrDwe8DwmRKqS5KLC29u18';
			$connection = wp_remote_get("https://sheets.googleapis.com/v4/spreadsheets/{$sheet}/values/{$query}?key={$key}");
			if (!is_wp_error($connection)) {
				$connection = json_decode(wp_remote_retrieve_body($connection), true);
				if (isset($connection['values'])) {
					$results = $connection['values'];
				}
			}
			return $results;
		}
		$number_week = $makerValue;
		$slim1 = $number_week . '!A2:AI452'; // массив фото
		$all_google_data = get_google_sheet_data($slim1);

		/**
		 * Insert an attachment from a URL address.
		 *
		 * @param  string   $url            The URL address.
		 * @param  int|null $parent_post_id The parent post ID (Optional).
		 * @return int|false                The attachment ID on success. False on failure.                   
		 */
		function wp_insert_attachment_from_url($url, $parent_post_id = null)
		{

			$wp_upload_dir    = wp_upload_dir();
			$file_path        = $wp_upload_dir['basedir'] . '/imageestate/' . $url; //кастом папка на хостинге для загрузки изображений
			$file_name        = basename($file_path);
			$file_type        = wp_check_filetype($file_name, null);
			$attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));


			$post_info = array(
				//'guid'           => $wp_upload_dir['basedir'] . //'/imageestate/' . $file_name,
				'post_mime_type' => $file_type['type'],
				'post_title'     => $attachment_title,
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			// Create the attachment.
			$attach_id = wp_insert_attachment($post_info, $file_path, $parent_post_id);
			// Include image.php.
			require_once ABSPATH . 'wp-admin/includes/image.php';
			// Generate the attachment metadata.
			$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
			// Assign metadata to attachment.
			wp_update_attachment_metadata($attach_id, $attach_data);
			return $attach_id;
		}

		$statuspost = 33;
		$statusid = 34;
		$image_array_offset = 0;
		$allpostcount = 450;
		$image_array = 15;
		$title = 15;
		$content = 16;
		$postcount = 0;
		$adress = 17;
		$property_country = 18;
		$property_size = 19;
		$property_price = 20;
		$property_bathrooms = 21;
		$property_bedrooms = 22;
		$stories_number = 23;
		$x = 0;

		//основной цикл перебора по всем записям на листе google sheet


		for ($x = 0; $x <= $allpostcount; $x++) {
			//echo($all_google_data[$statusid][0].'<br>'); 
			afterdelete:
			$post_id = get_the_ID();
			echo $post_id;
			//var_dump($all_google_data[$x][$statuspost]);
			//var_dump($all_google_data[$x][$statusid]);

			$status = get_post_status($all_google_data[$x][$statusid]);
			echo 'статус поста ' . $status;
			if (!empty($all_google_data[$x][$statusid]  && $all_google_data[$x][$statuspost] == 'publish' && $status != publish)) {
				$realty_id = $all_google_data[$x][$statusid];
				$postcount++;

				$url = 'https://skalarealty.com/wp-json/api/v1/estate_property';
				$username = 'admin';
				$password = 'JHg23jhg1234';
				$headers = [
					'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
				];

				$response = wp_remote_post($url, [
					'headers' => $headers,
					'sslverify' => false,
					'body' => [
						'ID'            =>  $realty_id,
						'post_title'    =>  $all_google_data[$x][$title],
						'post_content'  => $all_google_data[$x][$content],
						'property_price' => 	$all_google_data[$x][$property_price],
						'post_author' 	=> 	1,
						//'property_country'	=> 'Albania',
						'property_user'	=> 1,
						'property_status'  => 'new offer',
						'local_pgpr_slider_type' => 'global',
						'property_address' => $all_google_data[$x][$adress],
						'property_country' => $all_google_data[$x][$property_country],
						//'current_adv_filter_city' => $all_google_data[$x][$current_adv_filter_city],	
						'property_size' => $all_google_data[$x][$property_size],
						'property_bathrooms' => $all_google_data[$x][$property_bathrooms],
						'property_bedrooms' => $all_google_data[$x][$property_bedrooms],
						'stories-number' => $all_google_data[$x][$stories_number],
					]
				]);

				//добавление attachment img к посту
				//echo('array images <PRE>');var_dump($URL);
				//echo('</PRE>');
				$i = 0;
				$countimage = 0;
				$parent_post_id = $realty_id;
				for ($i = 0; $i <= 15; $i++) {
					$url = array_slice($all_google_data[$x], $image_array_offset, $image_array);
					//var_dump($url);
					if (!empty($url[$i])) {
						$url = $url[$i];
						//var_dump($url);

						$attach_id = wp_insert_attachment_from_url($url, $parent_post_id);
						// echo '<br>Attachment '.$i.' added<br>';
						if ($i == 0) {
							set_post_thumbnail($parent_post_id, $attach_id);
						}
						$countimage++;
					} else {
						//echo'Все картинки добавлены<br>';
						goto afterimage;
					}
				}

				afterimage:
				echo 'Запись ' . $realty_id . ' добавлена <br> Загружено Изображений:' . $countimage . ' <br><br>';
				//end attachment img к посту


				// проверка ошибки
				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
					echo "Что-то пошло не так: $error_message";
				} else {
					//echo $image_array_offset.' - image_array_offset';
					//echo 'запись есть <pre>';
					//var_dump(array_slice($all_google_data, $image_array_offset, $image_array));
				};
			}	//end if of loop					

			elseif (!empty($all_google_data[$x][$statusid]  && $all_google_data[$x][$statuspost] == 'delete')) {
				//var_dump($all_google_data[$x][$statusid]);
				echo 'Этот пост необходимо удалить ' . $all_google_data[$x][$statusid] . '<br><br>';
				$postid = $all_google_data[$x][$statusid];
				$trashpost = wp_delete_post($postid, $force_delete);
				$x++;
				//echo $trashpost;
				goto afterdelete;
			} else {
				$x = 451;
				echo ('---- все записи обновлены ----');
				break;
			}
		} //end for loop
	}
}
