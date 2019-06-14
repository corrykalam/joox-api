<?php
header('Content-Type: application/json');
    $maqlo = $_GET['song'];
    $string = str_replace(' ', '%20', $maqlo);

    //curl id song
    $cr=curl_init('http://api.jooxtt.sanook.com/web-fcgi-bin/web_search?country=id&lang=id&search_input='.$string.'&sin=0&ein=30');
	curl_setopt($cr,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($cr,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($cr,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0');
	$xx=curl_exec($cr);$mama=curl_getinfo($cr);curl_close($cr);
	if($mama['http_code']<>200) die(json_encode(array('result' => 0, 'content' => 'ada parameter yg kurang')));
	$xx=json_decode($xx);
	$sid = $xx->itemlist[0]->songid;
	$idsong = base64_encode($sid);
	
	//curl url song
	$ch = curl_init('http://api.joox.com/web-fcgi-bin/web_get_songinfo?songid='.base64_decode($idsong).'&lang=id&country=id&from_type=null&channel_id=null&_='.time());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIE, 'wmid=14997771; user_type=2; country=id; session_key=96870dd03ab9280c905566cad439c904;');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36');
	$json = curl_exec($ch);

	$json = str_replace('MusicInfoCallback(', '', $json);
	$json = str_replace(')', '', $json);
	$json = json_decode($json);
	$fi = $json->m4aUrl;
	$sing = $json->msinger;
	$song = $json->msong;
	$image = $json->imgSrc;

    //curl lyric
	$pro = curl_init('http://api.joox.com/web-fcgi-bin/web_lyric?musicid='.base64_decode(trim($idsong)).'&lang=id&country=id&_='.time());
	curl_setopt($pro, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($pro, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36');
	$ly = curl_exec($pro);
	curl_close($pro);
	$ly = str_replace('MusicJsonCallback(', '', $ly);
	$ly = str_replace(')', '', $ly);
	$ly = json_decode($ly);
	
	$ly = base64_decode($ly->lyric);
	$name = $json->msong;


	$data = json_encode([
		'singer' => $sing,       //penyanyi
		'title' => $song,        //judul lagu
		'url' => $fi,            //url
		'lyric' => $ly,          //lyricnya
		'image' => $image        //image
	]);
echo $data;
?>
