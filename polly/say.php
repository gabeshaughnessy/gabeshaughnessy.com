<?php

require 'vendor/autoload.php';

$sharedConfig = [
    'region'  => 'us-west-2',
    'version' => 'latest'
];

$sdk = new Aws\Sdk($sharedConfig);

//Voice
$voices = array('Geraint','Gwyneth','Mads','Naja','Hans','Marlene','Nicole','Russell','Amy','Brian','Emma','Raveena','Ivy','Joanna','Joey','Justin','Kendra','Kimberly','Salli','Conchita','Enrique','Miguel','Penelope','Chantal','Celine','Mathieu','Dora','Karl','Carla','Giorgio','Mizuki','Liv','Lotte','Ruben','Ewa','Jacek','Jan','Maja','Ricardo','Vitoria','Cristiano','Ines','Carmen','Maxim','Tatyana','Astrid','Filiz');
if(isset($_GET['voice']) && in_array(filter_input(INPUT_GET,'voice',FILTER_SANITIZE_STRING), $voices)){
 $voice = $_GET['voice'];
}else{
 $voice = 'Brian';
}

if(isset($_GET['name']) && !empty($_GET['name'])){
 $name = filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING);
}else{
 $name = 'there';
}
//Time
if(isset($_GET['time'])){


    date_default_timezone_set('America/Los_Angeles');
    $time = 'I presently reside on a server in Oregon, where it is currently '. date('g:i A') .'. ';
}else{
    $time = '';
}

//Weather
if(isset($_GET['weather'])){
    $weatherAPIkey ='APPID=1794446ecd9deb919b2575a760e4ce9a';
    $location = 'zip=97203';
    $url = 'http://api.openweathermap.org/data/2.5/weather?units=imperial&'.$location.'&'.$weatherAPIkey;
    $ch = curl_init($url);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json',));

    $response = json_decode(curl_exec($ch));
}
if(isset($response->weather) && !empty($response->weather[0])){
    $temp = $response->main->temp;
    $conditions = $response->weather[0]->description;
    $weather = 'Where I am, it is '.$temp.' degrees Farenheit and '.$conditions.'. ';
}else{
    $weather = '';
}




//Browser
if(isset($_GET['browser'])){
    $whichBrowser = new WhichBrowser\Parser(getallheaders());
    if(isset($whichBrowser) && !empty($whichBrowser)){
     $browser = 'It looks like you are using '.$whichBrowser->browser->name.' browser to visit this web page. ';
     if($whichBrowser->browser->name == 'Chrome'){
        $browser .= 'Excellent choice my friend. Of all the internet browsers, Chrome is my favorite. ';
     }elseif($whichBrowser->browser->name == 'Firefox'){
        $browser .= 'I find that use of Firefox generally indicates a distinguished internet browser. ';
     }
     elseif($whichBrowser->browser->name == 'Safari'){
        if($whichBrowser->device->manufacturer == 'Apple'){
        $browser .= 'Considering that you are on a '.$whichBrowser->device->model.', I would guess that you don\'t customize your web browser much, do you? Safari comes installed by default on Apple devices. ';
        }else{
            $browser .= 'I will never understand why some people use Safari. Especially people like you who are not even using an Apple device. ';
        }
     }elseif($whichBrowser->browser->name == 'Opera'){
        $browser .= 'That leads me to believe you are probably browsing on a VPN, because why else use Opera?';
     }
     $device = 'I also see that you are on a '.$whichBrowser->device->model.', manufactured by '.$whichBrowser->device->manufacturer.'. ';
     if($whichBrowser->device->manufacturer == 'Apple'){
        $device .= 'You must have a lot of cash lying around to afford one of those fancy machines.';
     }else{
        $device .= 'A sensible choice, for the sensible internet user. I applaud your selection. ';
     }
    }else{
     $browser = '';
     $device = '';
    }
}


//Message

$greeting = 'Hello ';

$message = $greeting.$name.', My name is '.$voice.'. ';
$message .= $time.$weather.$browser.$device;

//$message.= 'What you are hearing is actually a recording of my voice, saved as a file somewhere in the cloud';



$filename = date('g:i A').$name.$voice.filter_input(INPUT_GET,'browser',FILTER_SANITIZE_STRING);
$filename = str_replace(' ', '', $filename).'.mp3';
$filename = filter_var($filename, FILTER_SANITIZE_URL);
$pollyClient = $sdk->createPolly();

$result = $pollyClient->synthesizeSpeech([
    'OutputFormat' => 'mp3', // REQUIRED
    'Text' => $message, // REQUIRED
    'TextType' => 'text',
    'VoiceId' => $voice
]);

$mp3str = $result['AudioStream']->__toString();

$s3Client = $sdk->createS3();

$bucket = 'uploads.gabeshaughnessy.com';
$directory = 'mp3';
$filepath = $directory.'/'.$filename;
$putFile = $s3Client->putObject([
    'Bucket' => $bucket,
    'Key'    => $filepath,
    'Body'   => $mp3str
]);
$mp3url = $s3Client->getObjectUrl($bucket, $filepath);

echo '<video controls="" autoplay="" name="media"><source src="'.$mp3url.'" type="audio/mpeg"></video>';
?>
