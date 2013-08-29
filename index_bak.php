<html>
<head>
<title>AWS Meta Data</title>
<style>
h1, h3{ text-align:center }
.AWSMetaData {
	margin:0px;padding:0px;
	width:100%;
	box-shadow: 10px 10px 5px #888888;
	border:1px solid #000000;

	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;

	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;

	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;

	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}.AWSMetaData table{
	width:100%;
	height:100%;
	margin:0px;padding:0px;
}.AWSMetaData tr:last-child td:last-child {
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
}
.AWSMetaData table tr:first-child td:first-child {
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}
.AWSMetaData table tr:first-child td:last-child {
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
}.AWSMetaData tr:last-child td:first-child{
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
}.AWSMetaData tr:hover td{

}
.AWSMetaData tr:nth-child(odd){ background-color:#aad4ff; }
.AWSMetaData tr:nth-child(even)    { background-color:#ffffff; }.AWSMetaData td{
	vertical-align:middle;
	border:1px solid #000000;
	border-width:0px 1px 1px 0px;
	text-align:left;
	padding:7px;
	font-size:12px;
	font-family:Arial;
	font-weight:normal;
	color:#000000;
}.AWSMetaData tr:last-child td{
	border-width:0px 1px 0px 0px;
}.AWSMetaData tr td:last-child{
	border-width:0px 0px 1px 0px;
}.AWSMetaData tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.AWSMetaData tr:first-child td{
		background:-o-linear-gradient(bottom, #005fbf 5%, #003f7f 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #003f7f) );
	background:-moz-linear-gradient( center top, #005fbf 5%, #003f7f 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#003f7f");	background: -o-linear-gradient(top,#005fbf,003f7f);

	background-color:#005fbf;
	border:0px solid #000000;
	text-align:center;
	border-width:0px 0px 1px 1px;
	font-size:14px;
	font-family:Arial;
	font-weight:bold;
	color:#ffffff;
}
.AWSMetaData tr:first-child:hover td{
	background:-o-linear-gradient(bottom, #005fbf 5%, #003f7f 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #003f7f) );
	background:-moz-linear-gradient( center top, #005fbf 5%, #003f7f 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#003f7f");	background: -o-linear-gradient(top,#005fbf,003f7f);

	background-color:#005fbf;
}
.AWSMetaData tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.AWSMetaData tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}
 </style>
</head>
<body>
<h1>AWS Meta Data</h1>
<h3><a href="show_index.php" target="_blank">Display Source Code</a> by <a href="http://blog.hsdn.net" target="_blank">阿維</a></h3>
<h3>Reference: <a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/AESDG-chapter-instancedata.html" target="_blank">AWS Instance Metadata and User Data</a></h3>
<?php
$url = "http://169.254.169.254/latest/meta-data/";
$base = file_get_contents($url);
$base = explode("\n",$base);
$mac = '';
showTableHeader();
foreach($base as &$value)
{
	$executeUrl = $url.$value;
	$exePath = '';
	if(strpos($value,"/") == 0)
	{
		$metaData = getData($executeUrl);
		showInfo($value, $metaData);
		if($value == 'mac') $mac = $metaData;
	}
	else
	{
		$hasChildData = getData($executeUrl);
		switch($value){
			case "public-keys/":
			case "placement/":
			case "metrics/":
				$replaceData = str_replace("=","/",$hasChildData);
				$exePath = $url.$value.$replaceData;
				echo "path = $exePath<br />";
				$data = getData($exePath);
				showInfo($value.$replaceData, $data);
			breaK;
			case "network/":
				$networkPath = "network/interfaces/macs/".strtolower($mac)."/";
				$networkInfo = array("device-number", "local-hostname", "local-ipv4s", "mac", "owner-id", "public-hostname", "security-groups", "security-groups-ids", "subnet-id", "subnet-ipv4-cidr-block", "vpc-id", "vpc-ipv4-cidr-block");

				foreach($networkInfo as $networkVal)
				{
					$networkData = $networkPath.$networkVal;
					showInfo($networkData, getData($url.$networkData));
				}
			break;
			default:
				$childNode = explode("\n",$hasChildData);
				foreach($childNode as &$child)
				{
					$exeUrl = $url.$value.$child;
					$data = getData($exeUrl);
					showInfo($value.$child, $data);
				}
		}
	}
}
showTableFooter();

function getData($requestUrl)
{
//	echo "url = $requestUrl<br />";
	$resp = file_get_contents($requestUrl);
	return $resp;
}

function showTableHeader()
{
	echo "<div class='AWSMetaData'><table><tr><td>Meta Data</td><td>Value</td></tr>";
}

function showTableFooter()
{
	echo "</table></div></body></html>";
}

function showInfo($meta, $value)
{
	echo "<tr><td>".$meta."</td><td>".$value."</td></tr>";
}
