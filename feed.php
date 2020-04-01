<?php
const PLX_ROOT = './';
const PLX_CORE = PLX_ROOT .'core/';

include PLX_ROOT.'config.php';
include PLX_CORE.'lib/config.php';

# On verifie que PluXml est installé
if(!file_exists(path('XMLFILE_PARAMETERS'))) {
	header('Location: '.PLX_ROOT.'install.php');
	exit;
}

# Autorise le cross-origin des flus rss/atom : Cross-Origin Resource Sharing
# https://enable-cors.org/server_php.html
# https://developer.mozilla.org/fr/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
header('Access-Control-Allow-Origin: *');

# On inclut les librairies nécessaires
const ALL_CLASSES = array(
	'date',
	'glob',
	'utils',
	'record',
	'motor',
	'feed',
	'plugins'
);
foreach(ALL_CLASSES as $aClass) {
	include PLX_CORE . 'lib/class.plx.' . $aClass . '.php';
}

# Creation de l'objet principal et lancement du traitement
$plxFeed = plxFeed::getInstance();

if(empty($plxFeed->aConf['enable_rss'])) {
	header('Location: index.php');
	exit;
}

# Détermination de la langue à utiliser (modifiable par le hook : FeedBegin)
$lang = $plxFeed->aConf['default_lang'];

eval($plxFeed->plxPlugins->callHook('FeedBegin')); # Hook Plugins

# Chargement du fichier de langue du core de PluXml
loadLang(PLX_CORE.'lang/'.$lang.'/core.php');

# On démarre la bufferisation
ob_start();
ob_implicit_flush(0);

$plxFeed->fprechauffage();
$plxFeed->fdemarrage();

# Récuperation de la bufférisation
$output = ob_get_clean();

eval($plxFeed->plxPlugins->callHook('FeedEnd')); # Hook Plugins

# Restitution écran
echo $output;
?>
