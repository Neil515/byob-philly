   <?php
   /**
    * Plugin Name: BYOB Takeover CLI
    */

   if ( defined( 'WP_CLI' ) && WP_CLI ) {

   	class BYOB_Takeover_CLI {

   		public function batch( $args, $assoc_args ) {
   			$input  = sanitize_text_field( $assoc_args['input'] ?? '' );
   			$output = sanitize_file_name( $assoc_args['output'] ?? 'takeover_tokens.csv' );

   			if ( ! $input || ! file_exists( $input ) ) {
   				WP_CLI::error( "找不到輸入檔案：{$input}" );
   			}

   			$rows = array_map( 'str_getcsv', file( $input ) );
   			if ( empty( $rows ) ) {
   				WP_CLI::warning( '輸入檔案沒有資料。' );
   				return;
   			}

   			$result_rows = [];
   			$result_rows[] = [ 'Post ID', 'Restaurant', 'Address', 'Token', 'Takeover Link', 'Expires', 'Status' ];

   			foreach ( $rows as $row ) {
   				$post_id = intval( $row[0] ?? 0 );
   				$title   = sanitize_text_field( $row[1] ?? '' );
   				$address = sanitize_text_field( $row[2] ?? '' );

   				if ( ! $post_id || get_post_status( $post_id ) !== 'publish' ) {
   					$result_rows[] = [ $post_id, $title, $address, '', '', '', '❌ invalid post' ];
   					continue;
   				}

   				$token = byob_generate_restaurant_takeover_token( $post_id );
   				if ( ! $token ) {
   					$result_rows[] = [ $post_id, get_the_title( $post_id ), $address, '', '', '', '⚠️ generate failed' ];
   					WP_CLI::warning( "Post {$post_id} 產生失敗" );
   					continue;
   				}

   				$token_data = get_post_meta( $post_id, '_restaurant_takeover_token', true );
   				$expires    = $token_data['expires_at'] ?? '';
   				$link       = home_url( '/takeover-restaurant?token=' . urlencode( $token ) );

   				$result_rows[] = [
   					$post_id,
   					get_the_title( $post_id ),
   					$address,
   					$token,
   					$link,
   					$expires,
   					'✅ generated',
   				];

   				WP_CLI::log( "✅ {$post_id} - {$token}" );
   			}

   			$upload_dir = wp_upload_dir();
   			$target     = trailingslashit( $upload_dir['basedir'] ) . $output;
   			$handle     = fopen( $target, 'w' );
   			foreach ( $result_rows as $row ) {
   				fputcsv( $handle, $row );
   			}
   			fclose( $handle );

   			WP_CLI::success( "完成，CSV 已輸出：{$target}" );

   			$summary = implode( "\n", array_map(
   				fn( $r ) => "{$r[1]} -> {$r[4]}",
   				array_slice( $result_rows, 1 )
   			) );

   			wp_mail(
   				'byobmap.tw@gmail.com',
   				'Batch Takeover Tokens Generated',
   				"檔案：{$target}\n\n{$summary}"
   			);
   		}
   	}

	WP_CLI::add_command( 'byob-takeovers', 'BYOB_Takeover_CLI' );
   }