<?php

require('vendor/autoload.php');

use Aws\CloudFront\CloudFrontClient;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// S3 configured
$S3Options =
	[
		'version' => 'latest',
		'region'  => 'your region configured',
		'credentials' =>
		[
			'key' => 'key S3 from IAM ',
			'secret' => 'key S3 from IAM'
		]
	];


$s3 = new S3Client($S3Options);


// CLOUDFRONT configured
$cloudfront = CloudFrontClient::factory([
	'version' => 'latest',
	'region'  => 'your region configured',
	'credentials' =>
	[
		'private_key' => 'key CloudFront from IAM file .pem',
		'key_pair_id' => 'key CloudFront from IAM',
		'key' => 'key S3 from IAM ',
		'secret' => 'key S3 from IAM',

	]

]);

// list file from AMAZON S3
$archivos = $s3->listObjects(
	[
		'Bucket' => 'bucket name'
	]
);

$archivos = $archivos->toArray();
$fila = "";

foreach ($archivos['Contents'] as $archivo) {
	$fila .= "<tr><td>{$archivo['Key']}</td>";
	$fila .= "<td>bucket name</td>";
	$fila .= "<td>{$archivo['Size']}</td>";
	$fila .= "<td>{$archivo['LastModified']}</td>";
	$fila .= "<td><button onclick='getFile(&#34;{$archivo['Key']}&#34;)'>Download</button></td></tr>";
}

$object = 'your file'; // example upload/file.mp4
$expire = new DateTime('+10 minutes'); // time to view file

$url = $cloudfront->getSignedUrl([// cloudFront
	'private_key' => 'key CloudFront from IAM file .pem',
	'key_pair_id' => 'key CloudFront from IAM',
	'url' => "https://linkfrom.cloudfront.net/{$object}",
	'expires' => $expire->getTimestamp()

]);
// view video from CloudFront
echo '
<video width="320" height="240" controls>
  <source src="' . $url . '" type="video/mp4">
Your browser does not support the video tag.
</video>
';

$object = 'your file'; // example upload/file.pdf
$expire = new DateTime('+10 minutes'); // time to view file

$url2 = $cloudfront->getSignedUrl([
	'private_key' => 'key CloudFront from IAM file .pem',
	'key_pair_id' => 'key CloudFront from IAM',
	'url' => "https://linkfrom.cloudfront.net/{$object2}",
	'expires' => $expire2->getTimestamp()

]);
//view file from CloudFront
echo '
<p>Open a PDF file <a href="' . $url2 . '">example</a>.</p>
<embed src="' . $url2 . '" width="500" height="375" 
 type="application/pdf">
';


// function to search file in S3
$folder = 'your file search'; // example  upload/file.pdf
function doesFolderExists($bucket, $folder)
{
	$s3 = new Aws\S3\S3Client([
		'version' => 'latest',
		'region'  => 'your region configured',
		'credentials' =>
		[
			'key' => 'key S3 from IAM ',
			'secret' => 'key S3 from IAM'
		]
	]);
	$result = $s3->listObjects([
		'Bucket' => $bucket, // REQUIRED
		'Prefix' => $folder,
	]);
	if (isset($result['Contents'])) {
		return true;
	} else {
		return false;
	}
}
echo doesFolderExists('bucket name', $folder);

// clone file to different folder
if (doesFolderExists('bucket name', $folder)) {
	$folder1 = "new folder"; // example upload/newfolder
	$bucket_name = 'dinamouda';
	$s3->copyObject([
		'Bucket' => $bucket_name,
		'CopySource' => "$bucket_name/$folder",
		'Key' => "$folder1/$folder-copy",
	]);
	echo "Copied $folder to $folder1/$folder-copy.\n";
}


//upload file
if (isset($_FILES['file'])) {
	// For this, I would generate a unqiue random string for the key name. But you can do whatever.
	$keyName = 'folder' . basename($_FILES["file"]['name']);  // example upload/file/
	$pathInS3 = 'https://s3.your region configured.amazonaws.com/' . $bucketName . '/' . $keyName;
	$file = $_FILES["file"]['tmp_name'];
	$s3->putObject(
		array(
			'Bucket' => 'bucket name',
			'Key' =>  $keyName,
			'SourceFile' => $file,
			'StorageClass' => 'REDUCED_REDUNDANCY'
		)
	);
}




// donwload file
if ($_POST['key']) {
	$getFile = $s3->getObject([
		'Key' => 'your folder' . $_POST['key'], // example  upload/file.pdf
		'Bucket' => 'bucket name'
	]);
	$getFile = $getFile->toArray();
	file_put_contents($_POST['key'], $getFile['Body']);
}
