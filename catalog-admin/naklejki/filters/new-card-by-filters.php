<?php

// echo("12313123 new-card-by-filters");
include( $_SERVER["DOCUMENT_ROOT"] . '/tools/catalog-admin/naklejki/filters/semantic-templates.php' );


	if( $_GET[ 'template' ] == 'h1' ) {
		// echo('h1: ');
    // var_dump($fileName);
		$generatedH1 = $getH1Template( "Наклейки", explode( '__', $_GET[ 'f' ] ) );
		// var_dump($generatedH1);
		echo( $generatedH1 );
	};
	
	if( $_GET[ 'template' ] == 'description' ) {
		$generatedDescription = $getDescriptionTemplate( "Наклейки", explode( '__', $_GET[ 'f' ] )  );
		echo 'DE NEW!!!';
		echo $generatedDescription;
	}
	
	if( $_GET[ 'template' ] == 'meta_title' ) {
		$generatedMetaTitle = $getCardTitleTemplate( "Наклейки", explode( '__', $_GET[ 'f' ] )  );
		echo $generatedMetaTitle;
	}
	
	if( $_GET[ 'template' ] == 'meta_description' ) {
		$generatedMetaDescription = $getCardMetaDescriptionTemplate( "Наклейки", explode( '__', $_GET[ 'f' ] )  );
		echo $generatedMetaDescription;
	}
	
	if( $_GET[ 'template' ] == 'p_subheader' ) {
		$generatedSubheader = $getCardSubheaderTemplate( "Наклейки", explode( '__', $_GET[ 'f' ] )  );
		echo $generatedSubheader;
	}
	
	if( $_GET[ 'template' ] == 'html' ) {
		$generatedHtml = $getCardHtmlTemplate( "Наклейки", explode( '__', $_GET[ 'f' ] )  );
		echo $generatedHtml;
	}

?>