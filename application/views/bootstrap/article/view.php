<?php
if (!empty($article)) {
	if ($multiple) {
        $data = array(
            'total' => $total,
            'perPage' => articles::PER_PAGE,
            'page' => $page,
            'url' => $url
        );
        echo Partial::render('pagination', $data);
		foreach ($article as $post) {
			echo Partial::render('articleQuickView', $post);
		}
	} else {
        $article[0]['mlt'] = $mlt;
		echo Partial::render('articleFull', $article[0]);
		
		if ($article[0]['commentable']) {
			echo Partial::render('comment', array(
                'id' => $article[0]['_id'], 
                'page' => $commentPage, 
                'pageLoc' => $commentPageLoc
            ));
		}
	}
}
?>
