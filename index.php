<?php
/**
* Display AWS Instance Meta Data
*
* @author Yong-Wei Chen <yongwei.chen@gmail.com>
* @license http://myaws.tw/aws/LICENSE The MIT License
* @version 1.0
* @link http://myaws.tw/aws/index.php
*/

showHtmlHeader();
showTableHeader();

$url = "http://169.254.169.254/latest/meta-data/";
$base = explode("\n",getData($url));
$mac = '';

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
	$resp = file_get_contents($requestUrl);
	return $resp;
}

function showInfo($meta, $value)
{
	echo "<tr><td>".$meta."</td><td>".$value."</td></tr>";
}

function showHtmlHeader()
{
	echo <<<HEADER
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="keywords" content="AWS,PHP,Meta Data" />
<meta name="Description" content="Display all AWS instance meta data easily by PHP. 使用PHP方便快速的把AWS Instance Meta Data顯示出來" />
<meta name="robots" content="index,follow" />
<meta name="googlebot" content="index,follow" />
<title>AWS Instance Meta Data</title>
<link rel="canonical" href="http://myaws.tw/aws/index.php" />
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<h1>AWS Instance Meta Data</h1>
<h3><a href="show_index.php" target="_blank">Source Code</a> by <a href="http://blog.hsdn.net" target="_blank">Henry (阿維)</a></h3>
<h3>
Reference: <a href="http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/AESDG-chapter-instancedata.html" target="_blank">AWS Instance Metadata and User Data</a> | 
Thanks for: <a href="https://www.facebook.com/groups/286709044738947/" target="_blank">AWS User Group in Taiwan</a>
</h3>
HEADER;
}

function showTableHeader()
{
	echo "<div class='AWSMetaData'><table><tr><td>Meta Data</td><td>Value</td></tr>";
}

function showTableFooter()
{
	echo "</table></div>";
	include_once("analyticstracking.php");
	echo "</body></html>";
}