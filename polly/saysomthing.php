<?php

require 'vendor/autoload.php';

$sharedConfig = [
    'region'  => 'us-west-2',
    'version' => 'latest'
];

$sdk = new Aws\Sdk($sharedConfig);

$voices = array('Geraint','Gwyneth','Mads','Naja','Hans','Marlene','Nicole','Russell','Amy','Brian','Emma','Raveena','Ivy','Joanna','Joey','Justin','Kendra','Kimberly','Salli','Conchita','Enrique','Miguel','Penelope','Chantal','Celine','Mathieu','Dora','Karl','Carla','Giorgio','Mizuki','Liv','Lotte','Ruben','Ewa','Jacek','Jan','Maja','Ricardo','Vitoria','Cristiano','Ines','Carmen','Maxim','Tatyana','Astrid','Filiz');
if(isset($_GET['voice']) && in_array(filter_input(INPUT_GET,'voice'), $voices)){
 $voice = $_GET['voice'];
}else{
 $voice = 'Russell';
}
$pollyClient = $sdk->createPolly();

$result = $pollyClient->synthesizeSpeech([
    'OutputFormat' => 'mp3', // REQUIRED
    'Text' => 'Hi there, this was created with the PHP SDK for AWS', // REQUIRED
    'TextType' => 'text',
    'VoiceId' => $voice
]);
$mp3 = $result['AudioStream']->__toString();
file_put_contents('sdk.mp3', $mp3);
echo '<video controls="" autoplay="" name="media"><source src="/sdk.mp3" type="audio/mpeg"></video>';
?>
