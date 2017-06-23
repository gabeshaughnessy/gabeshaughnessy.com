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
 $name = urldecode(filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING));
}else{
 $name = '';
}

if(isset($_GET['company']) && !empty($_GET['company'])){
 $company = urldecode(filter_input(INPUT_GET,'company',FILTER_SANITIZE_STRING));
}else{
 $company = '';
}

if(isset($_GET['industry']) && !empty($_GET['industry'])){
 $industry = urldecode(filter_input(INPUT_GET,'industry',FILTER_SANITIZE_STRING));
}else{
 $industry = '';
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

$messages = array(
"Hello ".$name.", My name is Jive. I hope you enjoy my personalized presentation.",
"As companies in the ".$industry." industry grow, so does the challenge of connection,  both internally with employees and externally with customers. As you are probably aware of at ".$company.", with everyone working in so many different places  using countless tools, it's hard to get business done.  People in information and systems, they're disconnected, fragmented.",
"That's where Jive comes in. I bring all the pieces together, giving people at ".$company." one place to connect, engage and do great things together. So, what is Jive? I am a secure collaboration hub that powers human connection for the world's leading businesses and organizations. My interactive intranet software is the single place for employees to collaborate, communicate, access content and stay informed.",
"This allows any place to be the workplace. Whether that means working with team members, keeping up with ".$company." news or quickly finding the people, information and documents to answer questions, even on the go using my mobile app. With Jive, getting work done across departments or time zones has never been easier, more efficient or more transparent. Decisions are made and projects are finished faster for your customers and your customer service team.",
"My customer community software provides one place for you to interact with your customers and prospects, and for them to interact with each other. It's where people can learn about your products and ".$industry." industry trends, receive answers quickly or share their own knowledge. I also let your team engage with customers in a way that feels personal and authentic.",
"As Jive, I not only help people work better together, but I support the ways they work, which means integrating the apps they love and enterprise tools they need. I bring them all together with one search that pulls information from formerly disconnected systems into one clear list of results. Documents, conversations or presentations can be found in one place, regardless of where they were created.", "Using my collaboration solution, employees are more productive and engaged. They're happier because their jobs are easier and they have a voice. Customers are happier too and that enthusiasm spreads. It's like having an army of brand advocates. All this, while bringing everyone closer. Jive, work better together.
"

    );
//$message .= $time.$weather.$browser.$device;

//$message.= 'What you are hearing is actually a recording of my voice, saved as a file somewhere in the cloud';



$filename = $name.$voice.$company.$industry;
$filename = str_replace(' ', '', $filename).'.mp3';
$filename = filter_var($filename, FILTER_SANITIZE_URL);
$pollyClient = $sdk->createPolly();
$mp3files = array();

for($i=0; $i < count($messages); $i++) {
    $messages[$i];
    $result = $pollyClient->synthesizeSpeech([
        'OutputFormat' => 'mp3', // REQUIRED
        'Text' => $messages[$i], // REQUIRED
        'TextType' => 'text',
        'VoiceId' => $voice
    ]);

    $mp3str = $result['AudioStream']->__toString();

    $s3Client = $sdk->createS3();

    $bucket = 'uploads.gabeshaughnessy.com';
    $directory = 'personal-transcipt';
    $filepath = $directory.'/'.$filename.$i;
    $putFile = $s3Client->putObject([
        'Bucket' => $bucket,
        'Key'    => $filepath,
        'Body'   => $mp3str
    ]);
    $mp3url = $s3Client->getObjectUrl($bucket, $filepath);
    $mp3files[$i] = $mp3url;
    echo '<audio style="display:none;" class="voiceover" id="voiceover'.$i.'"><source src="'.$mp3url.'" type="audio/mpeg"></audio>';
}
?>
<audio id="soundtrack"><source src="https://s3-us-west-2.amazonaws.com/uploads.gabeshaughnessy.com/soundtrack/oaklawn-dreams.mp3" type="audio/mpeg"></audio>

<script src="https://fast.wistia.com/embed/medias/99ig15zsqb.jsonp" async></script><script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
<script src="js/jquery-3.2.1.min.js"></script>
<script>
window._wq = window._wq || [];
_wq.push({ id: "99ig15zsqb", onReady: function(video) {
  var soundtrack = document.getElementById("soundtrack");
  video.volume(0);
  video.bind("play", function() {
    video.volume(0);
    });
  video.bind("crosstime", 0, function() {
    jQuery('audio').trigger('pause');
    soundtrack.volume = 0.5;
    soundtrack.play();
      jQuery('#voiceover0').trigger('play');
    });
  video.bind("crosstime", 6.5, function() {
      jQuery('.voiceover').trigger('pause');
      jQuery('#voiceover1').trigger('play');
    });
  video.bind("crosstime", 31, function() {
      jQuery('.voiceover').trigger('pause');
      jQuery('#voiceover2').trigger('play');
    });
  video.bind("crosstime", 60, function() {
      jQuery('.voiceover').trigger('pause');
      jQuery('#voiceover3').trigger('play');
    });
  video.bind("crosstime", 95, function() {
      jQuery('.voiceover').trigger('pause');
      jQuery('#voiceover4').trigger('play');
    });
  video.bind("crosstime", 120, function() {
      jQuery('.voiceover').trigger('pause');
      jQuery('#voiceover5').trigger('play');
    });
  video.bind("crosstime", 154, function() {
      jQuery('.voiceover').trigger('pause');
      jQuery('#voiceover6').trigger('play');
    });
  video.bind("pause", function() {
    video.volume(0);
    jQuery('audio').trigger('pause');
    });
}});
</script>

<div class="wistia_responsive_padding" style="padding:56.0% 0 0 0;position:relative;"><div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;"><div class="wistia_embed wistia_async_99ig15zsqb videoFoam=true" style="height:100%;width:100%">&nbsp;</div></div></div>
