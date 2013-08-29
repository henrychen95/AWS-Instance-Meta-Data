<?php
showHeader();

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
		switch($value)
		{
			case "public-keys/":
			case "placement/":
			case "metrics/":
				$replaceData = $hasChildData; //str_replace("=","/",$hasChildData);
				$exePath = $url.$value.$replaceData;
				showInfo($value.$replaceData, getData($exePath));
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
					showInfo($value.$child, getData($exeUrl));
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

function showHeader()
{
	echo <<<HEADER
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>AWS Instance Meta Data</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<h1>AWS Instance Meta Data</h1>
<h3>
<a href="show_index.php" target="_blank">Source Code</a> by <a href="http://blog.hsdn.net" target="_blank">Henry (阿維)</a> | 
Reference: <a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/AESDG-chapter-instancedata.html" target="_blank">AWS Instance Metadata and User Data</a> | 
<a href="https://www.facebook.com/groups/286709044738947/" target="_blank">amazon web service User Group in Taiwan</a>
</h3>
HEADER;
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
