<html>
<body>

<?php 
	
	if (isset($_POST['compression']))
		$commp=  $_POST['compression'];
	else 
		$commp = 50;
	
	$commpression  = $commp / 100;
	
	if (isset($_POST['alpha']))
		$alpha=  $_POST['alpha'];
	else 
		$alpha = 0.6;
		
	if (isset($_POST['betha']))
		$betha=  $_POST['betha'];
	else 
		$betha = 0.4;
			
		
	//read file 
	$dir = "./corpus";
	$files = scandir($dir);
	$isi_file = file_get_contents("./corpus/" . $files[2]);
	//break the content to a few paragraph 
	$paragraph = preg_split('#(\r\n?|\n)+#', $isi_file);
	$par_tot = count($paragraph);
	
	//get the terms and delete the stopwords
	$stopwords = file_get_contents("./stopwords.txt");
	$stopwords = preg_split("/[\s]+/", $stopwords);
	$term =  preg_split("/[\d\W\s]+/", strtolower($isi_file));
	$term = array_diff ($term, $stopwords);
	$term = array_values($term);
	
	$term = array_count_values($term);
	//print_r ($term);
	//exit;
	 $df_term = array();
		foreach ($term as $key => $value) {
			
                $df_term[$key] = $value;
              	
				  //echo "Key: ".$key." Data: ".$value."<br />";
		
		}
		
		$sentence_weight = array();
    	$sentence_position = array();
    	$sentence_tfidf = array();
		$sentence2 = array();
		$i =1;
		
		foreach ($df_term as $key => $value) {
			$df = $value;
			$inverted_index[$key] = array();
			$inverted_index[$key]['idf'] = log10($par_tot / $df); // simpan nilai idf
			
			//print_r ($inverted_index[$key]['idf']);
		}
		//print_r($inverted_index);
		//exit;
		
		//get the tf.idf every sentence
		
		$sentence = preg_split("/[.?!]+/", $isi_file,  -1, PREG_SPLIT_NO_EMPTY);
		
   	 	$sum_sentence = ceil(sizeof($sentence) * $commpression);
		 
	
		foreach ($sentence as $key => $value) {
			$word = preg_split("/[\d\W\s]+/", strtolower($value));
			$word = array_diff($word, $stopwords);
			$word = array_values($word);
			$tf_idf = 0;
			$freq_word =  array_count_values($word);
			//print_r($freq_word);
			//exit;
				//count the tf.idf
				foreach ($freq_word as $terms => $tf) 
					  	$tf_idf += $tf * $inverted_index[$terms]['idf'];
						$weight_by_position = 1 / ($i); // count the value from position						
						$sum_weight = $alpha * $tf_idf + $betha * $weight_by_position;
						array_push($sentence_weight, $sum_weight);
						array_push($sentence_position, $weight_by_position);
						array_push($sentence_tfidf, $tf_idf);		
						array_push($sentence2,$value);				
						$i++;									
				
		}						
			arsort($sentence_weight);		
			$final_sorted = array_slice($sentence_weight, 0, $sum_sentence, true);
			ksort($final_sorted);
			
			
			$summary = "";
    foreach ($final_sorted as $key => $value)
        $summary = $summary . $sentence2[$key] . ". ";
		
	foreach ($final_sorted as $key => $value)
		$final_sentence[$key] = $sentence2[$key];

	print_r($final_sentence);	
						
?>

 <?php if (!empty($_POST["name"])) echo $_POST["compression"]; ?><br>

		<form action="index.php" method="post">
			Compressi (1 - 100 %): <input type="text" name="compression"><br>
			Alpha (0 - 1): <input type="text" name="alpha"><br>
			Betha (0 - 1): <input type="text" name="betha"><br>			
		<input type="submit">
		</form>
</body>
</html>