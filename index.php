<html lang="es">

<head>
	<title>Amazon S3</title>
	<meta charset="utf-8" content="Content-type: application/pdf">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="css/estilos.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.css">
	<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>

<body>
	<header>
		<div class="alert alert-info">
			<h3>Amazon S3</h3>
		</div>
	</header>

	<div class="container">

		<table class="table">
			<thead class="bg-primary text-white">
				<tr>
					<th scope="col">Key</th>
					<th scope="col">Bucket</th>
					<th scope="col">Size</th>
					<th scope="col">Date</th>
					<th scope="col">Download</th>
				</tr>
			</thead>

			<tbody id="">
			</tbody>
		</table>


		<form class="form-inline" method="post" id="uploadFile" enctype="multipart/form-data">
			<div class="form-group">
				<input type="file" class="form-control" name="file" id="file">
			</div>
			<button type="submit" class="btn btn-primary">Upload</button>
		</form>
	</div>
	<!-- DIV TO SHOW FILE MP4 OR PDF -->
	<div id="contenido"></div> 

</body>

</html>

<script type="text/javascript">
	//function to get all files from AMAZON S3
	window.onload = function(e) {
		$.ajax({

			url: "s3.php",
			success: function(data) {
				console.log(data)
				$('#contenido').append(data);
			}
		});
	};


	//Upload file to AMAZON S3
	$('#uploadFile').submit(function(e) {
		e.preventDefault();

		var Form = new FormData($('#uploadFile')[0]);

		$.ajax({

			url: "s3.php",
			type: "post",
			data: Form,
			processData: false,
			contentType: false,
			success: function(data) {
				alert(data);
			}
		});
	});

	//Download file from AMAZON S3
	function getFile(key) {
		$.ajax({
			url: "s3.php", 
			data: {
				key: key
			},
			type: "post",
			success: function(data) {
				alert('Descarga Correcta!');
			}
		})
	}
</script>