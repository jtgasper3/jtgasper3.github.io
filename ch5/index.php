<?
header('Content-type: text/xml');
/*
	Runs from a directory containing files to provide an
	RSS 2.0 feed that contains the list and modification times for all the
	files.

d=1
while wget "https://media.pearsoncmg.com/ph/chet/chet_mymedtermlab/turley4e/lecture/turley_ch05_lecture/data/sound$d.mp3"; do
    ((d++))
done

docker run -it -v $(pwd):/tmper php bash
cd /tmper
php index.php
*/
$feedName = "Chapter 5";
$feedDesc = "Who is the best guy in the world";
$feedURL = "https://jtgasper3.github.io/ch5";
$feedBaseURL = "https://jtgasper3.github.io/ch5/"; // must end in trailing forward slash (/).

$allowed_ext = ".mp4,.MP4,.mp3,.MP3";

?><<?= '?'; ?>xml version="1.0"<?= '?'; ?>>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?=$feedName?></title>
		<link><?=$feedURL?></link>
		<description><?=$feedDesc?></description>
		<atom:link href="http://gogglesoptional.com/bloopers" rel="self" type="application/rss+xml" />
		<image>
			<title>Chapter 5</title>
			<link>https://jtgasper3.github.io/ch5/rss.xml</link>
		</image>
		<ttl>1440</ttl>
<?
$files = scandir("./");
sort($files);
foreach ($files as $file) {
	$path_info = pathinfo($file);
	$ext = strtoupper($path_info['extension']);

	if($file !== '.' && $file !== '..' && !is_dir($file) && strpos($allowed_ext, $ext)>0)  
	{  
		$files[]['name'] = $file;  
		$files[]['timestamp'] = filectime($file);
	}  
}  
closedir($dir);
// natcasesort($files); - we will use dates and times to sort the list.

for($i=0; $i<count($files); $i++) {
	if($files[$i] != "index.php") {
          if (!empty($files[$i]['name'])) {
		echo "	<item>\n";
		echo "		<title>". $files[$i]['name'] ."</title>\n";
		echo "		<link>". $feedBaseURL . $files[$i]['name'] . "</link>\n";
		echo "		<guid>". $feedBaseURL . $files[$i]['name'] . "</guid>\n";
		echo '    <enclosure url="'. $feedBaseURL . $files[$i]['name'] . '" length="0" type="audio/mpeg"/>';
//		echo "		<pubDate>". date("D M j G:i:s T Y", $files[$i]['timestamp']) ."</pubDate>\n";
//		echo "		<pubDate>" . $files[$i]['timestamp'] ."</pubDate>\n";

		echo "    </item>\n";
	  }
	}
}
?>
	</channel>
</rss>
